<?php

namespace App\Services;

use App\Jobs\TranslateContentJob;
use App\Models\ContentTranslation;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TranslationService
{
    protected array $buffer = [];

    public function preload($models, array $fields): void
    {
        $locale = App::getLocale();
        if ($locale === $this->defaultLocale()) {
            return;
        }

        $keys = [];
        foreach ($this->prepareModelList($models) as $model) {
            if (!$model instanceof Model) {
                continue;
            }

            foreach ($fields as $field) {
                $keys[] = $this->cacheKey($model, $field, $locale);
            }
        }

        if ($keys) {
            $this->buffer = array_merge($this->buffer, Cache::many($keys));
        }
    }

    public function translate(Model $model, string $field): string
    {
        $locale = App::getLocale();
        $translations = $this->translationsFor($model, $field);
        $fallback = $this->fallbackText($translations);

        if ($locale === $this->defaultLocale()) {
            return $fallback;
        }

        if (!empty($translations[$locale])) {
            return (string) $translations[$locale];
        }

        $cacheKey = $this->cacheKey($model, $field, $locale);
        if (array_key_exists($cacheKey, $this->buffer) && $this->buffer[$cacheKey] !== null) {
            return (string) $this->buffer[$cacheKey];
        }

        if ($cached = Cache::get($cacheKey)) {
            return (string) $cached;
        }

        if ($stored = $this->storedModelTranslation($model, $field, $locale, $fallback)) {
            $this->remember($cacheKey, $stored);

            return $stored;
        }

        $this->queueMissingTranslation($model, $field, $locale, $fallback);

        return $fallback;
    }

    public function translateString(string $text, ?string $locale = null): string
    {
        $locale ??= App::getLocale();
        if ($text === '' || $locale === $this->defaultLocale()) {
            return $text;
        }

        return (string) Cache::get($this->stringCacheKey($text, $locale), $text);
    }

    public function translateText(string $text, string $locale): ?string
    {
        if ($text === '' || $locale === $this->defaultLocale() || !$this->autoTranslateEnabled()) {
            return null;
        }

        if ($this->isCircuitBroken()) {
            return null;
        }

        return match (config('translations.provider', 'none')) {
            'google_cloud' => $this->translateWithGoogleCloud($text, $locale),
            'libretranslate' => $this->translateWithLibreTranslate($text, $locale),
            default => null,
        };
    }

    public function rememberModelTranslation(Model $model, string $field, string $locale, string $translation): void
    {
        $this->remember($this->cacheKey($model, $field, $locale), $translation);
        $this->persistModelTranslation($model, $field, $locale, $translation);
    }

    public function rememberStringTranslation(string $text, string $locale, string $translation): void
    {
        $this->remember($this->stringCacheKey($text, $locale), $translation);
    }

    public function translationsFor(Model $model, string $field): array
    {
        return $this->normalizeTranslations($model->getRawOriginal($field));
    }

    public function cacheKey(Model $model, string $field, string $locale): string
    {
        $version = $model->updated_at ? $model->updated_at->timestamp : 'v1';
        return "trans_{$locale}_{$model->getTable()}_{$model->getKey()}_{$field}_{$version}";
    }

    public function isCircuitBroken(): bool
    {
        return Cache::has('translation_circuit_breaker');
    }

    public function tripCircuitBreaker(): void
    {
        Cache::put(
            'translation_circuit_breaker',
            true,
            now()->addMinutes((int) config('translations.circuit_breaker_minutes', 15))
        );
    }

    protected function queueMissingTranslation(Model $model, string $field, string $locale, string $text): void
    {
        if ($text === '' || !$this->autoTranslateEnabled() || $this->isCircuitBroken()) {
            return;
        }

        $pendingKey = 'pending_' . $this->cacheKey($model, $field, $locale);
        if (Cache::has($pendingKey)) {
            return;
        }

        Cache::put($pendingKey, true, now()->addMinutes(10));

        TranslateContentJob::dispatch(
            $model::class,
            $model->getKey(),
            $field,
            $locale,
            $text
        )->onQueue(config('translations.queue_name', 'default'));
    }

    protected function translateWithGoogleCloud(string $text, string $locale): ?string
    {
        $key = config('translations.providers.google_cloud.key');
        if (!$key) {
            return null;
        }

        $response = $this->httpClient()->post(config('translations.providers.google_cloud.endpoint'), [
            'query' => ['key' => $key],
            'json' => [
                'q' => $text,
                'target' => $locale,
                'format' => 'text',
                'source' => $this->defaultLocale(),
            ],
        ]);

        $data = json_decode((string) $response->getBody(), true);

        return $data['data']['translations'][0]['translatedText'] ?? null;
    }

    protected function translateWithLibreTranslate(string $text, string $locale): ?string
    {
        $endpoint = config('translations.providers.libretranslate.endpoint');
        if (!$endpoint) {
            return null;
        }

        $payload = [
            'q' => $text,
            'source' => $this->defaultLocale(),
            'target' => $locale,
            'format' => 'text',
        ];

        if ($key = config('translations.providers.libretranslate.key')) {
            $payload['api_key'] = $key;
        }

        $response = $this->httpClient()->post($endpoint, ['json' => $payload]);
        $data = json_decode((string) $response->getBody(), true);

        return $data['translatedText'] ?? null;
    }

    protected function normalizeTranslations($raw): array
    {
        if (is_array($raw)) {
            return $this->cleanTranslations($raw);
        }

        if ($raw === null || $raw === '') {
            return [];
        }

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $this->cleanTranslations($decoded);
            }

            return [$this->defaultLocale() => $raw];
        }

        return [$this->defaultLocale() => (string) $raw];
    }

    protected function cleanTranslations(array $translations): array
    {
        $clean = [];
        foreach ($translations as $locale => $value) {
            if (is_string($locale) && is_scalar($value) && $value !== '') {
                $clean[$locale] = (string) $value;
            }
        }

        return $clean;
    }

    protected function fallbackText(array $translations): string
    {
        $default = $this->defaultLocale();
        if (!empty($translations[$default])) {
            return (string) $translations[$default];
        }

        if (!empty($translations['en'])) {
            return (string) $translations['en'];
        }

        foreach ($translations as $value) {
            if ($value !== '') {
                return (string) $value;
            }
        }

        return '';
    }

    protected function remember(string $key, string $translation): void
    {
        $ttl = config('translations.cache_ttl');
        $ttl ? Cache::put($key, $translation, now()->addSeconds((int) $ttl)) : Cache::forever($key, $translation);
    }

    protected function storedModelTranslation(Model $model, string $field, string $locale, string $text): ?string
    {
        if (!$this->translationStoreEnabled() || $text === '') {
            return null;
        }

        try {
            return ContentTranslation::query()
                ->where('model_type', $model::class)
                ->where('model_id', (string) $model->getKey())
                ->where('field', $field)
                ->where('locale', $locale)
                ->where('source_hash', $this->sourceHash($text))
                ->value('translated_text');
        } catch (\Throwable $e) {
            Log::warning('Stored translation lookup failed.', [
                'model' => $model::class,
                'model_key' => $model->getKey(),
                'field' => $field,
                'locale' => $locale,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    protected function persistModelTranslation(Model $model, string $field, string $locale, string $translation): void
    {
        $translations = $this->translationsFor($model, $field);
        $source = $this->fallbackText($translations);

        if (!$this->translationStoreEnabled() || $source === '' || $translation === '') {
            return;
        }

        try {
            ContentTranslation::query()->updateOrCreate(
                [
                    'model_type' => $model::class,
                    'model_id' => (string) $model->getKey(),
                    'field' => $field,
                    'locale' => $locale,
                    'source_hash' => $this->sourceHash($source),
                ],
                [
                    'source_text' => $source,
                    'translated_text' => $translation,
                    'provider' => config('translations.provider', 'none'),
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('Stored translation persistence failed.', [
                'model' => $model::class,
                'model_key' => $model->getKey(),
                'field' => $field,
                'locale' => $locale,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function sourceHash(string $text): string
    {
        return hash('sha256', $text);
    }

    protected function translationStoreEnabled(): bool
    {
        return (bool) config('translations.database_store', true)
            && Schema::hasTable('content_translations');
    }

    protected function stringCacheKey(string $text, string $locale): string
    {
        return 'trans_string_' . $locale . '_' . md5($text);
    }

    protected function autoTranslateEnabled(): bool
    {
        return (bool) config('translations.auto_translate', false);
    }

    protected function defaultLocale(): string
    {
        return (string) config('translations.default_locale', config('app.locale', 'en'));
    }

    protected function prepareModelList($models): array
    {
        if ($models instanceof \Illuminate\Pagination\LengthAwarePaginator || $models instanceof \Illuminate\Support\Collection) {
            return $models->all();
        }

        return is_array($models) ? $models : [$models];
    }

    protected function httpClient(): Client
    {
        return new Client([
            'timeout' => (float) config('translations.http.timeout', 10),
            'verify' => (bool) config('translations.http.verify', true),
        ]);
    }
}
