<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Modules\RolesPermissions\Support\PermissionName;
use App\Modules\RolesPermissions\Support\RoleName;
use App\Policies\Concerns\AuthorizesWithPermissions;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use AuthorizesWithPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::USERS_VIEW);
    }

    public function view(User $user, User $target): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::USERS_VIEW);
    }

    public function create(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::USERS_CREATE);
    }

    public function update(User $user, User $target): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::USERS_UPDATE);
    }

    public function delete(User $user, User $target): Response
    {
        if (! $user->can(PermissionName::USERS_DELETE)) {
            return Response::deny('Aksi tidak diizinkan.');
        }

        $isDeletingLastSuperAdmin = $target->hasRole(RoleName::SUPER_ADMIN)
            && User::query()->role(RoleName::SUPER_ADMIN)->count() <= 1;

        if ($isDeletingLastSuperAdmin) {
            return Response::deny('Super Admin terakhir tidak boleh dihapus.');
        }

        return Response::allow();
    }

    public function assignRole(User $user, ?User $target = null): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::USERS_ASSIGN_ROLE);
    }
}
