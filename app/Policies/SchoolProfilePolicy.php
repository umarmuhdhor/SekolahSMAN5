<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Modules\RolesPermissions\Support\PermissionName;
use App\Modules\SchoolProfile\Models\SchoolProfile;
use App\Policies\Concerns\AuthorizesWithPermissions;
use Illuminate\Auth\Access\Response;

class SchoolProfilePolicy
{
    use AuthorizesWithPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::SCHOOL_PROFILE_VIEW);
    }

    public function view(User $user, SchoolProfile $schoolProfile): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::SCHOOL_PROFILE_VIEW);
    }

    public function create(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::SCHOOL_PROFILE_UPDATE);
    }

    public function update(User $user, SchoolProfile $schoolProfile): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::SCHOOL_PROFILE_UPDATE);
    }
}
