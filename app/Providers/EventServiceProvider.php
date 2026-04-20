<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\Auth\RecordFailedLoginEvent;
use App\Listeners\Auth\RecordLoginEvent;
use App\Listeners\Auth\RecordLogoutEvent;
use App\Listeners\RolesPermissions\RecordPermissionAttachedEvent;
use App\Listeners\RolesPermissions\RecordPermissionDetachedEvent;
use App\Listeners\RolesPermissions\RecordRoleAttachedEvent;
use App\Listeners\RolesPermissions\RecordRoleDetachedEvent;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Spatie\Permission\Events\PermissionAttached;
use Spatie\Permission\Events\PermissionDetached;
use Spatie\Permission\Events\RoleAttached;
use Spatie\Permission\Events\RoleDetached;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Pemetaan listener event lintas aplikasi.
     *
     * Fokus fase T006-T009:
     * - Menjamin event auth utama tercatat.
     * - Menjamin mutasi role/permission tercatat sebagai audit event dasar.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Login::class => [
            RecordLoginEvent::class,
        ],
        Logout::class => [
            RecordLogoutEvent::class,
        ],
        Failed::class => [
            RecordFailedLoginEvent::class,
        ],
        RoleAttached::class => [
            RecordRoleAttachedEvent::class,
        ],
        RoleDetached::class => [
            RecordRoleDetachedEvent::class,
        ],
        PermissionAttached::class => [
            RecordPermissionAttachedEvent::class,
        ],
        PermissionDetached::class => [
            RecordPermissionDetachedEvent::class,
        ],
    ];

    /**
     * Menentukan apakah event discovery otomatis diaktifkan.
     *
     * Discovery dinonaktifkan untuk menjaga mapping listener eksplisit dan mudah diaudit.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
