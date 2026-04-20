<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Modules\Announcements\Models\Announcement;
use App\Modules\AuditLogs\Models\AuditLog;
use App\Modules\Galleries\Models\Gallery;
use App\Modules\MediaLibrary\Models\MediaAsset;
use App\Modules\NavigationMenus\Models\NavigationItem;
use App\Modules\NavigationMenus\Models\NavigationMenu;
use App\Modules\News\Models\News;
use App\Modules\RolesPermissions\Support\AuthorizationAbility;
use App\Modules\RolesPermissions\Support\PermissionName;
use App\Modules\RolesPermissions\Support\RoleName;
use App\Modules\SchoolProfile\Models\SchoolProfile;
use App\Modules\ThemeSettings\Models\ThemeSetting;
use App\Policies\AnnouncementPolicy;
use App\Policies\AuditLogPolicy;
use App\Policies\GalleryPolicy;
use App\Policies\MediaAssetPolicy;
use App\Policies\NavigationItemPolicy;
use App\Policies\NavigationMenuPolicy;
use App\Policies\NewsPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\SchoolProfilePolicy;
use App\Policies\ThemeSettingPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        AuditLog::class => AuditLogPolicy::class,
        MediaAsset::class => MediaAssetPolicy::class,
        News::class => NewsPolicy::class,
        Announcement::class => AnnouncementPolicy::class,
        Gallery::class => GalleryPolicy::class,
        SchoolProfile::class => SchoolProfilePolicy::class,
        ThemeSetting::class => ThemeSettingPolicy::class,
        NavigationMenu::class => NavigationMenuPolicy::class,
        NavigationItem::class => NavigationItemPolicy::class,
    ];

    /**
     * Register authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define(
            AuthorizationAbility::PANEL_ACCESS,
            fn (User $user): bool => $user->hasAnyRole(RoleName::adminPanelRoles())
                && $user->can(PermissionName::DASHBOARD_VIEW)
        );

        Gate::define(
            AuthorizationAbility::PANEL_DASHBOARD_VIEW,
            fn (User $user): bool => $user->hasAnyRole(RoleName::adminPanelRoles())
                && $user->can(PermissionName::DASHBOARD_VIEW)
        );
    }
}
