<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use App\Modules\NavigationMenus\Models\NavigationItem;
use App\Modules\NavigationMenus\Models\NavigationMenu;
use App\Modules\NavigationMenus\Support\NavigationItemTarget;
use App\Modules\NavigationMenus\Support\NavigationLinkType;
use App\Modules\NavigationMenus\Support\NavigationMenuLocation;
use App\Modules\SchoolProfile\Models\SchoolProfile;
use App\Modules\ThemeSettings\Models\ThemeSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PublicHomepageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Route::has('testing.public.page')) {
            Route::get('/testing/public-page', static fn () => 'ok')->name('testing.public.page');
        }
    }

    public function test_homepage_renders_safe_fallback_when_configuration_is_missing(): void
    {
        $response = $this->get(route('public.home'));

        $response->assertOk();
        $response->assertSee((string) config('app.name', 'CMS Sekolah Dinamis'));
        $response->assertSee('Alamat sekolah belum diperbarui.');
        $response->assertSee('Navigasi publik belum diatur.');
        $response->assertSee(asset('assets/theme/default-school-logo.svg'), false);
    }

    public function test_homepage_uses_school_profile_theme_and_navigation_from_configuration(): void
    {
        SchoolProfile::factory()->create([
            'school_name' => 'SMA Dinamis Makassar',
            'description' => 'Sekolah unggulan berbasis akademik dan karakter.',
            'address' => 'Jl. Pendidikan No. 1, Makassar',
            'email' => 'info@dinamis.test',
            'phone' => '+62 411 123456',
            'website' => 'https://dinamis.test',
            'instagram_url' => 'https://instagram.com/dinamis',
        ]);

        ThemeSetting::factory()->create([
            'primary_color' => '#112233',
            'secondary_color' => '#225566',
            'accent_color' => '#CC8844',
            'is_active' => true,
        ]);

        $headerMenu = NavigationMenu::factory()->create([
            'location' => NavigationMenuLocation::HEADER,
            'is_active' => true,
            'name' => 'Header Publik',
        ]);

        $footerMenu = NavigationMenu::factory()->create([
            'location' => NavigationMenuLocation::FOOTER,
            'is_active' => true,
            'name' => 'Footer Publik',
        ]);

        $parent = NavigationItem::factory()->create([
            'navigation_menu_id' => $headerMenu->id,
            'label' => 'Profil',
            'link_type' => NavigationLinkType::URL,
            'link_value' => '/profil',
            'target' => NavigationItemTarget::SELF,
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        NavigationItem::factory()->create([
            'navigation_menu_id' => $headerMenu->id,
            'label' => 'Portal Siswa',
            'link_type' => NavigationLinkType::URL,
            'link_value' => 'https://portal.siswa.test',
            'target' => NavigationItemTarget::BLANK,
            'sort_order' => 2,
            'is_visible' => true,
        ]);

        NavigationItem::factory()->create([
            'navigation_menu_id' => $headerMenu->id,
            'label' => 'Tidak Tampil',
            'link_type' => NavigationLinkType::URL,
            'link_value' => '/hidden',
            'target' => NavigationItemTarget::SELF,
            'sort_order' => 3,
            'is_visible' => false,
        ]);

        NavigationItem::factory()->create([
            'navigation_menu_id' => $footerMenu->id,
            'label' => 'Kontak',
            'link_type' => NavigationLinkType::URL,
            'link_value' => '/kontak',
            'target' => NavigationItemTarget::SELF,
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        $response = $this->get(route('public.home'));

        $response->assertOk();
        $response->assertSee('SMA Dinamis Makassar');
        $response->assertSee('Sekolah unggulan berbasis akademik dan karakter.');
        $response->assertSee('Jl. Pendidikan No. 1, Makassar');
        $response->assertSee('info@dinamis.test');
        $response->assertSee('#112233');
        $response->assertSee('Profil');
        $response->assertSee('Portal Siswa');
        $response->assertSee('https://portal.siswa.test', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertDontSee('Tidak Tampil');
    }

    public function test_homepage_skips_navigation_item_when_route_is_invalid(): void
    {
        $headerMenu = NavigationMenu::factory()->create([
            'location' => NavigationMenuLocation::HEADER,
            'is_active' => true,
        ]);

        NavigationItem::factory()->create([
            'navigation_menu_id' => $headerMenu->id,
            'label' => 'Route Rusak',
            'link_type' => NavigationLinkType::ROUTE,
            'link_value' => 'route.yang.tidak.ada',
            'target' => NavigationItemTarget::SELF,
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        $this->get(route('public.home'))
            ->assertOk()
            ->assertDontSee('Route Rusak');
    }
}
