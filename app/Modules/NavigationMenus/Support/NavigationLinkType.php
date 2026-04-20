<?php

declare(strict_types=1);

namespace App\Modules\NavigationMenus\Support;

final class NavigationLinkType
{
    public const URL = 'url';

    public const ROUTE = 'route';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::URL,
            self::ROUTE,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::URL => 'URL',
            self::ROUTE => 'Route Name',
        ];
    }
}
