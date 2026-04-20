<?php

declare(strict_types=1);

namespace Tests\Feature\News;

use App\Filament\Resources\News\Pages\CreateNews;
use App\Filament\Resources\News\Pages\ListNews;
use App\Models\User;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\News\Models\News;
use App\Modules\News\Support\NewsStatus;
use App\Modules\RolesPermissions\Support\RoleName;
use Database\Seeders\RolesPermissions\RolesAndPermissionsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class NewsModuleAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    /**
     * Memastikan create news berhasil, slug selalu unik, dan event create tercatat di audit log.
     */
    public function test_super_admin_can_create_news_with_unique_slug_and_audit_created_event(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $this->actingAs($superAdmin);

        Livewire::test(CreateNews::class)
            ->set('data.title', 'Kegiatan Sains Pekan Ini')
            ->set('data.slug', 'kegiatan-sains-pekan-ini')
            ->set('data.content', 'Konten berita pertama.')
            ->set('data.status', NewsStatus::DRAFT)
            ->call('create');

        Livewire::test(CreateNews::class)
            ->set('data.title', 'Kegiatan Sains Pekan Ini')
            ->set('data.slug', 'kegiatan-sains-pekan-ini')
            ->set('data.content', 'Konten berita kedua.')
            ->set('data.status', NewsStatus::DRAFT)
            ->call('create');

        $createdNews = News::query()->orderByDesc('id')->firstOrFail();

        $this->assertSame($superAdmin->id, $createdNews->author_id);
        $this->assertSame('kegiatan-sains-pekan-ini-2', $createdNews->slug);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::NEWS_CREATED,
            'module' => AuditModule::NEWS,
            'entity_type' => News::class,
            'entity_id' => (string) $createdNews->id,
            'actor_id' => $superAdmin->id,
        ]);
    }

    /**
     * Memastikan aksi publish/unpublish dari admin memutakhirkan status dan menghasilkan audit event yang sesuai.
     */
    public function test_publish_and_unpublish_actions_update_status_and_record_audit_events(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $news = News::factory()->create([
            'author_id' => $superAdmin->id,
            'status' => NewsStatus::DRAFT,
            'published_at' => null,
        ]);

        $this->actingAs($superAdmin);

        Livewire::test(ListNews::class)
            ->callTableAction('publish', $news);

        $news->refresh();

        $this->assertSame(NewsStatus::PUBLISHED, $news->status);
        $this->assertNotNull($news->published_at);
        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::NEWS_PUBLISHED,
            'module' => AuditModule::NEWS,
            'entity_type' => News::class,
            'entity_id' => (string) $news->id,
            'actor_id' => $superAdmin->id,
        ]);

        Livewire::test(ListNews::class)
            ->callTableAction('unpublish', $news);

        $news->refresh();

        $this->assertSame(NewsStatus::DRAFT, $news->status);
        $this->assertNull($news->published_at);
        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::NEWS_UNPUBLISHED,
            'module' => AuditModule::NEWS,
            'entity_type' => News::class,
            'entity_id' => (string) $news->id,
            'actor_id' => $superAdmin->id,
        ]);
    }

    /**
     * Memastikan policy update/delete own-vs-any dan policy publish diberlakukan server-side.
     */
    public function test_news_policy_enforces_own_any_and_publish_permissions(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole(RoleName::GURU);

        $otherGuru = User::factory()->create();
        $otherGuru->assignRole(RoleName::GURU);

        $ownNews = News::factory()->create([
            'author_id' => $guru->id,
        ]);

        $otherNews = News::factory()->create([
            'author_id' => $otherGuru->id,
        ]);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $this->assertTrue(Gate::forUser($guru)->allows('update', $ownNews));
        $this->assertTrue(Gate::forUser($guru)->allows('delete', $ownNews));

        $this->assertFalse(Gate::forUser($guru)->allows('update', $otherNews));
        $this->assertFalse(Gate::forUser($guru)->allows('delete', $otherNews));
        $this->assertFalse(Gate::forUser($guru)->allows('publish', $ownNews));

        $this->assertTrue(Gate::forUser($superAdmin)->allows('update', $otherNews));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('delete', $otherNews));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('publish', $otherNews));
    }

    /**
     * Memastikan user tanpa permission news.view ditolak backend saat akses resource news.
     */
    public function test_user_without_news_permission_cannot_access_news_pages(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole(RoleName::SISWA);

        $this->actingAs($siswa)
            ->get('/admin/news')
            ->assertForbidden();

        $this->actingAs($siswa)
            ->get('/admin/news/create')
            ->assertForbidden();
    }
}
