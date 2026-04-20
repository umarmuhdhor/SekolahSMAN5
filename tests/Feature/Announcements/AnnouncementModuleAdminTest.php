<?php

declare(strict_types=1);

namespace Tests\Feature\Announcements;

use App\Filament\Resources\Announcements\Pages\CreateAnnouncement;
use App\Filament\Resources\Announcements\Pages\ListAnnouncements;
use App\Models\User;
use App\Modules\Announcements\Models\Announcement;
use App\Modules\Announcements\Support\AnnouncementStatus;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\RolesPermissions\Support\RoleName;
use Carbon\CarbonImmutable;
use Database\Seeders\RolesPermissions\RolesAndPermissionsSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class AnnouncementModuleAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    /**
     * Memastikan create announcement berhasil, slug unik server-side, dan audit created tercatat.
     */
    public function test_super_admin_can_create_announcement_with_unique_slug_and_audit_created_event(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $this->actingAs($superAdmin);

        Livewire::test(CreateAnnouncement::class)
            ->set('data.title', 'Libur Nasional Pekan Depan')
            ->set('data.slug', 'libur-nasional-pekan-depan')
            ->set('data.content', 'Sekolah libur pada hari Senin pekan depan.')
            ->set('data.status', AnnouncementStatus::DRAFT)
            ->call('create');

        Livewire::test(CreateAnnouncement::class)
            ->set('data.title', 'Libur Nasional Pekan Depan')
            ->set('data.slug', 'libur-nasional-pekan-depan')
            ->set('data.content', 'Pengingat kedua untuk pengumuman libur.')
            ->set('data.status', AnnouncementStatus::DRAFT)
            ->call('create');

        $announcement = Announcement::query()->orderByDesc('id')->firstOrFail();

        $this->assertSame($superAdmin->id, $announcement->author_id);
        $this->assertSame('libur-nasional-pekan-depan-2', $announcement->slug);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::ANNOUNCEMENT_CREATED,
            'module' => AuditModule::ANNOUNCEMENTS,
            'entity_type' => Announcement::class,
            'entity_id' => (string) $announcement->id,
            'actor_id' => $superAdmin->id,
        ]);
    }

    /**
     * Memastikan validasi publish window menolak publish_end_at sebelum publish_start_at.
     */
    public function test_publish_window_validation_rejects_invalid_range(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $this->actingAs($superAdmin);

        Livewire::test(CreateAnnouncement::class)
            ->set('data.title', 'Jadwal Tryout')
            ->set('data.content', 'Tryout dilaksanakan sesuai jadwal.')
            ->set('data.publish_start_at', now()->addDays(2)->format('Y-m-d H:i:s'))
            ->set('data.publish_end_at', now()->addDay()->format('Y-m-d H:i:s'))
            ->call('create')
            ->assertHasErrors(['data.publish_end_at']);
    }

    /**
     * Memastikan aksi publish/unpublish memutakhirkan status dan mencatat audit event.
     */
    public function test_publish_and_unpublish_actions_record_audit_events(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $announcement = Announcement::factory()->create([
            'author_id' => $superAdmin->id,
            'status' => AnnouncementStatus::DRAFT,
            'published_at' => null,
        ]);

        $this->actingAs($superAdmin);

        Livewire::test(ListAnnouncements::class)
            ->callTableAction('publish', $announcement);

        $announcement->refresh();

        $this->assertSame(AnnouncementStatus::PUBLISHED, $announcement->status);
        $this->assertNotNull($announcement->published_at);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::ANNOUNCEMENT_PUBLISHED,
            'module' => AuditModule::ANNOUNCEMENTS,
            'entity_type' => Announcement::class,
            'entity_id' => (string) $announcement->id,
            'actor_id' => $superAdmin->id,
        ]);

        Livewire::test(ListAnnouncements::class)
            ->callTableAction('unpublish', $announcement);

        $announcement->refresh();

        $this->assertSame(AnnouncementStatus::DRAFT, $announcement->status);
        $this->assertNull($announcement->published_at);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => AuditEventType::ANNOUNCEMENT_UNPUBLISHED,
            'module' => AuditModule::ANNOUNCEMENTS,
            'entity_type' => Announcement::class,
            'entity_id' => (string) $announcement->id,
            'actor_id' => $superAdmin->id,
        ]);
    }

    /**
     * Memastikan policy own-vs-any dan publish permission diberlakukan server-side.
     */
    public function test_announcement_policy_enforces_own_any_and_publish_permissions(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole(RoleName::GURU);

        $otherGuru = User::factory()->create();
        $otherGuru->assignRole(RoleName::GURU);

        $ownAnnouncement = Announcement::factory()->create([
            'author_id' => $guru->id,
        ]);

        $otherAnnouncement = Announcement::factory()->create([
            'author_id' => $otherGuru->id,
        ]);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole(RoleName::SUPER_ADMIN);

        $this->assertTrue(Gate::forUser($guru)->allows('update', $ownAnnouncement));
        $this->assertTrue(Gate::forUser($guru)->allows('delete', $ownAnnouncement));
        $this->assertFalse(Gate::forUser($guru)->allows('publish', $ownAnnouncement));

        $this->assertFalse(Gate::forUser($guru)->allows('update', $otherAnnouncement));
        $this->assertFalse(Gate::forUser($guru)->allows('delete', $otherAnnouncement));

        $this->assertTrue(Gate::forUser($superAdmin)->allows('update', $otherAnnouncement));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('delete', $otherAnnouncement));
        $this->assertTrue(Gate::forUser($superAdmin)->allows('publish', $otherAnnouncement));
    }

    /**
     * Memastikan user tanpa permission announcements.view ditolak backend saat akses modul.
     */
    public function test_user_without_announcements_permission_cannot_access_pages(): void
    {
        $siswa = User::factory()->create();
        $siswa->assignRole(RoleName::SISWA);

        $this->actingAs($siswa)
            ->get('/admin/announcements')
            ->assertForbidden();

        $this->actingAs($siswa)
            ->get('/admin/announcements/create')
            ->assertForbidden();
    }

    /**
     * Memastikan scope visibilitas publik mengikuti status publish dan publish window.
     */
    public function test_public_visibility_scope_respects_publish_window(): void
    {
        CarbonImmutable::setTestNow('2026-04-20 09:00:00');

        try {
            Announcement::factory()->published()->create([
                'title' => 'Tanpa Window',
                'publish_start_at' => null,
                'publish_end_at' => null,
            ]);

            Announcement::factory()->published()->create([
                'title' => 'Window Aktif',
                'publish_start_at' => now()->subHour(),
                'publish_end_at' => now()->addHour(),
            ]);

            Announcement::factory()->published()->create([
                'title' => 'Belum Mulai',
                'publish_start_at' => now()->addHour(),
                'publish_end_at' => now()->addHours(2),
            ]);

            Announcement::factory()->published()->create([
                'title' => 'Sudah Lewat',
                'publish_start_at' => now()->subHours(3),
                'publish_end_at' => now()->subHour(),
            ]);

            Announcement::factory()->create([
                'title' => 'Masih Draft',
                'status' => AnnouncementStatus::DRAFT,
                'published_at' => null,
                'publish_start_at' => null,
                'publish_end_at' => null,
            ]);

            $visibleTitles = Announcement::query()
                ->visibleOnPublic()
                ->pluck('title')
                ->all();

            $this->assertContains('Tanpa Window', $visibleTitles);
            $this->assertContains('Window Aktif', $visibleTitles);
            $this->assertNotContains('Belum Mulai', $visibleTitles);
            $this->assertNotContains('Sudah Lewat', $visibleTitles);
            $this->assertNotContains('Masih Draft', $visibleTitles);
        } finally {
            CarbonImmutable::setTestNow();
        }
    }
}
