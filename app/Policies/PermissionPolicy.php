<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Modules\RolesPermissions\Support\PermissionName;
use App\Policies\Concerns\AuthorizesWithPermissions;
use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Permission;

class PermissionPolicy
{
    use AuthorizesWithPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::PERMISSIONS_VIEW);
    }

    public function view(User $user, Permission $permission): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::PERMISSIONS_VIEW);
    }

    public function create(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::PERMISSIONS_CREATE);
    }

    public function update(User $user, Permission $permission): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::PERMISSIONS_UPDATE);
    }

    public function delete(User $user, Permission $permission): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::PERMISSIONS_DELETE);
    }
}
