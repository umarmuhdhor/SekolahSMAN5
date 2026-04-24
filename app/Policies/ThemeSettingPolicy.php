<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Modules\RolesPermissions\Support\PermissionName;
use App\Modules\ThemeSettings\Models\ThemeSetting;
use App\Policies\Concerns\AuthorizesWithPermissions;
use Illuminate\Auth\Access\Response;

class ThemeSettingPolicy
{
    use AuthorizesWithPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::THEME_VIEW);
    }

    public function view(User $user, ThemeSetting $themeSetting): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::THEME_VIEW);
    }

    public function create(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::THEME_UPDATE);
    }

    public function update(User $user, ThemeSetting $themeSetting): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::THEME_UPDATE);
    }
}
