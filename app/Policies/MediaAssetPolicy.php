<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Modules\MediaLibrary\Models\MediaAsset;
use App\Modules\RolesPermissions\Support\PermissionName;
use App\Policies\Concerns\AuthorizesWithPermissions;
use Illuminate\Auth\Access\Response;

class MediaAssetPolicy
{
    use AuthorizesWithPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::MEDIA_VIEW);
    }

    public function view(User $user, MediaAsset $mediaAsset): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::MEDIA_VIEW);
    }

    public function create(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::MEDIA_CREATE);
    }

    public function update(User $user, MediaAsset $mediaAsset): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::MEDIA_UPDATE);
    }

    public function delete(User $user, MediaAsset $mediaAsset): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::MEDIA_DELETE);
    }
}
