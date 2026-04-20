<?php

declare(strict_types=1);

namespace Tests\Feature\Galleries;

use App\Filament\Resources\Galleries\Pages\CreateGallery;
use App\Filament\Resources\Galleries\Pages\ListGalleries;
use App\Models\User;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\Galleries\Models\Gallery;
use App\Modules\MediaLibrary\Models\MediaAsset;
use App\Modules\RolesPermissions\Support\RoleName;
use Database\Seeders\RolesPermissions\RolesAndPermissionsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class GalleryModuleAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    /**
     * Memastikan admin dapat membuat gallery + item, slug unik, dan audit event utama tercatat.
     */
    public function test_super_admin_can_create_gallery_items_with_unique_slug_and_audit_logs(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $mediaA = $this->createMediaAsset('opening.jpg');
        $mediaB = $this->createMediaAsset('closing.jpg');

        $this->actingAs($superAdmin);

        Livewire::test(CreateGallery::class)
            ->set('data.title', 'Dokumentasi Class Meeting')
            ->set('data.slug', 'dokumentasi-class-meeting')
            ->set('data.description', 'Album dokumentasi kegiatan class meeting.')
            ->set('data.status', 'draft')
            ->set('data.items', [
                [
                    'media_asset_id' => $mediaA->id,
                    'caption' => 'Sesi pembukaan',
                    'alt_text' => 'Foto pembukaan',
                ],
                [
                    'media_asset_id' => $mediaB->id,
                    'caption' => 'Sesi penutupan',
                    'alt_text' => 'Foto penutupan',
                ],
            ])
            ->call('create');

        Livewire::test(CreateGallery::class)
            ->set('data.title', 'Dokumentasi Class Meeting')
            ->set('data.slug', 'dokumentasi-class-meeting')
            ->set('data.description', 'Album kedua')
            ->set('data.status', 'draft')
            ->call('create');

        $firstGallery = Gallery::query()->where('slug', 'dokumentasi-class-meeting')->firstOrFail();
        $secondGallery = Gallery::query()->where('slug', 'dokumentasi-class-meeting-2')->firstOrFail();

        $this->assertSame($superAdmin->id, $firstGallery->author_id);
        $this->assertCount(2, $firstGallery->items);
        $this->assertSame([1, 2], $firstGallery->items->pluck('sort_order')->all());
        $this->assertSame('dokumentasi-class-meeting-2', $secondGallery->slug);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::GALLERY_CREATED,
            'module' => AuditModule::GALLERIES,
            'entity_type' => Gallery::class,
            'entity_id' => (string) $firstGallery->id,
            'actor_id' => $superAdmin->id,
        ]);

        $this->assertDatabaseCount('audit_logs', 4);
    }

    /**
     * Memastikan publish/unpublish gallery berjalan dan tercatat di audit log.
     */
    public function test_publish_and_unpublish_actions_update_status_and_record_audit_events(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $gallery = Gallery::factory()->create([
            'author_id' => $superAdmin->id,
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->actingAs($superAdmin);

        Livewire::test(ListGalleries::class)
            ->callTableAction('publish', $gallery);

        $gallery->refresh();

        $this->assertSame('published', $gallery->status);
        $this->assertNotNull($gallery->published_at);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::GALLERY_PUBLISHED,
            'module' => AuditModule::GALLERIES,
            'entity_type' => Gallery::class,
            'entity_id' => (string) $gallery->id,
            'actor_id' => $superAdmin->id,
        ]);

        Livewire::test(ListGalleries::class)
            ->callTableAction('unpublish', $gallery);

        $gallery->refresh();

        $this->assertSame('draft', $gallery->status);
        $this->assertNull($gallery->published_at);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::GALLERY_UNPUBLISHED,
            'module' => AuditModule::GALLERIES,
            'entity_type' => Gallery::class,
            'entity_id' => (string) $gallery->id,
            'actor_id' => $superAdmin->id,
        ]);
    }

    /**
     * Memastikan update/delete own-vs-any dan publish permission diproteksi policy backend.
     */
    public function test_gallery_policy_enforces_own_any_and_publish_permissions(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole(RoleName::GURU);

        $otherGuru = User::factory()->create();
        $otherGuru->assignRole(RoleName::GURU);

        $ownGallery = Gallery::factory()->create([
            'author_id' => $guru->id,
        ]);

        $otherGallery = Gallery::factory()->create([
            'author_id' => $otherGuru->id,
        ]);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $this->assertTrue(Gate::forUser($guru)->allows('update', $ownGallery));
        $this->assertTrue(Gate::forUser($guru)->allows('delete', $ownGallery));
        $this->assertFalse(Gate::forUser($guru)->allows('publish', $ownGallery));

        $this->assertFalse(Gate::forUser($guru)->allows('update', $otherGallery));
        $this->assertFalse(Gate::forUser($guru)->allows('delete', $otherGallery));

        $this->assertTrue(Gate::forUser($superAdmin)->allows('update', $otherGallery));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('delete', $otherGallery));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('publish', $otherGallery));
    }

    /**
     * Memastikan user tanpa permission galleries.view ditolak backend saat akses halaman galleries.
     */
    public function test_user_without_gallery_permissions_cannot_access_pages(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole(RoleName::SISWA);

        $this->actingAs($siswa)
            ->get('/admin/galleries')
            ->assertForbidden();

        $this->actingAs($siswa)
            ->get('/admin/galleries/create')
            ->assertForbidden();
    }

    /**
     * Memastikan urutan item stabil setelah reorder dan event reorder tercatat.
     */
    public function test_gallery_item_sorting_is_stable_after_reorder(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $mediaA = $this->createMediaAsset('item-a.jpg');
        $mediaB = $this->createMediaAsset('item-b.jpg');

        $gallery = Gallery::factory()->create([
            'author_id' => $superAdmin->id,
        ]);

        $gallery->items()->createMany([
            [
                'media_asset_id' => $mediaA->id,
                'sort_order' => 1,
                'caption' => 'Item A',
            ],
            [
                'media_asset_id' => $mediaB->id,
                'sort_order' => 2,
                'caption' => 'Item B',
            ],
        ]);

        $gallery->load('items');

        $firstItem = $gallery->items[0];
        $secondItem = $gallery->items[1];

        $this->actingAs($superAdmin);
        $firstItem->update([
            'sort_order' => 3,
        ]);

        $gallery->refresh();
        $orderedMediaIds = $gallery->items()->pluck('media_asset_id')->all();
        $orderedSortValues = $gallery->items()->pluck('sort_order')->all();

        $this->assertSame([$mediaB->id, $mediaA->id], $orderedMediaIds);
        $this->assertSame([1, 2], $orderedSortValues);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::GALLERY_ITEMS_REORDERED,
            'module' => AuditModule::GALLERIES,
            'entity_type' => Gallery::class,
            'entity_id' => (string) $gallery->id,
            'actor_id' => $superAdmin->id,
        ]);
    }

    /**
     * Memastikan hanya gallery berstatus published yang masuk scope publik.
     */
    public function test_only_published_gallery_is_visible_for_public_scope(): void
    {
        Gallery::factory()->published()->create([
            'title' => 'Galeri Published',
        ]);

        Gallery::factory()->create([
            'title' => 'Galeri Draft',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $publicGalleryTitles = Gallery::query()
            ->published()
            ->pluck('title')
            ->all();

        $this->assertContains('Galeri Published', $publicGalleryTitles);
        $this->assertNotContains('Galeri Draft', $publicGalleryTitles);
    }

    private function createMediaAsset(string $fileName): MediaAsset
    {
        return MediaAsset::query()->create([
            'disk' => 's3',
            'bucket' => 'cms-sekolah-media',
            'path' => 'galleries/'.Str::uuid().'-'.$fileName,
            'file_name' => $fileName,
            'original_name' => $fileName,
            'extension' => pathinfo($fileName, PATHINFO_EXTENSION),
            'mime_type' => 'image/jpeg',
            'size_bytes' => 1200,
            'checksum' => hash('sha256', $fileName),
            'visibility' => 'private',
            'uploaded_by' => null,
        ]);
    }
}
