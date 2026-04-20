<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Modules\RolesPermissions\Support\PermissionName;
use App\Policies\Concerns\AuthorizesWithPermissions;
use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use AuthorizesWithPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::ROLES_VIEW);
    }

    public function view(User $user, Role $role): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::ROLES_VIEW);
    }

    public function create(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::ROLES_CREATE);
    }

    public function update(User $user, Role $role): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::ROLES_UPDATE);
    }

    public function delete(User $user, Role $role): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::ROLES_DELETE);
    }
}
