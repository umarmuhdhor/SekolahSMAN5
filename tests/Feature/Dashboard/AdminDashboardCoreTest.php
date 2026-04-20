<?php

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Modules\Dashboard\Actions\GetDashboardQuickLinksAction;
use App\Modules\RolesPermissions\Support\PermissionName;
use App\Modules\RolesPermissions\Support\RoleName;
use Database\Seeders\RolesPermissions\RolesAndPermissionsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardCoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    /**
     * Memastikan dashboard admin dapat diakses user berizin dan menampilkan quick links sesuai permission.
     */
    public function test_dashboard_quick_links_are_filtered_by_user_permissions(): void
    {
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

        $superAdminLinks = app(GetDashboardQuickLinksAction::class)->execute($superAdmin);
        $guruLinks = app(GetDashboardQuickLinksAction::class)->execute($guru);

        $superAdminKeys = collect($superAdminLinks)->pluck('key')->all();
        $guruKeys = collect($guruLinks)->pluck('key')->all();

        $this->assertContains('users', $superAdminKeys);
        $this->assertContains('audit_logs', $superAdminKeys);
        $this->assertContains('news', $guruKeys);
        $this->assertContains('announcements', $guruKeys);
        $this->assertNotContains('users', $guruKeys);
        $this->assertNotContains('audit_logs', $guruKeys);
    }

    /**
     * Memastikan link modul users pada dashboard aktif, sedangkan modul roadmap lain masih ditandai belum tersedia.
     */
    public function test_dashboard_quick_links_include_ready_and_upcoming_modules(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $links = collect(app(GetDashboardQuickLinksAction::class)->execute($superAdmin))->keyBy('key');

        $this->assertTrue((bool) $links['users']['is_ready']);
        $this->assertNotNull($links['users']['url']);
        $this->assertTrue((bool) $links['media']['is_ready']);
        $this->assertNotNull($links['media']['url']);

        $this->assertTrue((bool) $links['news']['is_ready']);
        $this->assertNotNull($links['news']['url']);
        $this->assertTrue((bool) $links['announcements']['is_ready']);
        $this->assertNotNull($links['announcements']['url']);
        $this->assertTrue((bool) $links['galleries']['is_ready']);
        $this->assertNotNull($links['galleries']['url']);
        $this->assertTrue((bool) $links['school_profile']['is_ready']);
        $this->assertNotNull($links['school_profile']['url']);
        $this->assertTrue((bool) $links['theme']['is_ready']);
        $this->assertNotNull($links['theme']['url']);
    }

    /**
     * Memastikan menu resource users mengikuti permission users.view.
     */
    public function test_users_navigation_is_guarded_by_permission(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $guru = User::factory()->create();
        $guru->assignRole(RoleName::GURU);

        $this->actingAs($superAdmin);
        $this->assertTrue($superAdmin->can(PermissionName::USERS_VIEW));
        $this->assertTrue(UserResource::canAccess());

        $this->actingAs($guru);
        $this->assertFalse($guru->can(PermissionName::USERS_VIEW));
        $this->assertFalse(UserResource::canAccess());
    }
}
