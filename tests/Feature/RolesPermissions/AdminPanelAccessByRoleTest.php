<?php

declare(strict_types=1);

namespace Tests\Feature\RolesPermissions;

use App\Models\User;
use App\Modules\RolesPermissions\Support\AuthorizationAbility;
use App\Modules\RolesPermissions\Support\PermissionName;
use App\Modules\RolesPermissions\Support\RoleName;
use Database\Seeders\RolesPermissions\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPanelAccessByRoleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan role admin dasar (super_admin, guru) diizinkan mengakses panel admin.
     */
    public function test_super_admin_and_guru_can_access_admin_panel(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $guru = User::factory()->create();
        $guru->assignRole(RoleName::GURU);

        $this->actingAs($superAdmin)
            ->get('/admin')
            ->assertOk();

        $this->actingAs($guru)
            ->get('/admin')
            ->assertOk();

        $this->assertTrue(
            Gate::forUser($superAdmin)->allows(AuthorizationAbility::PANEL_ACCESS)
        );
        $this->assertTrue(
            Gate::forUser($guru)->allows(AuthorizationAbility::PANEL_ACCESS)
        );
        $this->assertTrue(
            Gate::forUser($superAdmin)->allows(AuthorizationAbility::PANEL_DASHBOARD_VIEW)
        );
        $this->assertTrue(
            Gate::forUser($guru)->allows(AuthorizationAbility::PANEL_DASHBOARD_VIEW)
        );
    }

    /**
     * Memastikan role non-admin (siswa, orang_tua) ditolak mengakses panel admin.
     */
    public function test_siswa_and_orang_tua_cannot_access_admin_panel(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $siswa = User::factory()->create();
        $siswa->assignRole(RoleName::SISWA);

        $orangTua = User::factory()->create();
        $orangTua->assignRole(RoleName::ORANG_TUA);

        $this->actingAs($siswa)
            ->get('/admin')
            ->assertForbidden();

        $this->actingAs($orangTua)
            ->get('/admin')
            ->assertForbidden();
    }

    /**
     * Memastikan role admin tanpa permission dashboard tetap ditolak secara backend.
     */
    public function test_guru_without_dashboard_permission_cannot_access_admin_panel(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $guruRole = Role::findByName(RoleName::GURU, 'web');
        $guruRole->revokePermissionTo(PermissionName::DASHBOARD_VIEW);

        $guru = User::factory()->create();
        $guru->assignRole(RoleName::GURU);

        $this->actingAs($guru)
            ->get('/admin')
            ->assertForbidden();

        $this->assertFalse(
            Gate::forUser($guru)->allows(AuthorizationAbility::PANEL_ACCESS)
        );
        $this->assertFalse(
            Gate::forUser($guru)->allows(AuthorizationAbility::PANEL_DASHBOARD_VIEW)
        );
    }

    /**
     * Memastikan permission dashboard saja tidak cukup jika role bukan admin panel.
     */
    public function test_non_admin_role_with_dashboard_permission_is_still_denied_admin_panel(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $siswa = User::factory()->create();
        $siswa->assignRole(RoleName::SISWA);
        $siswa->givePermissionTo(PermissionName::DASHBOARD_VIEW);

        $this->actingAs($siswa)
            ->get('/admin')
            ->assertForbidden();

        $this->assertFalse(
            Gate::forUser($siswa)->allows(AuthorizationAbility::PANEL_ACCESS)
        );
        $this->assertFalse(
            Gate::forUser($siswa)->allows(AuthorizationAbility::PANEL_DASHBOARD_VIEW)
        );
    }
}
