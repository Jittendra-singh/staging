<?php

namespace App\Jobs;

use App\Services\TranslationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TranslateContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        protected string $modelClass,
        protected string|int $modelKey,
        protected string $field,
        protected string $locale,
        protected string $text
    ) {}

    public function handle(TranslationService $service): void
    {
        if ($service->isCircuitBroken()) {
            $this->release(300);
            return;
        }

        try {
            $model = $this->resolveModel();
            if (!$model) {
                return;
            }

            $translation = $service->translateText($this->text, $this->locale);
            if ($translation) {
                $service->rememberModelTranslation($model, $this->field, $this->locale, $translation);
            }
        } catch (\Throwable $e) {
            if ($this->shouldTripCircuitBreaker($e)) {
                $service->tripCircuitBreaker();
            }

            Log::warning('Auto translation failed.', [
                'model' => $this->modelClass,
                'model_key' => $this->modelKey,
                'field' => $this->field,
                'locale' => $this->locale,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function resolveModel(): ?Model
    {
        if (!is_subclass_of($this->modelClass, Model::class)) {
            return null;
        }

        return $this->modelClass::query()->find($this->modelKey);
    }

    protected function shouldTripCircuitBreaker(\Throwable $e): bool
    {
        $message = $e->getMessage();

        return str_contains($message, '429')
            || str_contains($message, '500')
            || str_contains($message, '502')
            || str_contains($message, '503')
            || str_contains($message, '504');
    }
}
