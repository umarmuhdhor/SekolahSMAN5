<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Modules\NavigationMenus\Models\NavigationItem;
use App\Modules\RolesPermissions\Support\PermissionName;
use App\Policies\Concerns\AuthorizesWithPermissions;
use Illuminate\Auth\Access\Response;

class NavigationItemPolicy
{
    use AuthorizesWithPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::NAVIGATION_VIEW);
    }

    public function view(User $user, NavigationItem $navigationItem): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::NAVIGATION_VIEW);
    }

    public function create(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::NAVIGATION_UPDATE);
    }

    public function update(User $user, NavigationItem $navigationItem): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::NAVIGATION_UPDATE);
    }

    public function delete(User $user, NavigationItem $navigationItem): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::NAVIGATION_UPDATE);
    }
}
