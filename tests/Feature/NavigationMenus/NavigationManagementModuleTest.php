<?php

declare(strict_types=1);

namespace Tests\Feature\NavigationMenus;

use App\Filament\Resources\NavigationItems\Pages\CreateNavigationItem;
use App\Filament\Resources\NavigationItems\Pages\EditNavigationItem;
use App\Filament\Resources\NavigationMenus\Pages\CreateNavigationMenu;
use App\Models\User;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\NavigationMenus\Models\NavigationItem;
use App\Modules\NavigationMenus\Models\NavigationMenu;
use App\Modules\NavigationMenus\Support\NavigationLinkType;
use App\Modules\RolesPermissions\Support\RoleName;
use Database\Seeders\RolesPermissions\RolesAndPermissionsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

class NavigationManagementModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        if (! Route::has('testing.navigation.route')) {
            Route::get('/testing/navigation-route', static fn () => 'ok')->name('testing.navigation.route');
        }
    }

    /**
     * Memastikan super admin dapat mengelola menu dan item navigation serta audit event utama tercatat.
     */
    public function test_super_admin_can_manage_navigation_menu_and_items_with_audit_events(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $this->actingAs($superAdmin);

        Livewire::test(CreateNavigationMenu::class)
            ->set('data.key', 'main_header')
            ->set('data.name', 'Main Header')
            ->set('data.location', 'header')
            ->set('data.description', 'Menu utama website publik.')
            ->call('create');

        $menu = NavigationMenu::query()->firstOrFail();

        Livewire::test(CreateNavigationItem::class)
            ->set('data.navigation_menu_id', $menu->id)
            ->set('data.label', 'Profil Sekolah')
            ->set('data.link_type', NavigationLinkType::URL)
            ->set('data.link_value', '/profil-sekolah')
            ->set('data.target', '_self')
            ->set('data.sort_order', 1)
            ->set('data.is_visible', true)
            ->call('create');

        $item = NavigationItem::query()->firstOrFail();

        Livewire::test(EditNavigationItem::class, ['record' => $item->getRouteKey()])
            ->set('data.navigation_menu_id', $menu->id)
            ->set('data.parent_id', null)
            ->set('data.label', 'Profil Sekolah Baru')
            ->set('data.link_type', NavigationLinkType::ROUTE)
            ->set('data.link_value', 'testing.navigation.route')
            ->set('data.target', '_blank')
            ->set('data.sort_order', 2)
            ->set('data.is_visible', true)
            ->call('save');

        $item->refresh();

        $this->assertSame('Profil Sekolah Baru', $item->label);
        $this->assertSame(NavigationLinkType::ROUTE, $item->link_type);
        $this->assertSame('testing.navigation.route', $item->link_value);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::NAVIGATION_MENU_UPDATED,
            'module' => AuditModule::NAVIGATION,
            'entity_type' => NavigationMenu::class,
            'entity_id' => (string) $menu->id,
            'actor_id' => $superAdmin->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::NAVIGATION_ITEM_CREATED,
            'module' => AuditModule::NAVIGATION,
            'entity_type' => NavigationItem::class,
            'entity_id' => (string) $item->id,
            'actor_id' => $superAdmin->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::NAVIGATION_ITEM_UPDATED,
            'module' => AuditModule::NAVIGATION,
            'entity_type' => NavigationItem::class,
            'entity_id' => (string) $item->id,
            'actor_id' => $superAdmin->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::NAVIGATION_ITEMS_REORDERED,
            'module' => AuditModule::NAVIGATION,
            'entity_type' => NavigationItem::class,
            'entity_id' => (string) $item->id,
            'actor_id' => $superAdmin->id,
        ]);
    }

    /**
     * Memastikan validasi URL/route menolak nilai link invalid sebelum tersimpan.
     */
    public function test_navigation_link_validation_rejects_invalid_url_and_route(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $menu = NavigationMenu::factory()->create();

        $this->actingAs($superAdmin);

        Livewire::test(CreateNavigationItem::class)
            ->set('data.navigation_menu_id', $menu->id)
            ->set('data.label', 'Link Invalid')
            ->set('data.link_type', NavigationLinkType::URL)
            ->set('data.link_value', 'invalid-link')
            ->set('data.sort_order', 1)
            ->call('create')
            ->assertHasErrors(['data.link_value']);

        Livewire::test(CreateNavigationItem::class)
            ->set('data.navigation_menu_id', $menu->id)
            ->set('data.label', 'Route Invalid')
            ->set('data.link_type', NavigationLinkType::ROUTE)
            ->set('data.link_value', 'route.tidak.ada')
            ->set('data.sort_order', 1)
            ->call('create')
            ->assertHasErrors(['data.link_value']);
    }

    /**
     * Memastikan backend menolak circular parent-child dan sort order duplikat pada parent sama.
     */
    public function test_navigation_hierarchy_validation_blocks_circular_reference_and_duplicate_order(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $menu = NavigationMenu::factory()->create();

        $parentItem = NavigationItem::factory()->create([
            'navigation_menu_id' => $menu->id,
            'parent_id' => null,
            'sort_order' => 1,
        ]);

        $childItem = NavigationItem::factory()->create([
            'navigation_menu_id' => $menu->id,
            'parent_id' => $parentItem->id,
            'sort_order' => 1,
        ]);

        $this->actingAs($superAdmin);

        Livewire::test(EditNavigationItem::class, ['record' => $parentItem->getRouteKey()])
            ->set('data.navigation_menu_id', $menu->id)
            ->set('data.parent_id', $childItem->id)
            ->set('data.label', $parentItem->label)
            ->set('data.link_type', $parentItem->link_type)
            ->set('data.link_value', $parentItem->link_value)
            ->set('data.target', $parentItem->target)
            ->set('data.sort_order', 1)
            ->set('data.is_visible', true)
            ->call('save')
            ->assertHasErrors(['data.parent_id']);

        Livewire::test(CreateNavigationItem::class)
            ->set('data.navigation_menu_id', $menu->id)
            ->set('data.parent_id', null)
            ->set('data.label', 'Order Bentrok')
            ->set('data.link_type', NavigationLinkType::URL)
            ->set('data.link_value', '/order-bentrok')
            ->set('data.sort_order', 1)
            ->call('create')
            ->assertHasErrors(['data.sort_order']);
    }

    /**
     * Memastikan policy navigation menegakkan izin view/update pada server-side.
     */
    public function test_navigation_policy_enforces_view_and_update_permissions(): void
    {
        $menu = NavigationMenu::factory()->create();
        $item = NavigationItem::factory()->create([
            'navigation_menu_id' => $menu->id,
        ]);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $guru = User::factory()->create();
        $guru->assignRole(RoleName::GURU);

        $this->assertTrue(Gate::forUser($superAdmin)->allows('viewAny', NavigationMenu::class));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('update', $menu));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('update', $item));

        $this->assertFalse(Gate::forUser($guru)->allows('viewAny', NavigationMenu::class));
        $this->assertFalse(Gate::forUser($guru)->allows('update', $menu));
        $this->assertFalse(Gate::forUser($guru)->allows('update', $item));
    }

    /**
     * Memastikan user tanpa permission navigation.* ditolak backend saat mengakses halaman navigation.
     */
    public function test_user_without_navigation_permission_cannot_access_navigation_pages(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole(RoleName::GURU);

        $this->actingAs($guru)
            ->get('/admin/navigation-menus')
            ->assertForbidden();

        $this->actingAs($guru)
            ->get('/admin/navigation-items')
            ->assertForbidden();

        $this->actingAs($guru)
            ->get('/admin/navigation-menus/create')
            ->assertForbidden();

        $this->actingAs($guru)
            ->get('/admin/navigation-items/create')
            ->assertForbidden();
    }
}
