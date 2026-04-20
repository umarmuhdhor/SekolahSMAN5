<?php

declare(strict_types=1);

namespace Tests\Feature\RolesPermissions;

use App\Modules\RolesPermissions\Support\PermissionName;
use App\Modules\RolesPermissions\Support\RoleName;
use Database\Seeders\RolesPermissions\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionFoundationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan role wajib dan permission baseline berhasil diseed.
     */
    public function test_roles_and_permissions_seeded_successfully(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        foreach (RoleName::all() as $roleName) {
            $this->assertDatabaseHas('roles', ['name' => $roleName, 'guard_name' => 'web']);
        }

        foreach (PermissionName::all() as $permissionName) {
            $this->assertDatabaseHas('permissions', ['name' => $permissionName, 'guard_name' => 'web']);
        }

        $superAdmin = Role::findByName(RoleName::SUPER_ADMIN, 'web');
        $this->assertCount(count(PermissionName::all()), $superAdmin->permissions);
    }

    /**
     * Memastikan role guru mendapatkan subset permission sesuai baseline dan bukan full akses.
     */
    public function test_guru_receives_default_subset_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $guru = Role::findByName(RoleName::GURU, 'web');
        $guruPermissionNames = $guru->permissions->pluck('name')->sort()->values()->all();
        $expectedPermissionNames = collect(PermissionName::guruDefaults())->sort()->values()->all();

        $this->assertSame($expectedPermissionNames, $guruPermissionNames);

        $this->assertFalse($guru->hasPermissionTo(PermissionName::USERS_ASSIGN_ROLE));
        $this->assertFalse($guru->hasPermissionTo(PermissionName::AUDIT_VIEW));
        $this->assertTrue(Permission::where('name', PermissionName::DASHBOARD_VIEW)->exists());
    }
}
