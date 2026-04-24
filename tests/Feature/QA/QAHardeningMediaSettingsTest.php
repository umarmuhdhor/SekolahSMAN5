<?php

declare(strict_types=1);

namespace Tests\Feature\QA;

use App\Modules\MediaLibrary\Actions\PrepareMediaAssetPayloadAction;
use App\Modules\MediaLibrary\Models\MediaAsset;
use App\Modules\NavigationMenus\Actions\AssertUniqueNavigationSortOrderAction;
use App\Modules\NavigationMenus\Actions\ResolvePublicNavigationMenuAction;
use App\Modules\NavigationMenus\Actions\ValidateNavigationLinkAction;
use App\Modules\NavigationMenus\Models\NavigationItem;
use App\Modules\NavigationMenus\Models\NavigationMenu;
use App\Modules\NavigationMenus\Support\NavigationLinkType;
use App\Modules\NavigationMenus\Support\NavigationMenuLocation;
use App\Modules\ThemeSettings\Actions\ResolveActiveThemeSettingsAction;
use App\Modules\ThemeSettings\Models\ThemeSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class QAHardeningMediaSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('media_library.disk', 's3');
        config()->set('filesystems.default', 's3');
        Storage::fake('s3');
    }

    public function test_media_payload_validation_rejects_file_exceeding_mime_specific_limit(): void
    {
        config()->set('media_library.max_size_kb', [
            'image/*' => 0,
            '*' => 0,
        ]);

        $uploadedImage = UploadedFile::fake()->image('oversized.jpg')->size(2048);
        $path = $uploadedImage->storeAs('media-library', 'oversized.jpg', 's3');

        try {
            app(PrepareMediaAssetPayloadAction::class)->execute([
                'path' => $path,
                'original_name' => 'oversized.jpg',
            ]);

            $this->fail('Expected validation exception for oversized media file.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('data.path', $exception->errors());
            $this->assertStringContainsString('Ukuran file melebihi batas', $exception->errors()['data.path'][0]);
        }
    }

    public function test_media_payload_sanitizes_original_name_and_produces_checksum(): void
    {
        $uploadedImage = UploadedFile::fake()->image('safe.png')->size(120);
        $path = $uploadedImage->storeAs('media-library', 'safe.png', 's3');

        $payload = app(PrepareMediaAssetPayloadAction::class)->execute([
            'path' => $path,
            'original_name' => '../../secrets/'.str_repeat('a', 300).'.png',
        ]);

        $this->assertSame('s3', $payload['disk']);
        $this->assertSame('private', $payload['visibility']);
        $this->assertSame(64, strlen((string) $payload['checksum']));
        $this->assertStringNotContainsString('/', (string) $payload['original_name']);
        $this->assertStringNotContainsString('\\', (string) $payload['original_name']);
        $this->assertLessThanOrEqual(255, strlen((string) $payload['original_name']));
    }

    public function test_theme_resolver_uses_and_marks_logo_fallback_when_logo_file_is_missing(): void
    {
        $logoMedia = MediaAsset::query()->create([
            'disk' => 's3',
            'bucket' => 'cms-sekolah-media',
            'path' => 'media-library/logo-hilang.png',
            'file_name' => 'logo-hilang.png',
            'original_name' => 'logo-hilang.png',
            'extension' => 'png',
            'mime_type' => 'image/png',
            'size_bytes' => 1000,
            'checksum' => hash('sha256', 'logo-hilang'),
            'visibility' => 'private',
            'uploaded_by' => null,
        ]);

        ThemeSetting::query()->create([
            'singleton_key' => 'default',
            'logo_media_id' => $logoMedia->id,
            'primary_color' => '#1D4ED8',
            'secondary_color' => '#0F766E',
            'accent_color' => '#F59E0B',
            'is_active' => true,
            'updated_by' => null,
        ]);

        $resolved = app(ResolveActiveThemeSettingsAction::class)->execute();

        $this->assertSame(asset('assets/theme/default-school-logo.svg'), $resolved['logo_url']);
        $this->assertTrue($resolved['is_fallback']);
    }

    public function test_navigation_link_validator_rejects_javascript_scheme_and_invalid_protocol(): void
    {
        $validator = app(ValidateNavigationLinkAction::class);

        $this->expectException(ValidationException::class);

        $validator->execute(
            linkType: NavigationLinkType::URL,
            linkValue: 'javascript:alert(1)',
        );
    }

    public function test_public_navigation_resolver_skips_invalid_config_normalizes_empty_label_and_limits_depth(): void
    {
        $menu = NavigationMenu::factory()->create([
            'location' => NavigationMenuLocation::HEADER,
            'is_active' => true,
            'name' => 'Header QA',
        ]);

        $validRoot = NavigationItem::factory()->create([
            'navigation_menu_id' => $menu->id,
            'parent_id' => null,
            'label' => 'Profil',
            'link_type' => NavigationLinkType::URL,
            'link_value' => '/profil',
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        NavigationItem::factory()->create([
            'navigation_menu_id' => $menu->id,
            'parent_id' => null,
            'label' => 'Script Injection',
            'link_type' => NavigationLinkType::URL,
            'link_value' => 'javascript:alert(1)',
            'sort_order' => 2,
            'is_visible' => true,
        ]);

        NavigationItem::factory()->create([
            'navigation_menu_id' => $menu->id,
            'parent_id' => null,
            'label' => 'Route Invalid',
            'link_type' => NavigationLinkType::ROUTE,
            'link_value' => 'route.invalid.qa',
            'sort_order' => 3,
            'is_visible' => true,
        ]);

        NavigationItem::factory()->create([
            'navigation_menu_id' => $menu->id,
            'parent_id' => null,
            'label' => '   ',
            'link_type' => NavigationLinkType::URL,
            'link_value' => '/untitled',
            'sort_order' => 4,
            'is_visible' => true,
        ]);

        $childLevel1 = NavigationItem::factory()->create([
            'navigation_menu_id' => $menu->id,
            'parent_id' => $validRoot->id,
            'label' => 'Akademik',
            'link_type' => NavigationLinkType::URL,
            'link_value' => '/akademik',
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        $childLevel2 = NavigationItem::factory()->create([
            'navigation_menu_id' => $menu->id,
            'parent_id' => $childLevel1->id,
            'label' => 'Kurikulum',
            'link_type' => NavigationLinkType::URL,
            'link_value' => '/kurikulum',
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        NavigationItem::factory()->create([
            'navigation_menu_id' => $menu->id,
            'parent_id' => $childLevel2->id,
            'label' => 'Terlalu Dalam',
            'link_type' => NavigationLinkType::URL,
            'link_value' => '/terlalu-dalam',
            'sort_order' => 1,
            'is_visible' => true,
        ]);

        $resolved = app(ResolvePublicNavigationMenuAction::class)->execute(NavigationMenuLocation::HEADER);

        $labels = collect($resolved['items'])->pluck('label')->all();

        $this->assertContains('Profil', $labels);
        $this->assertContains('Untitled', $labels);
        $this->assertNotContains('Script Injection', $labels);
        $this->assertNotContains('Route Invalid', $labels);

        $rootItem = collect($resolved['items'])->firstWhere('label', 'Profil');

        $this->assertNotNull($rootItem);
        $this->assertCount(1, $rootItem['children']);
        $this->assertSame('Akademik', $rootItem['children'][0]['label']);
        $this->assertCount(1, $rootItem['children'][0]['children']);
        $this->assertSame('Kurikulum', $rootItem['children'][0]['children'][0]['label']);
        $this->assertSame([], $rootItem['children'][0]['children'][0]['children']);
    }

    public function test_navigation_sort_order_uniqueness_is_enforced_for_same_parent_level(): void
    {
        $menu = NavigationMenu::factory()->create();

        $parent = NavigationItem::factory()->create([
            'navigation_menu_id' => $menu->id,
            'parent_id' => null,
            'sort_order' => 1,
        ]);

        NavigationItem::factory()->create([
            'navigation_menu_id' => $menu->id,
            'parent_id' => $parent->id,
            'sort_order' => 1,
        ]);

        $this->expectException(ValidationException::class);

        app(AssertUniqueNavigationSortOrderAction::class)->execute(
            navigationMenuId: $menu->id,
            parentId: $parent->id,
            sortOrder: 1,
        );
    }
}
