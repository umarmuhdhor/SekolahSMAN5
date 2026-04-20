<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Modules\Announcements\Models\Announcement;
use App\Modules\RolesPermissions\Support\PermissionName;
use App\Policies\Concerns\AuthorizesWithPermissions;
use Illuminate\Auth\Access\Response;

class AnnouncementPolicy
{
    use AuthorizesWithPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::ANNOUNCEMENTS_VIEW);
    }

    public function view(User $user, Announcement $announcement): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::ANNOUNCEMENTS_VIEW);
    }

    public function create(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::ANNOUNCEMENTS_CREATE);
    }

    public function update(User $user, Announcement $announcement): Response
    {
        if ($user->can(PermissionName::ANNOUNCEMENTS_UPDATE_ANY)) {
            return Response::allow();
        }

        if ((int) $announcement->author_id === (int) $user->getKey() && $user->can(PermissionName::ANNOUNCEMENTS_UPDATE_OWN)) {
            return Response::allow();
        }

        return Response::deny('Aksi tidak diizinkan.');
    }

    public function delete(User $user, Announcement $announcement): Response
    {
        if ($user->can(PermissionName::ANNOUNCEMENTS_DELETE_ANY)) {
            return Response::allow();
        }

        if ((int) $announcement->author_id === (int) $user->getKey() && $user->can(PermissionName::ANNOUNCEMENTS_DELETE_OWN)) {
            return Response::allow();
        }

        return Response::deny('Aksi tidak diizinkan.');
    }

    public function publish(User $user, Announcement $announcement): Response
    {
        if (! $user->can(PermissionName::ANNOUNCEMENTS_PUBLISH)) {
            return Response::deny('Aksi tidak diizinkan.');
        }

        return $this->update($user, $announcement);
    }

    public function unpublish(User $user, Announcement $announcement): Response
    {
        if (! $user->can(PermissionName::ANNOUNCEMENTS_PUBLISH)) {
            return Response::deny('Aksi tidak diizinkan.');
        }

        return $this->update($user, $announcement);
    }
}
