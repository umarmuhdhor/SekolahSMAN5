<?php

declare(strict_types=1);

namespace Tests\Feature\RolesPermissions;

use App\Models\User;
use App\Modules\AuditLogs\Models\AuditLog;
use App\Modules\RolesPermissions\Support\PermissionName;
use App\Modules\RolesPermissions\Support\RoleName;
use Database\Seeders\RolesPermissions\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PolicyEnforcementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan policy User mengikuti mapping permission yang eksplisit.
     */
    public function test_user_policy_enforces_permission_mapping(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $guru = User::factory()->create();
        $guru->assignRole(RoleName::GURU);

        $this->assertTrue(Gate::forUser($superAdmin)->allows('viewAny', User::class));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('create', User::class));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('assignRole', User::class));

        $this->assertFalse(Gate::forUser($guru)->allows('viewAny', User::class));
        $this->assertFalse(Gate::forUser($guru)->allows('create', User::class));
        $this->assertFalse(Gate::forUser($guru)->allows('assignRole', User::class));
    }

    /**
     * Memastikan policy Role dan Permission menolak user tanpa permission yang sesuai.
     */
    public function test_role_and_permission_policies_enforce_server_side_authorization(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $guru = User::factory()->create();
        $guru->assignRole(RoleName::GURU);

        $role = Role::findByName(RoleName::GURU, 'web');
        $permission = Permission::findByName(PermissionName::DASHBOARD_VIEW, 'web');

        $this->assertTrue(Gate::forUser($superAdmin)->allows('update', $role));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('delete', $permission));

        $this->assertFalse(Gate::forUser($guru)->allows('update', $role));
        $this->assertFalse(Gate::forUser($guru)->allows('delete', $permission));
    }

    /**
     * Memastikan ability policy yang tidak didefinisikan otomatis ditolak (deny-by-default).
     */
    public function test_undefined_policy_ability_is_denied_by_default(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $this->assertFalse(Gate::forUser($superAdmin)->allows('restore', User::class));
        $this->assertFalse(Gate::forUser($superAdmin)->allows('forceDelete', User::class));
    }

    /**
     * Memastikan policy audit log hanya mengizinkan user dengan permission audit.view.
     */
    public function test_audit_log_policy_requires_audit_view_permission(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $guru = User::factory()->create();
        $guru->assignRole(RoleName::GURU);

        $this->assertTrue(Gate::forUser($superAdmin)->allows('viewAny', AuditLog::class));
        $this->assertFalse(Gate::forUser($guru)->allows('viewAny', AuditLog::class));
    }
}
