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

    public const NAVIGATION_UPDATE = 'navigation_update';

    public const MEDIA_UPLOAD = 'media_upload';

    public const MEDIA_REPLACE = 'media_replace';

    public const MEDIA_DELETE = 'media_delete';

    public const USER_CREATED = 'user.created';

    public const USER_UPDATED = 'user.updated';

    public const USER_STATUS_CHANGED = 'user.status_changed';

    public const USER_DELETED = 'user.deleted';
}
