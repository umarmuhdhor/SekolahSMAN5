<?php

declare(strict_types=1);

namespace App\Modules\AuditLogs\Support;

final class AuditEventType
{
    public const LOGIN = 'login';

    public const LOGOUT = 'logout';

    public const FAILED_LOGIN = 'failed_login';

    public const CREATE = 'create';

    public const UPDATE = 'update';

    public const DELETE = 'delete';

    public const PUBLISH = 'publish';

    public const UNPUBLISH = 'unpublish';

    public const ROLE_CHANGE = 'role_change';

    public const PERMISSION_CHANGE = 'permission_change';

    public const SCHOOL_PROFILE_UPDATE = 'school_profile_update';

    public const THEME_UPDATE = 'theme_update';

    public const THEME_UPDATED = 'theme.updated';

    public const THEME_LOGO_CHANGED = 'theme.logo_changed';

    public const NAVIGATION_UPDATE = 'navigation_update';

    public const MEDIA_UPLOAD = 'media_upload';

    public const MEDIA_REPLACE = 'media_replace';

    public const MEDIA_DELETE = 'media_delete';

    public const USER_CREATED = 'user.created';

    public const USER_UPDATED = 'user.updated';

    public const USER_STATUS_CHANGED = 'user.status_changed';

    public const USER_DELETED = 'user.deleted';

    public const NEWS_CREATED = 'news.created';

    public const NEWS_UPDATED = 'news.updated';

    public const NEWS_DELETED = 'news.deleted';

    public const NEWS_PUBLISHED = 'news.published';

    public const NEWS_UNPUBLISHED = 'news.unpublished';

    public const ANNOUNCEMENT_CREATED = 'announcement.created';

    public const ANNOUNCEMENT_UPDATED = 'announcement.updated';

    public const ANNOUNCEMENT_DELETED = 'announcement.deleted';

    public const ANNOUNCEMENT_PUBLISHED = 'announcement.published';

    public const ANNOUNCEMENT_UNPUBLISHED = 'announcement.unpublished';

    public const GALLERY_CREATED = 'gallery.created';

    public const GALLERY_UPDATED = 'gallery.updated';

    public const GALLERY_DELETED = 'gallery.deleted';

    public const GALLERY_PUBLISHED = 'gallery.published';

    public const GALLERY_UNPUBLISHED = 'gallery.unpublished';

    public const GALLERY_ITEM_ADDED = 'gallery.item_added';

    public const GALLERY_ITEM_REMOVED = 'gallery.item_removed';

    public const GALLERY_ITEMS_REORDERED = 'gallery.items_reordered';
}
