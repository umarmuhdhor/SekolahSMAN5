<?php

declare(strict_types=1);

namespace App\Policies\Concerns;

use App\Models\User;
use Illuminate\Auth\Access\Response;

trait AuthorizesWithPermissions
{
    protected function allowWhenUserHasPermission(User $user, string $permission): Response
    {
        return $user->can($permission)
            ? Response::allow()
            : Response::deny('Aksi tidak diizinkan.');
    }
}
