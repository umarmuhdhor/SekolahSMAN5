<?php

declare(strict_types=1);

namespace Tests\Feature\QA;

use App\Models\User;
use App\Modules\Announcements\Models\Announcement;
use App\Modules\Announcements\Support\AnnouncementStatus;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\Galleries\Models\Gallery;
use App\Modules\Galleries\Support\GalleryStatus;
use App\Modules\News\Models\News;
use App\Modules\News\Support\NewsStatus;
use App\Modules\RolesPermissions\Support\RoleName;
use Database\Seeders\RolesPermissions\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QAHardeningAuthPermissionAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_is_redirected_to_admin_login_from_critical_admin_routes(): void
    {
        $criticalRoutes = [
            '/admin/news',
            '/admin/announcements',
            '/admin/galleries',
            '/admin/school-profile',
            '/admin/theme-settings',
            '/admin/navigation-menus',
            '/admin/audit-logs',
        ];

        foreach ($criticalRoutes as $route) {
            $response = $this->get($route);

            $response->assertStatus(302);
            $this->assertStringContainsString('/admin/login', (string) $response->headers->get('Location'));
        }
    }

    public function test_siswa_role_is_forbidden_from_content_management_routes_across_modules(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole(RoleName::SISWA);

        $news = News::factory()->create();
        $announcement = Announcement::factory()->create();
        $gallery = Gallery::factory()->create();

        $this->actingAs($siswa)
            ->get('/admin/news')
            ->assertForbidden();

        $this->actingAs($siswa)
            ->get('/admin/news/'.$news->id.'/edit')
            ->assertForbidden();

        $this->actingAs($siswa)
            ->get('/admin/announcements')
            ->assertForbidden();

        $this->actingAs($siswa)
            ->get('/admin/announcements/'.$announcement->id.'/edit')
            ->assertForbidden();

        $this->actingAs($siswa)
            ->get('/admin/galleries')
            ->assertForbidden();

        $this->actingAs($siswa)
            ->get('/admin/galleries/'.$gallery->id.'/edit')
            ->assertForbidden();
    }

    public function test_public_routes_do_not_leak_unpublished_or_out_of_window_content_across_modules(): void
    {
        $publishedNews = News::factory()->published()->create([
            'title' => 'News Published QA',
            'slug' => 'news-published-qa',
        ]);

        $draftNews = News::factory()->create([
            'title' => 'News Draft QA',
            'slug' => 'news-draft-qa',
            'status' => NewsStatus::DRAFT,
            'published_at' => null,
        ]);

        $publishedAnnouncement = Announcement::factory()->published()->create([
            'title' => 'Announcement Published QA',
            'slug' => 'announcement-published-qa',
            'publish_start_at' => now()->subHour(),
            'publish_end_at' => now()->addHour(),
        ]);

        $futureAnnouncement = Announcement::factory()->published()->create([
            'title' => 'Announcement Future QA',
            'slug' => 'announcement-future-qa',
            'publish_start_at' => now()->addHour(),
            'publish_end_at' => now()->addHours(2),
        ]);

        $publishedGallery = Gallery::factory()->published()->create([
            'title' => 'Gallery Published QA',
            'slug' => 'gallery-published-qa',
        ]);

        $archivedGallery = Gallery::factory()->create([
            'title' => 'Gallery Archived QA',
            'slug' => 'gallery-archived-qa',
            'status' => GalleryStatus::ARCHIVED,
            'published_at' => now(),
        ]);

        $this->get(route('public.news.index'))
            ->assertOk()
            ->assertSee('News Published QA')
            ->assertDontSee('News Draft QA');

        $this->get(route('public.news.show', ['slug' => $publishedNews->slug]))
            ->assertOk();

        $this->get(route('public.news.show', ['slug' => $draftNews->slug]))
            ->assertNotFound();

        $this->get(route('public.announcements.index'))
            ->assertOk()
            ->assertSee('Announcement Published QA')
            ->assertDontSee('Announcement Future QA');

        $this->get(route('public.announcements.show', ['slug' => $publishedAnnouncement->slug]))
            ->assertOk();

        $this->get(route('public.announcements.show', ['slug' => $futureAnnouncement->slug]))
            ->assertNotFound();

        $this->get(route('public.galleries.index'))
            ->assertOk()
            ->assertSee('Gallery Published QA')
            ->assertDontSee('Gallery Archived QA');

        $this->get(route('public.galleries.show', ['slug' => $publishedGallery->slug]))
            ->assertOk();

        $this->get(route('public.galleries.show', ['slug' => $archivedGallery->slug]))
            ->assertNotFound();
    }

    public function test_audit_lifecycle_events_are_complete_for_content_modules(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $this->actingAs($superAdmin);

        $news = News::factory()->create([
            'author_id' => $superAdmin->id,
            'status' => NewsStatus::DRAFT,
            'published_at' => null,
        ]);

        $news->update([
            'status' => NewsStatus::PUBLISHED,
            'published_at' => now(),
        ]);

        $news->update([
            'status' => NewsStatus::DRAFT,
            'published_at' => null,
        ]);

        $newsId = $news->id;
        $news->delete();

        $announcement = Announcement::factory()->create([
            'author_id' => $superAdmin->id,
            'status' => AnnouncementStatus::DRAFT,
            'published_at' => null,
            'publish_start_at' => null,
            'publish_end_at' => null,
        ]);

        $announcement->update([
            'status' => AnnouncementStatus::PUBLISHED,
            'published_at' => now(),
        ]);

        $announcement->update([
            'status' => AnnouncementStatus::DRAFT,
            'published_at' => null,
        ]);

        $announcementId = $announcement->id;
        $announcement->delete();

        $gallery = Gallery::factory()->create([
            'author_id' => $superAdmin->id,
            'status' => GalleryStatus::DRAFT,
            'published_at' => null,
        ]);

        $gallery->update([
            'status' => GalleryStatus::PUBLISHED,
            'published_at' => now(),
        ]);

        $gallery->update([
            'status' => GalleryStatus::DRAFT,
            'published_at' => null,
        ]);

        $galleryId = $gallery->id;
        $gallery->delete();

        $this->assertAuditExists(AuditEventType::NEWS_CREATED, AuditModule::NEWS, News::class, $newsId, $superAdmin->id);
        $this->assertAuditExists(AuditEventType::NEWS_PUBLISHED, AuditModule::NEWS, News::class, $newsId, $superAdmin->id);
        $this->assertAuditExists(AuditEventType::NEWS_UNPUBLISHED, AuditModule::NEWS, News::class, $newsId, $superAdmin->id);
        $this->assertAuditExists(AuditEventType::NEWS_DELETED, AuditModule::NEWS, News::class, $newsId, $superAdmin->id);

        $this->assertAuditExists(AuditEventType::ANNOUNCEMENT_CREATED, AuditModule::ANNOUNCEMENTS, Announcement::class, $announcementId, $superAdmin->id);
        $this->assertAuditExists(AuditEventType::ANNOUNCEMENT_PUBLISHED, AuditModule::ANNOUNCEMENTS, Announcement::class, $announcementId, $superAdmin->id);
        $this->assertAuditExists(AuditEventType::ANNOUNCEMENT_UNPUBLISHED, AuditModule::ANNOUNCEMENTS, Announcement::class, $announcementId, $superAdmin->id);
        $this->assertAuditExists(AuditEventType::ANNOUNCEMENT_DELETED, AuditModule::ANNOUNCEMENTS, Announcement::class, $announcementId, $superAdmin->id);

        $this->assertAuditExists(AuditEventType::GALLERY_CREATED, AuditModule::GALLERIES, Gallery::class, $galleryId, $superAdmin->id);
        $this->assertAuditExists(AuditEventType::GALLERY_PUBLISHED, AuditModule::GALLERIES, Gallery::class, $galleryId, $superAdmin->id);
        $this->assertAuditExists(AuditEventType::GALLERY_UNPUBLISHED, AuditModule::GALLERIES, Gallery::class, $galleryId, $superAdmin->id);
        $this->assertAuditExists(AuditEventType::GALLERY_DELETED, AuditModule::GALLERIES, Gallery::class, $galleryId, $superAdmin->id);
    }

    private function assertAuditExists(string $eventType, string $module, string $entityType, int $entityId, int $actorId): void
    {
        $this->assertDatabaseHas('audit_logs', [
            'event_type' => $eventType,
            'module' => $module,
            'entity_type' => $entityType,
            'entity_id' => (string) $entityId,
            'actor_id' => $actorId,
        ]);
    }
}
