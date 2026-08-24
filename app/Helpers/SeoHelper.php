<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Request;

class SeoHelper
{
    /**
     * Generate HTML for hreflang tags to inform search engines of localized versions.
     */
    public static function generateHrefLangTags(): string
    {
        $locales = ['en', 'es', 'ht'];
        $currentPath = Request::path();
        
        // Remove existing locale from path if present (e.g., /es/events -> events)
        $segments = Request::segments();
        if (in_array($segments[0] ?? '', $locales)) {
            array_shift($segments);
        }
        $cleanPath = implode('/', $segments);

        $html = "";
        foreach ($locales as $locale) {
            $url = url($locale . '/' . $cleanPath);
            $html .= "<link rel=\"alternate\" hreflang=\"{$locale}\" href=\"{$url}\" />\n";
        }
        
        // x-default for the default language (English)
        $html .= "<link rel=\"alternate\" hreflang=\"x-default\" href=\"" . url('en/' . $cleanPath) . "\" />\n";

        return $html;
    }
}
