<?php

declare(strict_types=1);

namespace App\Modules\ThemeSettings\Support;

final class ThemeDefaults
{
    public const DEFAULT_COLOR_PRIMARY = '#1D4ED8';

    public const DEFAULT_COLOR_SECONDARY = '#0F766E';

    public const DEFAULT_COLOR_ACCENT = '#F59E0B';

    /**
     * @return array{primary: string, secondary: string, accent: string}
     */
    public static function palette(): array
    {
        return [
            'primary' => self::normalizeHex((string) config('theme_settings.default_palette.primary', self::DEFAULT_COLOR_PRIMARY))
                ?? self::DEFAULT_COLOR_PRIMARY,
            'secondary' => self::normalizeHex((string) config('theme_settings.default_palette.secondary', self::DEFAULT_COLOR_SECONDARY))
                ?? self::DEFAULT_COLOR_SECONDARY,
            'accent' => self::normalizeHex((string) config('theme_settings.default_palette.accent', self::DEFAULT_COLOR_ACCENT))
                ?? self::DEFAULT_COLOR_ACCENT,
        ];
    }

    public static function logoPath(): string
    {
        $defaultPath = (string) config('theme_settings.default_logo_path', 'assets/theme/default-school-logo.svg');

        return trim($defaultPath) !== '' ? $defaultPath : 'assets/theme/default-school-logo.svg';
    }

    public static function colorOrDefault(?string $candidate, string $type): string
    {
        $palette = self::palette();

        return self::normalizeHex($candidate) ?? ($palette[$type] ?? self::DEFAULT_COLOR_PRIMARY);
    }

    public static function normalizeHex(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = strtoupper(trim($value));

        if ($normalized === '') {
            return null;
        }

        if (! str_starts_with($normalized, '#')) {
            $normalized = '#'.$normalized;
        }

        return preg_match('/^#[0-9A-F]{6}$/', $normalized) === 1
            ? $normalized
            : null;
    }
}
