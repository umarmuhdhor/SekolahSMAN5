<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use App\Modules\Announcements\Models\Announcement;
use App\Modules\Announcements\Support\AnnouncementStatus;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAnnouncementPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_announcement_listing_only_renders_items_visible_on_public(): void
    {
        CarbonImmutable::setTestNow('2026-04-21 09:00:00');

        try {
            $alwaysVisible = Announcement::factory()->published()->create([
                'title' => 'Perubahan Jadwal Upacara',
                'slug' => 'perubahan-jadwal-upacara',
                'publish_start_at' => null,
                'publish_end_at' => null,
            ]);

            $windowVisible = Announcement::factory()->published()->create([
                'title' => 'Pendaftaran Ekstrakurikuler Dibuka',
                'slug' => 'pendaftaran-ekstrakurikuler-dibuka',
                'publish_start_at' => now()->subHour(),
                'publish_end_at' => now()->addHour(),
            ]);

            Announcement::factory()->published()->create([
                'title' => 'Info Ujian Akhir',
                'slug' => 'info-ujian-akhir',
                'publish_start_at' => now()->addHour(),
                'publish_end_at' => now()->addHours(2),
            ]);

            Announcement::factory()->published()->create([
                'title' => 'Pengumuman Lama',
                'slug' => 'pengumuman-lama',
                'publish_start_at' => now()->subHours(4),
                'publish_end_at' => now()->subHour(),
            ]);

            Announcement::factory()->create([
                'title' => 'Draft Internal',
                'slug' => 'draft-internal',
                'status' => AnnouncementStatus::DRAFT,
                'published_at' => null,
                'publish_start_at' => null,
                'publish_end_at' => null,
            ]);

            Announcement::factory()->create([
                'title' => 'Arsip Semester Lalu',
                'slug' => 'arsip-semester-lalu',
                'status' => AnnouncementStatus::ARCHIVED,
                'published_at' => now(),
                'publish_start_at' => null,
                'publish_end_at' => null,
            ]);

            Announcement::factory()->create([
                'title' => 'Published Tanpa Tanggal',
                'slug' => 'published-tanpa-tanggal',
                'status' => AnnouncementStatus::PUBLISHED,
                'published_at' => null,
                'publish_start_at' => null,
                'publish_end_at' => null,
            ]);

            $response = $this->get(route('public.announcements.index'));

            $response->assertOk();
            $response->assertSee('Perubahan Jadwal Upacara');
            $response->assertSee('Pendaftaran Ekstrakurikuler Dibuka');
            $response->assertSee(route('public.announcements.show', ['slug' => $alwaysVisible->slug]), false);
            $response->assertSee(route('public.announcements.show', ['slug' => $windowVisible->slug]), false);
            $response->assertDontSee('Info Ujian Akhir');
            $response->assertDontSee('Pengumuman Lama');
            $response->assertDontSee('Draft Internal');
            $response->assertDontSee('Arsip Semester Lalu');
            $response->assertDontSee('Published Tanpa Tanggal');
        } finally {
            CarbonImmutable::setTestNow();
        }
    }

    public function test_announcement_listing_shows_empty_state_when_no_item_is_visible(): void
    {
        Announcement::factory()->count(2)->create([
            'status' => AnnouncementStatus::DRAFT,
            'published_at' => null,
        ]);

        $this->get(route('public.announcements.index'))
            ->assertOk()
            ->assertSee('Belum ada pengumuman yang sedang tayang.');
    }

    public function test_announcement_detail_renders_for_visible_slug(): void
    {
        CarbonImmutable::setTestNow('2026-04-21 11:00:00');

        try {
            $announcement = Announcement::factory()->published()->create([
                'title' => 'Jadwal Pembagian Rapor',
                'slug' => 'jadwal-pembagian-rapor',
                'excerpt' => 'Rapor dibagikan hari Jumat pukul 09.00.',
                'content' => "Pembagian rapor semester genap.\nWali murid dimohon hadir.",
                'publish_start_at' => now()->subDay(),
                'publish_end_at' => now()->addDay(),
            ]);

            $response = $this->get(route('public.announcements.show', ['slug' => $announcement->slug]));

            $response->assertOk();
            $response->assertSee('Jadwal Pembagian Rapor');
            $response->assertSee('Rapor dibagikan hari Jumat pukul 09.00.');
            $response->assertSee('Pembagian rapor semester genap.');
            $response->assertSee(route('public.announcements.index'), false);
        } finally {
            CarbonImmutable::setTestNow();
        }
    }

    public function test_announcement_detail_returns_not_found_for_non_visible_slug(): void
    {
        CarbonImmutable::setTestNow('2026-04-21 09:00:00');

        try {
            $draft = Announcement::factory()->create([
                'slug' => 'pengumuman-draft',
                'status' => AnnouncementStatus::DRAFT,
                'published_at' => null,
            ]);

            $futureWindow = Announcement::factory()->published()->create([
                'slug' => 'pengumuman-belum-mulai',
                'publish_start_at' => now()->addHour(),
                'publish_end_at' => now()->addHours(2),
            ]);

            $expiredWindow = Announcement::factory()->published()->create([
                'slug' => 'pengumuman-sudah-lewat',
                'publish_start_at' => now()->subHours(2),
                'publish_end_at' => now()->subHour(),
            ]);

            $publishedWithoutDate = Announcement::factory()->create([
                'slug' => 'pengumuman-tanpa-published-at',
                'status' => AnnouncementStatus::PUBLISHED,
                'published_at' => null,
                'publish_start_at' => null,
                'publish_end_at' => null,
            ]);

            $this->get(route('public.announcements.show', ['slug' => $draft->slug]))->assertNotFound();
            $this->get(route('public.announcements.show', ['slug' => $futureWindow->slug]))->assertNotFound();
            $this->get(route('public.announcements.show', ['slug' => $expiredWindow->slug]))->assertNotFound();
            $this->get(route('public.announcements.show', ['slug' => $publishedWithoutDate->slug]))->assertNotFound();
            $this->get(route('public.announcements.show', ['slug' => 'slug-tidak-ditemukan']))->assertNotFound();
        } finally {
            CarbonImmutable::setTestNow();
        }
    }

    public function test_announcement_listing_is_paginated_with_nine_items_per_page(): void
    {
        CarbonImmutable::setTestNow('2026-04-21 13:00:00');

        try {
            for ($index = 1; $index <= 12; $index++) {
                Announcement::factory()->published()->create([
                    'title' => sprintf('Pengumuman Publik #%02d', $index),
                    'slug' => sprintf('pengumuman-publik-%02d', $index),
                    'published_at' => now()->subMinutes(12 - $index),
                    'publish_start_at' => now()->subDay(),
                    'publish_end_at' => now()->addDay(),
                ]);
            }

            $firstPage = $this->get(route('public.announcements.index'));

            $firstPage->assertOk();
            $firstPage->assertSee('Menampilkan 1-9 dari 12 pengumuman.');
            $firstPage->assertSee('Pengumuman Publik #12');
            $firstPage->assertDontSee('Pengumuman Publik #01');
            $firstPage->assertDontSee(route('public.announcements.show', ['slug' => 'pengumuman-publik-01']), false);

            $secondPage = $this->get(route('public.announcements.index', ['page' => 2]));

            $secondPage->assertOk();
            $secondPage->assertSee('Menampilkan 10-12 dari 12 pengumuman.');
            $secondPage->assertSee('Pengumuman Publik #01');
            $secondPage->assertDontSee('Pengumuman Publik #12');
            $secondPage->assertSee(route('public.announcements.show', ['slug' => 'pengumuman-publik-01']), false);
        } finally {
            CarbonImmutable::setTestNow();
        }
    }
}
