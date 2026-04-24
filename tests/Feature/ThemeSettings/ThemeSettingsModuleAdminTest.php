<?php

declare(strict_types=1);

namespace Tests\Feature\ThemeSettings;

use App\Filament\Resources\ThemeSettings\Pages\CreateThemeSetting;
use App\Filament\Resources\ThemeSettings\Pages\EditThemeSetting;
use App\Models\User;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\MediaLibrary\Models\MediaAsset;
use App\Modules\RolesPermissions\Support\RoleName;
use App\Modules\ThemeSettings\Actions\ResolveActiveThemeSettingsAction;
use App\Modules\ThemeSettings\Models\ThemeSetting;
use App\Modules\ThemeSettings\Support\ThemeDefaults;
use Database\Seeders\RolesPermissions\RolesAndPermissionsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ThemeSettingsModuleAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        config()->set('filesystems.default', 's3');
        Storage::fake('s3');
    }

    /**
     * Memastikan super admin dapat membuat dan memperbarui theme settings dengan audit event theme terkait.
     */
    public function test_super_admin_can_create_and_update_theme_settings_with_audit_events(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $firstLogo = $this->createMediaAsset($superAdmin->id, 'media-library/logo-awal.png');
        $secondLogo = $this->createMediaAsset($superAdmin->id, 'media-library/logo-baru.png');

        $this->actingAs($superAdmin);

        Livewire::test(CreateThemeSetting::class)
            ->set('data.logo_media_id', $firstLogo->id)
            ->set('data.primary_color', '#1d4ed8')
            ->set('data.secondary_color', '#0f766e')
            ->set('data.accent_color', '#f59e0b')
            ->call('create');

        $themeSetting = ThemeSetting::query()->firstOrFail();

        $this->assertSame('#1D4ED8', $themeSetting->primary_color);
        $this->assertSame('#0F766E', $themeSetting->secondary_color);
        $this->assertSame('#F59E0B', $themeSetting->accent_color);

        Livewire::test(EditThemeSetting::class, ['record' => $themeSetting->getRouteKey()])
            ->set('data.logo_media_id', $secondLogo->id)
            ->set('data.primary_color', '#0EA5E9')
            ->call('save');

        $themeSetting->refresh();

        $this->assertSame($secondLogo->id, $themeSetting->logo_media_id);
        $this->assertSame('#0EA5E9', $themeSetting->primary_color);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::THEME_UPDATED,
            'module' => AuditModule::THEME,
            'entity_type' => ThemeSetting::class,
            'entity_id' => (string) $themeSetting->id,
            'actor_id' => $superAdmin->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::THEME_LOGO_CHANGED,
            'module' => AuditModule::THEME,
            'entity_type' => ThemeSetting::class,
            'entity_id' => (string) $themeSetting->id,
            'actor_id' => $superAdmin->id,
        ]);
    }

    /**
     * Memastikan validasi backend menolak warna yang bukan HEX 6 digit.
     */
    public function test_theme_color_validation_rejects_invalid_hex_value(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $this->actingAs($superAdmin);

        Livewire::test(CreateThemeSetting::class)
            ->set('data.primary_color', '#12GG45')
            ->call('create')
            ->assertHasErrors(['data.primary_color']);
    }

    /**
     * Memastikan fallback default warna dan logo aktif saat konfigurasi kosong atau tidak valid.
     */
    public function test_resolve_active_theme_uses_safe_defaults_when_data_missing_or_invalid(): void
    {
        $resolvedWithoutConfig = app(ResolveActiveThemeSettingsAction::class)->execute();

        $this->assertTrue($resolvedWithoutConfig['is_fallback']);
        $this->assertSame(ThemeDefaults::palette()['primary'], $resolvedWithoutConfig['primary_color']);
        $this->assertSame(ThemeDefaults::palette()['secondary'], $resolvedWithoutConfig['secondary_color']);
        $this->assertSame(ThemeDefaults::palette()['accent'], $resolvedWithoutConfig['accent_color']);

        ThemeSetting::query()->create([
            'singleton_key' => 'default',
            'primary_color' => 'NOTHEX',
            'secondary_color' => null,
            'accent_color' => '#ZZZZZZ',
            'is_active' => true,
        ]);

        $resolvedWithInvalidConfig = app(ResolveActiveThemeSettingsAction::class)->execute();

        $this->assertSame(ThemeDefaults::palette()['primary'], $resolvedWithInvalidConfig['primary_color']);
        $this->assertSame(ThemeDefaults::palette()['secondary'], $resolvedWithInvalidConfig['secondary_color']);
        $this->assertSame(ThemeDefaults::palette()['accent'], $resolvedWithInvalidConfig['accent_color']);
    }

    /**
     * Memastikan policy theme mengizinkan super admin dan menolak role tanpa permission theme.*.
     */
    public function test_theme_policy_enforces_view_and_update_permissions(): void
    {
        $themeSetting = ThemeSetting::factory()->create();

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $guru = User::factory()->create();
        $guru->assignRole(RoleName::GURU);

        $this->assertTrue(Gate::forUser($superAdmin)->allows('viewAny', ThemeSetting::class));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('update', $themeSetting));

        $this->assertFalse(Gate::forUser($guru)->allows('viewAny', ThemeSetting::class));
        $this->assertFalse(Gate::forUser($guru)->allows('update', $themeSetting));
    }

    /**
     * Memastikan role tanpa permission theme.* ditolak backend saat mengakses halaman theme settings.
     */
    public function test_user_without_theme_permission_cannot_access_theme_pages(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole(RoleName::GURU);

        $this->actingAs($guru)
            ->get('/admin/theme-settings')
            ->assertForbidden();

        $this->actingAs($guru)
            ->get('/admin/theme-settings/create')
            ->assertForbidden();
    }

    private function createMediaAsset(int $userId, string $path): MediaAsset
    {
        Storage::disk('s3')->put($path, 'logo-content');

        return MediaAsset::query()->create([
            'disk' => 's3',
            'bucket' => 'cms-sekolah-media',
            'path' => $path,
            'file_name' => basename($path),
            'original_name' => basename($path),
            'extension' => pathinfo($path, PATHINFO_EXTENSION),
            'mime_type' => 'image/png',
            'size_bytes' => Storage::disk('s3')->size($path),
            'checksum' => hash('sha256', 'logo-content'),
            'visibility' => 'private',
            'uploaded_by' => $userId,
        ]);
    }
}
