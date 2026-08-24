<?php

namespace App\Helpers;

use App\Services\TranslationService;

class TranslationHelper
{
    public static function translate($text): string
    {
        if ($text === null || $text === '') {
            return (string) $text;
        }

        return app(TranslationService::class)->translateString((string) $text);
    }
}
