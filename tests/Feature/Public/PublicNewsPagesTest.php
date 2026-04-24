<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use App\Modules\News\Models\News;
use App\Modules\News\Support\NewsStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicNewsPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_listing_only_renders_published_items(): void
    {
        $published = News::factory()->published()->create([
            'title' => 'Prestasi Olimpiade Nasional',
            'slug' => 'prestasi-olimpiade-nasional',
            'excerpt' => 'Siswa SMA Dinamis meraih medali emas.',
        ]);

        News::factory()->create([
            'title' => 'Rapat Internal Guru',
            'slug' => 'rapat-internal-guru',
            'status' => NewsStatus::DRAFT,
            'published_at' => null,
        ]);

        News::factory()->create([
            'title' => 'Arsip Event Tahun Lalu',
            'slug' => 'arsip-event-tahun-lalu',
            'status' => NewsStatus::ARCHIVED,
            'published_at' => now(),
        ]);

        News::factory()->create([
            'title' => 'Belum Dijadwalkan Publish',
            'slug' => 'belum-dijadwalkan-publish',
            'status' => NewsStatus::PUBLISHED,
            'published_at' => null,
        ]);

        $response = $this->get(route('public.news.index'));

        $response->assertOk();
        $response->assertSee('Prestasi Olimpiade Nasional');
        $response->assertSee(route('public.news.show', ['slug' => $published->slug]), false);
        $response->assertDontSee('Rapat Internal Guru');
        $response->assertDontSee('Arsip Event Tahun Lalu');
        $response->assertDontSee('Belum Dijadwalkan Publish');
    }

    public function test_news_listing_shows_empty_state_when_published_news_is_absent(): void
    {
        News::factory()->count(2)->create([
            'status' => NewsStatus::DRAFT,
            'published_at' => null,
        ]);

        $this->get(route('public.news.index'))
            ->assertOk()
            ->assertSee('Belum ada berita yang dipublikasikan.');
    }

    public function test_news_detail_renders_for_published_slug(): void
    {
        $news = News::factory()->published()->create([
            'title' => 'Workshop Robotik Untuk Siswa',
            'slug' => 'workshop-robotik-untuk-siswa',
            'excerpt' => 'Program praktik robotik untuk kelas XI.',
            'content' => "Agenda workshop robotik.\nDilaksanakan di laboratorium teknologi.",
        ]);

        $response = $this->get(route('public.news.show', ['slug' => $news->slug]));

        $response->assertOk();
        $response->assertSee('Workshop Robotik Untuk Siswa');
        $response->assertSee('Program praktik robotik untuk kelas XI.');
        $response->assertSee('Agenda workshop robotik.');
        $response->assertSee(route('public.news.index'), false);
    }

    public function test_news_detail_returns_not_found_for_non_public_slug(): void
    {
        $draft = News::factory()->create([
            'slug' => 'berita-draft',
            'status' => NewsStatus::DRAFT,
            'published_at' => null,
        ]);

        $archived = News::factory()->create([
            'slug' => 'berita-arsip',
            'status' => NewsStatus::ARCHIVED,
            'published_at' => now(),
        ]);

        $publishedWithoutDate = News::factory()->create([
            'slug' => 'berita-published-tanpa-tanggal',
            'status' => NewsStatus::PUBLISHED,
            'published_at' => null,
        ]);

        $this->get(route('public.news.show', ['slug' => $draft->slug]))->assertNotFound();
        $this->get(route('public.news.show', ['slug' => $archived->slug]))->assertNotFound();
        $this->get(route('public.news.show', ['slug' => $publishedWithoutDate->slug]))->assertNotFound();
        $this->get(route('public.news.show', ['slug' => 'slug-tidak-ditemukan']))->assertNotFound();
    }

    public function test_news_listing_is_paginated_with_nine_items_per_page(): void
    {
        for ($index = 1; $index <= 12; $index++) {
            News::factory()->published()->create([
                'title' => sprintf('Berita Publik #%02d', $index),
                'slug' => sprintf('berita-publik-%02d', $index),
                'published_at' => now()->subMinutes(12 - $index),
            ]);
        }

        $firstPage = $this->get(route('public.news.index'));

        $firstPage->assertOk();
        $firstPage->assertSee('Menampilkan 1-9 dari 12 berita.');
        $firstPage->assertSee('Berita Publik #12');
        $firstPage->assertDontSee('Berita Publik #01');
        $firstPage->assertDontSee(route('public.news.show', ['slug' => 'berita-publik-01']), false);

        $secondPage = $this->get(route('public.news.index', ['page' => 2]));

        $secondPage->assertOk();
        $secondPage->assertSee('Menampilkan 10-12 dari 12 berita.');
        $secondPage->assertSee('Berita Publik #01');
        $secondPage->assertDontSee('Berita Publik #12');
        $secondPage->assertSee(route('public.news.show', ['slug' => 'berita-publik-01']), false);
    }
}
