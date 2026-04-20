<?php

declare(strict_types=1);

namespace Tests\Feature\MediaLibrary;

use App\Filament\Resources\MediaAssets\MediaAssetResource;
use App\Filament\Resources\MediaAssets\Pages\CreateMediaAsset;
use App\Models\User;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\MediaLibrary\Models\MediaAsset;
use App\Modules\RolesPermissions\Support\RoleName;
use Database\Seeders\RolesPermissions\RolesAndPermissionsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MediaLibraryFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        config()->set('media_library.disk', 's3');
        config()->set('filesystems.default', 's3');
        Storage::fake('s3');

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    /**
     * Memastikan upload media valid berhasil, metadata tersimpan, dan audit upload tercatat.
     */
    public function test_super_admin_can_upload_media_and_store_metadata(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $this->actingAs($superAdmin);

        Livewire::test(CreateMediaAsset::class)
            ->set('data.path', UploadedFile::fake()->image('banner-home.jpg'))
            ->set('data.alt_text', 'Banner halaman depan')
            ->set('data.caption', 'Digunakan untuk hero section website.')
            ->call('create');

        $media = MediaAsset::query()->firstOrFail();

        $this->assertSame($superAdmin->id, $media->uploaded_by);
        $this->assertSame('s3', $media->disk);
        $this->assertNotEmpty($media->checksum);
        $this->assertSame(64, strlen($media->checksum));
        Storage::disk('s3')->assertExists($media->path);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::MEDIA_UPLOAD,
            'module' => AuditModule::MEDIA_LIBRARY,
            'entity_type' => MediaAsset::class,
            'entity_id' => (string) $media->id,
            'actor_id' => $superAdmin->id,
        ]);
    }

    /**
     * Memastikan validasi MIME whitelist menolak file yang tidak diizinkan.
     */
    public function test_upload_rejects_disallowed_mime_type(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $this->actingAs($superAdmin);

        Livewire::test(CreateMediaAsset::class)
            ->set('data.path', UploadedFile::fake()->create('notes.txt', 12, 'text/plain'))
            ->call('create')
            ->assertHasErrors(['data.path']);
    }

    /**
     * Memastikan replace media aman: file lama dibersihkan dan audit replace tercatat.
     */
    public function test_replacing_media_file_deletes_old_file_and_records_audit(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        Storage::disk('s3')->put('media-library/original-photo.jpg', 'original-file-content');

        $media = MediaAsset::query()->create([
            'disk' => 's3',
            'bucket' => 'cms-sekolah-media',
            'path' => 'media-library/original-photo.jpg',
            'file_name' => 'original-photo.jpg',
            'original_name' => 'original-photo.jpg',
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
            'size_bytes' => Storage::disk('s3')->size('media-library/original-photo.jpg'),
            'checksum' => hash('sha256', 'original-file-content'),
            'visibility' => 'private',
            'uploaded_by' => $superAdmin->id,
        ]);

        $this->actingAs($superAdmin);

        $newPath = 'media-library/replacement-photo.jpg';
        Storage::disk('s3')->put($newPath, 'replacement-file-content');

        $media->update([
            'path' => $newPath,
            'file_name' => 'replacement-photo.jpg',
            'original_name' => 'replacement-photo.jpg',
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
            'size_bytes' => Storage::disk('s3')->size($newPath),
            'checksum' => hash('sha256', 'replacement-file-content'),
        ]);

        $media->refresh();

        $this->assertNotSame('media-library/original-photo.jpg', $media->path);
        Storage::disk('s3')->assertMissing('media-library/original-photo.jpg');
        Storage::disk('s3')->assertExists($media->path);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::MEDIA_REPLACE,
            'module' => AuditModule::MEDIA_LIBRARY,
            'entity_type' => MediaAsset::class,
            'entity_id' => (string) $media->id,
            'actor_id' => $superAdmin->id,
        ]);
    }

    /**
     * Memastikan delete media membersihkan object storage dan mencatat audit delete.
     */
    public function test_deleting_media_removes_file_and_records_audit(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        Storage::disk('s3')->put('media-library/deletable.pdf', 'pdf-content');

        $media = MediaAsset::query()->create([
            'disk' => 's3',
            'bucket' => 'cms-sekolah-media',
            'path' => 'media-library/deletable.pdf',
            'file_name' => 'deletable.pdf',
            'original_name' => 'deletable.pdf',
            'extension' => 'pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => Storage::disk('s3')->size('media-library/deletable.pdf'),
            'checksum' => hash('sha256', 'pdf-content'),
            'visibility' => 'private',
            'uploaded_by' => $superAdmin->id,
        ]);

        $this->actingAs($superAdmin);
        $media->delete();

        Storage::disk('s3')->assertMissing('media-library/deletable.pdf');

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::MEDIA_DELETE,
            'module' => AuditModule::MEDIA_LIBRARY,
            'entity_type' => MediaAsset::class,
            'entity_id' => (string) $media->id,
            'actor_id' => $superAdmin->id,
        ]);
    }

    /**
     * Memastikan akses media library di panel admin mengikuti role/permission baseline.
     */
    public function test_media_library_access_is_guarded_by_permission(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole(RoleName::GURU);

        $siswa = User::factory()->create();
        $siswa->assignRole(RoleName::SISWA);

        $this->actingAs($guru);
        $this->assertTrue(MediaAssetResource::canAccess());

        $this->actingAs($siswa);
        $this->assertFalse(MediaAssetResource::canAccess());

        $this->actingAs($siswa)
            ->get('/admin/media-assets')
            ->assertForbidden();
    }
}
