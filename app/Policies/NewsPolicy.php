<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Modules\News\Models\News;
use App\Modules\RolesPermissions\Support\PermissionName;
use App\Policies\Concerns\AuthorizesWithPermissions;
use Illuminate\Auth\Access\Response;

class NewsPolicy
{
    use AuthorizesWithPermissions;

    public function viewAny(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::NEWS_VIEW);
    }

    public function view(User $user, News $news): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::NEWS_VIEW);
    }

    public function create(User $user): Response
    {
        return $this->allowWhenUserHasPermission($user, PermissionName::NEWS_CREATE);
    }

    public function update(User $user, News $news): Response
    {
        if ($user->can(PermissionName::NEWS_UPDATE_ANY)) {
            return Response::allow();
        }

        if ((int) $news->author_id === (int) $user->getKey() && $user->can(PermissionName::NEWS_UPDATE_OWN)) {
            return Response::allow();
        }

        return Response::deny('Aksi tidak diizinkan.');
    }

    public function delete(User $user, News $news): Response
    {
        if ($user->can(PermissionName::NEWS_DELETE_ANY)) {
            return Response::allow();
        }

        if ((int) $news->author_id === (int) $user->getKey() && $user->can(PermissionName::NEWS_DELETE_OWN)) {
            return Response::allow();
        }

        return Response::deny('Aksi tidak diizinkan.');
    }

    public function publish(User $user, News $news): Response
    {
        if (! $user->can(PermissionName::NEWS_PUBLISH)) {
            return Response::deny('Aksi tidak diizinkan.');
        }

        return $this->update($user, $news);
    }

    public function unpublish(User $user, News $news): Response
    {
        if (! $user->can(PermissionName::NEWS_PUBLISH)) {
            return Response::deny('Aksi tidak diizinkan.');
        }

        return $this->update($user, $news);
    }
}
