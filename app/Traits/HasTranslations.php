<?php

namespace App\Traits;

use App\Services\TranslationService;

trait HasTranslations
{
    public function t(string $field): string
    {
        return app(TranslationService::class)->translate($this, $field);
    }

    public function getTranslatableFields(): array
    {
        return property_exists($this, 'translatable') ? $this->translatable : [];
    }

    public function __get($key)
    {
        if (str_starts_with($key, 'translated_')) {
            $field = substr($key, strlen('translated_'));

            if (in_array($field, $this->getTranslatableFields(), true) || array_key_exists($field, $this->attributes)) {
                return $this->t($field);
            }
        }

        return parent::__get($key);
    }
}
