<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Modules\AuditLogs\Models\AuditLog;
use App\Modules\RolesPermissions\Support\PermissionName;
use App\Policies\Concerns\AuthorizesWithPermissions;
use Illuminate\Auth\Access\Response;

class AuditLogPolicy
{
    use AuthorizesWithPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::AUDIT_VIEW);
    }

    public function view(User $user, AuditLog $auditLog): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::AUDIT_VIEW);
    }
}
