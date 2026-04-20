<?php

declare(strict_types=1);

namespace Tests\Feature\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Models\User;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\RolesPermissions\Support\RoleName;
use Database\Seeders\RolesPermissions\RolesAndPermissionsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    /**
     * Memastikan super admin dapat membuat user baru, assign role, dan event audit dasar tercatat.
     */
    public function test_super_admin_can_create_user_and_assign_role(): void
    {
        $actor = User::factory()->create();
        $actor->assignRole(RoleName::SUPER_ADMIN);

        $this->actingAs($actor);

        Livewire::test(CreateUser::class)
            ->set('data.name', 'Guru Baru')
            ->set('data.email', 'guru.baru@sekolah.local')
            ->set('data.password', 'Password123')
            ->set('data.password_confirmation', 'Password123')
            ->set('data.is_active', true)
            ->set('data.assigned_roles', [RoleName::GURU])
            ->call('create');

        $createdUser = User::query()->where('email', 'guru.baru@sekolah.local')->firstOrFail();

        $this->assertTrue($createdUser->hasRole(RoleName::GURU));
        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::USER_CREATED,
            'module' => AuditModule::USERS,
            'entity_type' => User::class,
            'entity_id' => (string) $createdUser->id,
            'actor_id' => $actor->id,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::ROLE_CHANGE,
            'module' => AuditModule::ROLES_PERMISSIONS,
            'entity_type' => User::class,
            'entity_id' => (string) $createdUser->id,
            'actor_id' => $actor->id,
        ]);
    }

    /**
     * Memastikan validasi email unik aktif pada create user.
     */
    public function test_create_user_requires_unique_email(): void
    {
        $actor = User::factory()->create();
        $actor->assignRole(RoleName::SUPER_ADMIN);

        User::factory()->create([
            'email' => 'duplicate@sekolah.local',
        ]);

        $this->actingAs($actor);

        Livewire::test(CreateUser::class)
            ->set('data.name', 'User Duplikat')
            ->set('data.email', 'duplicate@sekolah.local')
            ->set('data.password', 'Password123')
            ->set('data.password_confirmation', 'Password123')
            ->set('data.is_active', true)
            ->call('create')
            ->assertHasErrors(['data.email']);
    }

    /**
     * Memastikan perubahan status akun user tercatat pada audit log.
     */
    public function test_status_change_is_recorded_in_audit_log(): void
    {
        $actor = User::factory()->create();
        $actor->assignRole(RoleName::SUPER_ADMIN);

        $targetUser = User::factory()->create([
            'is_active' => true,
        ]);
        $targetUser->assignRole(RoleName::GURU);

        $this->actingAs($actor);

        Livewire::test(EditUser::class, ['record' => $targetUser->getRouteKey()])
            ->set('data.is_active', false)
            ->call('save');

        $targetUser->refresh();

        $this->assertFalse($targetUser->is_active);
        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::USER_STATUS_CHANGED,
            'module' => AuditModule::USERS,
            'entity_type' => User::class,
            'entity_id' => (string) $targetUser->id,
            'actor_id' => $actor->id,
        ]);
    }

    /**
     * Memastikan guru tanpa permission users.* ditolak mengakses user management.
     */
    public function test_guru_cannot_access_user_management_pages(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole(RoleName::GURU);

        $this->actingAs($guru)
            ->get('/admin/users')
            ->assertForbidden();

        $this->actingAs($guru)
            ->get('/admin/users/create')
            ->assertForbidden();
    }

    /**
     * Memastikan user nonaktif ditolak mengakses panel admin.
     */
    public function test_inactive_admin_user_cannot_access_admin_panel(): void
    {
        $superAdmin = User::factory()->create([
            'is_active' => false,
        ]);
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $this->actingAs($superAdmin)
            ->get('/admin')
            ->assertForbidden();
    }

    /**
     * Memastikan super admin terakhir tidak dapat dihapus oleh policy backend.
     */
    public function test_last_super_admin_cannot_be_deleted(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $this->assertFalse(
            Gate::forUser($superAdmin)->allows('delete', $superAdmin)
        );

        $secondSuperAdmin = User::factory()->create();
        $secondSuperAdmin->assignRole(RoleName::SUPER_ADMIN);

        $this->assertTrue(
            Gate::forUser($superAdmin)->allows('delete', $secondSuperAdmin)
        );
    }
}
