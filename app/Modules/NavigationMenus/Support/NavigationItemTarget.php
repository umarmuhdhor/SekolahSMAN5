<?php

declare(strict_types=1);

namespace App\Modules\NavigationMenus\Support;

final class NavigationItemTarget
{
    public const SELF = '_self';

    public const BLANK = '_blank';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::SELF,
            self::BLANK,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::SELF => 'Tab Saat Ini',
            self::BLANK => 'Tab Baru',
        ];
    }
}
