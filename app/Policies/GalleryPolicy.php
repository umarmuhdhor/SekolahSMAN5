<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Modules\Galleries\Models\Gallery;
use App\Modules\RolesPermissions\Support\PermissionName;
use App\Policies\Concerns\AuthorizesWithPermissions;
use Illuminate\Auth\Access\Response;

class GalleryPolicy
{
    use AuthorizesWithPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::GALLERIES_VIEW);
    }

    public function view(User $user, Gallery $gallery): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::GALLERIES_VIEW);
    }

    public function create(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::GALLERIES_CREATE);
    }

    public function update(User $user, Gallery $gallery): Response
    {
        if ($user->can(PermissionName::GALLERIES_UPDATE_ANY)) {
            return Response::allow();
        }

        if ((int) $gallery->author_id === (int) $user->getKey() && $user->can(PermissionName::GALLERIES_UPDATE_OWN)) {
            return Response::allow();
        }

        return Response::deny('Aksi tidak diizinkan.');
    }

    public function delete(User $user, Gallery $gallery): Response
    {
        if ($user->can(PermissionName::GALLERIES_DELETE_ANY)) {
            return Response::allow();
        }

        if ((int) $gallery->author_id === (int) $user->getKey() && $user->can(PermissionName::GALLERIES_DELETE_OWN)) {
            return Response::allow();
        }

        return Response::deny('Aksi tidak diizinkan.');
    }

    public function publish(User $user, Gallery $gallery): Response
    {
        if (! $user->can(PermissionName::GALLERIES_PUBLISH)) {
            return Response::deny('Aksi tidak diizinkan.');
        }

        return $this->update($user, $gallery);
    }

    public function unpublish(User $user, Gallery $gallery): Response
    {
        if (! $user->can(PermissionName::GALLERIES_PUBLISH)) {
            return Response::deny('Aksi tidak diizinkan.');
        }

        return $this->update($user, $gallery);
    }
}
