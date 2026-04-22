<?php

declare(strict_types=1);

namespace App\Modules\ThemeSettings\Actions;

use App\Modules\ThemeSettings\Models\ThemeSetting;
use App\Modules\ThemeSettings\Support\ThemeDefaults;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ResolveActiveThemeSettingsAction
{
    /**
     * @return array{
     *     logo_media_id: int|null,
     *     logo_url: string,
     *     primary_color: string,
     *     secondary_color: string,
     *     accent_color: string,
     *     is_fallback: bool
     * }
     */
    public function execute(): array
    {
        if (! Schema::hasTable('theme_settings')) {
            return [
                'logo_media_id' => null,
                'logo_url' => asset(ThemeDefaults::logoPath()),
                'primary_color' => ThemeDefaults::palette()['primary'],
                'secondary_color' => ThemeDefaults::palette()['secondary'],
                'accent_color' => ThemeDefaults::palette()['accent'],
                'is_fallback' => true,
            ];
        }

        $activeTheme = ThemeSetting::query()
            ->with('logoMedia')
            ->active()
            ->orderByDesc('updated_at')
            ->first();

        if ($activeTheme === null) {
            return [
                'logo_media_id' => null,
                'logo_url' => asset(ThemeDefaults::logoPath()),
                'primary_color' => ThemeDefaults::palette()['primary'],
                'secondary_color' => ThemeDefaults::palette()['secondary'],
                'accent_color' => ThemeDefaults::palette()['accent'],
                'is_fallback' => true,
            ];
        }

        $resolvedLogo = $this->resolveLogo($activeTheme);

        return [
            'logo_media_id' => $activeTheme->logo_media_id,
            'logo_url' => $resolvedLogo['url'],
            'primary_color' => $activeTheme->resolvedPrimaryColor(),
            'secondary_color' => $activeTheme->resolvedSecondaryColor(),
            'accent_color' => $activeTheme->resolvedAccentColor(),
            'is_fallback' => ThemeDefaults::normalizeHex($activeTheme->primary_color) === null
                || ThemeDefaults::normalizeHex($activeTheme->secondary_color) === null
                || ThemeDefaults::normalizeHex($activeTheme->accent_color) === null
                || $resolvedLogo['is_fallback'],
        ];
    }

    /**
     * @return array{url: string, is_fallback: bool}
     */
    private function resolveLogo(ThemeSetting $themeSetting): array
    {
        $fallbackUrl = asset(ThemeDefaults::logoPath());
        $logoMedia = $themeSetting->logoMedia;

        if ($logoMedia === null) {
            return [
                'url' => $fallbackUrl,
                'is_fallback' => true,
            ];
        }

        try {
            $disk = Storage::disk($logoMedia->disk);

            if (! $disk->exists($logoMedia->path)) {
                return [
                    'url' => $fallbackUrl,
                    'is_fallback' => true,
                ];
            }

            return [
                'url' => $disk->url($logoMedia->path),
                'is_fallback' => false,
            ];
        } catch (Throwable) {
            return [
                'url' => $fallbackUrl,
                'is_fallback' => true,
            ];
        }
    }
}
