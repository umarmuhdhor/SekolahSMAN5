<?php

declare(strict_types=1);

namespace App\Modules\NavigationMenus\Support;

final class NavigationMenuLocation
{
    public const HEADER = 'header';

    public const FOOTER = 'footer';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::HEADER,
            self::FOOTER,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::HEADER => 'Header',
            self::FOOTER => 'Footer',
        ];
    }
}
