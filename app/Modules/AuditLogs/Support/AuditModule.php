<?php

declare(strict_types=1);

namespace App\Modules\AuditLogs\Support;

final class AuditModule
{
    public const AUTH = 'auth';

    public const USERS = 'users';

    public const ROLES_PERMISSIONS = 'roles_permissions';

    public const NEWS = 'news';

    public const ANNOUNCEMENTS = 'announcements';

    public const GALLERIES = 'galleries';

    public const MEDIA_LIBRARY = 'media_library';

    public const SCHOOL_PROFILE = 'school_profile';

    public const THEME = 'theme';

    public const NAVIGATION = 'navigation';

    public const SYSTEM = 'system';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::AUTH,
            self::USERS,
            self::ROLES_PERMISSIONS,
            self::NEWS,
            self::ANNOUNCEMENTS,
            self::GALLERIES,
            self::MEDIA_LIBRARY,
            self::SCHOOL_PROFILE,
            self::THEME,
            self::NAVIGATION,
            self::SYSTEM,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::AUTH => 'Auth',
            self::USERS => 'Users',
            self::ROLES_PERMISSIONS => 'Roles & Permissions',
            self::NEWS => 'News',
            self::ANNOUNCEMENTS => 'Announcements',
            self::GALLERIES => 'Galleries',
            self::MEDIA_LIBRARY => 'Media Library',
            self::SCHOOL_PROFILE => 'School Profile',
            self::THEME => 'Theme',
            self::NAVIGATION => 'Navigation',
            self::SYSTEM => 'System',
        ];
    }
}
