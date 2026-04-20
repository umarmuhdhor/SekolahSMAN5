<?php

declare(strict_types=1);

namespace App\Modules\RolesPermissions\Support;

final class PermissionName
{
    public const DASHBOARD_VIEW = 'dashboard.view';

    public const USERS_VIEW = 'users.view';

    public const USERS_CREATE = 'users.create';

    public const USERS_UPDATE = 'users.update';

    public const USERS_DELETE = 'users.delete';

    public const USERS_ASSIGN_ROLE = 'users.assign_role';

    public const ROLES_VIEW = 'roles.view';

    public const ROLES_CREATE = 'roles.create';

    public const ROLES_UPDATE = 'roles.update';

    public const ROLES_DELETE = 'roles.delete';

    public const PERMISSIONS_VIEW = 'permissions.view';

    public const PERMISSIONS_CREATE = 'permissions.create';

    public const PERMISSIONS_UPDATE = 'permissions.update';

    public const PERMISSIONS_DELETE = 'permissions.delete';

    public const NEWS_VIEW = 'news.view';

    public const NEWS_CREATE = 'news.create';

    public const NEWS_UPDATE_OWN = 'news.update_own';

    public const NEWS_UPDATE_ANY = 'news.update_any';

    public const NEWS_DELETE_OWN = 'news.delete_own';

    public const NEWS_DELETE_ANY = 'news.delete_any';

    public const NEWS_PUBLISH = 'news.publish';

    public const ANNOUNCEMENTS_VIEW = 'announcements.view';

    public const ANNOUNCEMENTS_CREATE = 'announcements.create';

    public const ANNOUNCEMENTS_UPDATE_OWN = 'announcements.update_own';

    public const ANNOUNCEMENTS_UPDATE_ANY = 'announcements.update_any';

    public const ANNOUNCEMENTS_DELETE_OWN = 'announcements.delete_own';

    public const ANNOUNCEMENTS_DELETE_ANY = 'announcements.delete_any';

    public const ANNOUNCEMENTS_PUBLISH = 'announcements.publish';

    public const GALLERIES_VIEW = 'galleries.view';

    public const GALLERIES_CREATE = 'galleries.create';

    public const GALLERIES_UPDATE_OWN = 'galleries.update_own';

    public const GALLERIES_UPDATE_ANY = 'galleries.update_any';

    public const GALLERIES_DELETE_OWN = 'galleries.delete_own';

    public const GALLERIES_DELETE_ANY = 'galleries.delete_any';

    public const GALLERIES_PUBLISH = 'galleries.publish';

    public const MEDIA_VIEW = 'media.view';

    public const MEDIA_CREATE = 'media.create';

    public const MEDIA_UPDATE = 'media.update';

    public const MEDIA_DELETE = 'media.delete';

    public const SCHOOL_PROFILE_VIEW = 'school_profile.view';

    public const SCHOOL_PROFILE_UPDATE = 'school_profile.update';

    public const THEME_VIEW = 'theme.view';

    public const THEME_UPDATE = 'theme.update';

    public const NAVIGATION_VIEW = 'navigation.view';

    public const NAVIGATION_UPDATE = 'navigation.update';

    public const AUDIT_VIEW = 'audit.view';

    /**
     * Daftar seluruh permission baseline lintas modul CMS.
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::DASHBOARD_VIEW,
            self::USERS_VIEW,
            self::USERS_CREATE,
            self::USERS_UPDATE,
            self::USERS_DELETE,
            self::USERS_ASSIGN_ROLE,
            self::ROLES_VIEW,
            self::ROLES_CREATE,
            self::ROLES_UPDATE,
            self::ROLES_DELETE,
            self::PERMISSIONS_VIEW,
            self::PERMISSIONS_CREATE,
            self::PERMISSIONS_UPDATE,
            self::PERMISSIONS_DELETE,
            self::NEWS_VIEW,
            self::NEWS_CREATE,
            self::NEWS_UPDATE_OWN,
            self::NEWS_UPDATE_ANY,
            self::NEWS_DELETE_OWN,
            self::NEWS_DELETE_ANY,
            self::NEWS_PUBLISH,
            self::ANNOUNCEMENTS_VIEW,
            self::ANNOUNCEMENTS_CREATE,
            self::ANNOUNCEMENTS_UPDATE_OWN,
            self::ANNOUNCEMENTS_UPDATE_ANY,
            self::ANNOUNCEMENTS_DELETE_OWN,
            self::ANNOUNCEMENTS_DELETE_ANY,
            self::ANNOUNCEMENTS_PUBLISH,
            self::GALLERIES_VIEW,
            self::GALLERIES_CREATE,
            self::GALLERIES_UPDATE_OWN,
            self::GALLERIES_UPDATE_ANY,
            self::GALLERIES_DELETE_OWN,
            self::GALLERIES_DELETE_ANY,
            self::GALLERIES_PUBLISH,
            self::MEDIA_VIEW,
            self::MEDIA_CREATE,
            self::MEDIA_UPDATE,
            self::MEDIA_DELETE,
            self::SCHOOL_PROFILE_VIEW,
            self::SCHOOL_PROFILE_UPDATE,
            self::THEME_VIEW,
            self::THEME_UPDATE,
            self::NAVIGATION_VIEW,
            self::NAVIGATION_UPDATE,
            self::AUDIT_VIEW,
        ];
    }

    /**
     * Permission default role Guru sesuai batas operasional awal CMS.
     *
     * @return array<int, string>
     */
    public static function guruDefaults(): array
    {
        return [
            self::DASHBOARD_VIEW,
            self::NEWS_VIEW,
            self::NEWS_CREATE,
            self::NEWS_UPDATE_OWN,
            self::NEWS_DELETE_OWN,
            self::ANNOUNCEMENTS_VIEW,
            self::ANNOUNCEMENTS_CREATE,
            self::ANNOUNCEMENTS_UPDATE_OWN,
            self::ANNOUNCEMENTS_DELETE_OWN,
            self::GALLERIES_VIEW,
            self::GALLERIES_CREATE,
            self::GALLERIES_UPDATE_OWN,
            self::GALLERIES_DELETE_OWN,
            self::MEDIA_VIEW,
            self::MEDIA_CREATE,
            self::MEDIA_UPDATE,
            self::MEDIA_DELETE,
        ];
    }
}
