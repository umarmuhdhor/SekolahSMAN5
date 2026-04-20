<?php

declare(strict_types=1);

namespace App\Modules\ThemeSettings\Actions;

use App\Modules\ThemeSettings\Models\ThemeSetting;
use App\Modules\ThemeSettings\Support\ThemeDefaults;
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

        return [
            'logo_media_id' => $activeTheme->logo_media_id,
            'logo_url' => $this->resolveLogoUrl($activeTheme),
            'primary_color' => $activeTheme->resolvedPrimaryColor(),
            'secondary_color' => $activeTheme->resolvedSecondaryColor(),
            'accent_color' => $activeTheme->resolvedAccentColor(),
            'is_fallback' => ThemeDefaults::normalizeHex($activeTheme->primary_color) === null
                || ThemeDefaults::normalizeHex($activeTheme->secondary_color) === null
                || ThemeDefaults::normalizeHex($activeTheme->accent_color) === null
                || $activeTheme->logo_media_id === null,
        ];
    }

    private function resolveLogoUrl(ThemeSetting $themeSetting): string
    {
        $fallbackUrl = asset(ThemeDefaults::logoPath());
        $logoMedia = $themeSetting->logoMedia;

        if ($logoMedia === null) {
            return $fallbackUrl;
        }

        try {
            $disk = Storage::disk($logoMedia->disk);

            if (! $disk->exists($logoMedia->path)) {
                return $fallbackUrl;
            }

            return $disk->url($logoMedia->path);
        } catch (Throwable) {
            return $fallbackUrl;
        }
    }
}
