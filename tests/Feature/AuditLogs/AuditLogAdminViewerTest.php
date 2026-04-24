<?php

declare(strict_types=1);

namespace Tests\Feature\AuditLogs;

use App\Filament\Resources\AuditLogs\Pages\ListAuditLogs;
use App\Models\User;
use App\Modules\AuditLogs\Models\AuditLog;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\RolesPermissions\Support\RoleName;
use Database\Seeders\RolesPermissions\RolesAndPermissionsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuditLogAdminViewerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    /**
     * Memastikan super admin dapat mengakses halaman audit logs admin viewer.
     */
    public function test_super_admin_can_access_audit_log_viewer_page(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $this->actingAs($superAdmin)
            ->get('/admin/audit-logs')
            ->assertOk();
    }

    /**
     * Memastikan role tanpa permission audit.view ditolak backend saat akses audit logs.
     */
    public function test_user_without_audit_permission_cannot_access_audit_log_viewer_page(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole(RoleName::GURU);

        $this->actingAs($guru)
            ->get('/admin/audit-logs')
            ->assertForbidden();
    }

    /**
     * Memastikan filter actor, module, dan rentang tanggal bekerja pada audit log viewer.
     */
    public function test_audit_log_viewer_filters_actor_module_and_date_range(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $secondActor = User::factory()->create();
        $secondActor->assignRole(RoleName::SUPER_ADMIN);

        AuditLog::query()->create([
            'actor_id' => $superAdmin->id,
            'event_type' => AuditEventType::NEWS_CREATED,
            'module' => AuditModule::NEWS,
            'entity_type' => 'news',
            'entity_id' => '101',
            'created_at' => now()->subDays(3),
        ]);

        AuditLog::query()->create([
            'actor_id' => $secondActor->id,
            'event_type' => AuditEventType::USER_CREATED,
            'module' => AuditModule::USERS,
            'entity_type' => 'user',
            'entity_id' => '202',
            'created_at' => now()->subDay(),
        ]);

        $this->actingAs($superAdmin);

        Livewire::test(ListAuditLogs::class)
            ->set('tableFilters.actor_id.value', (string) $superAdmin->id)
            ->assertSee(AuditEventType::NEWS_CREATED)
            ->assertDontSee(AuditEventType::USER_CREATED)
            ->set('tableFilters.actor_id.value', null)
            ->set('tableFilters.module.value', AuditModule::USERS)
            ->assertSee(AuditEventType::USER_CREATED)
            ->assertDontSee(AuditEventType::NEWS_CREATED)
            ->set('tableFilters.module.value', null)
            ->set('tableFilters.created_at_range.created_from', now()->subDays(2)->toDateString())
            ->set('tableFilters.created_at_range.created_until', now()->toDateString())
            ->assertSee(AuditEventType::USER_CREATED)
            ->assertDontSee(AuditEventType::NEWS_CREATED);
    }
}
