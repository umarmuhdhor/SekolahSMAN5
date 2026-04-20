<?php

declare(strict_types=1);

namespace Tests\Feature\AuditLogs;

use App\Models\User;
use App\Modules\AuditLogs\Models\AuditLog;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\RolesPermissions\Support\PermissionName;
use App\Modules\RolesPermissions\Support\RoleName;
use Database\Seeders\RolesPermissions\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionAuditLoggingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan attach/detach role saat ada actor terautentikasi tercatat sebagai role_change.
     */
    public function test_role_attach_and_detach_are_recorded_in_audit_logs(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $actor = User::factory()->create();
        $actor->assignRole(RoleName::SUPER_ADMIN);

        $targetUser = User::factory()->create();

        $this->actingAs($actor);

        $targetUser->assignRole(RoleName::GURU);
        $targetUser->removeRole(RoleName::GURU);

        $roleChanges = AuditLog::query()
            ->where('event_type', AuditEventType::ROLE_CHANGE)
            ->orderBy('id')
            ->get();

        $this->assertCount(2, $roleChanges);
        $this->assertSame(AuditModule::ROLES_PERMISSIONS, $roleChanges[0]->module);
        $this->assertSame($actor->id, $roleChanges[0]->actor_id);
        $this->assertSame(User::class, $roleChanges[0]->entity_type);
        $this->assertSame((string) $targetUser->id, $roleChanges[0]->entity_id);
        $this->assertSame('attached', data_get($roleChanges[0]->after_json, 'change'));
        $this->assertSame('detached', data_get($roleChanges[1]->after_json, 'change'));
    }

    /**
     * Memastikan attach/detach permission saat ada actor terautentikasi tercatat sebagai permission_change.
     */
    public function test_permission_attach_and_detach_are_recorded_in_audit_logs(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $actor = User::factory()->create();
        $actor->assignRole(RoleName::SUPER_ADMIN);

        $targetUser = User::factory()->create();

        $this->actingAs($actor);

        $targetUser->givePermissionTo(PermissionName::NEWS_VIEW);
        $targetUser->revokePermissionTo(PermissionName::NEWS_VIEW);

        $permissionChanges = AuditLog::query()
            ->where('event_type', AuditEventType::PERMISSION_CHANGE)
            ->orderBy('id')
            ->get();

        $this->assertCount(2, $permissionChanges);
        $this->assertSame(AuditModule::ROLES_PERMISSIONS, $permissionChanges[0]->module);
        $this->assertSame($actor->id, $permissionChanges[0]->actor_id);
        $this->assertSame(User::class, $permissionChanges[0]->entity_type);
        $this->assertSame((string) $targetUser->id, $permissionChanges[0]->entity_id);
        $this->assertSame('attached', data_get($permissionChanges[0]->after_json, 'change'));
        $this->assertSame('detached', data_get($permissionChanges[1]->after_json, 'change'));
    }

    /**
     * Memastikan mutasi role/permission tanpa actor terautentikasi tidak dicatat sebagai aktivitas admin.
     */
    public function test_role_permission_changes_without_authenticated_actor_are_not_recorded(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $targetUser = User::factory()->create();

        $targetUser->assignRole(RoleName::GURU);
        $targetUser->givePermissionTo(PermissionName::NEWS_VIEW);

        $this->assertDatabaseCount('audit_logs', 0);
    }
}
