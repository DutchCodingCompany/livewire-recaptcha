<?php

namespace DutchCodingCompany\LivewireRecaptcha;

use Illuminate\Support\Facades\Blade;
use RuntimeException;

class LivewireRecaptcha
{
    /**
     * @param string|null $siteKey
     * @param 'v2'|'v3'|null $version
     * @param 'normal'|'compact'|'invisible'|null $size
     * @param 'light'|'dark'|null $theme
     * @return string
     */
    public static function directive(
        string $version = null,
        string $siteKey = null,
        string $theme = null,
        string $size = null,
    ): string {
        $version ??= config('services.google.recaptcha.version') ?? 'v3';

        return Blade::render(file_get_contents($path = __DIR__."/directive.recaptcha.$version.blade.php") ?: throw new RuntimeException("Failed to load '$path'."),
            [
                'siteKey' => $siteKey ?? config('services.google.recaptcha.site_key'),
                'theme' => $theme ?? config('services.google.recaptcha.theme') ?? 'light',
                'size' => $size ?? config('services.google.recaptcha.size') ?? 'normal',
            ]);
    }
}
