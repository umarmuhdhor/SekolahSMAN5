<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use App\Modules\Galleries\Models\Gallery;
use App\Modules\MediaLibrary\Models\MediaAsset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicGalleryPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_listing_only_renders_published_albums(): void
    {
        $published = Gallery::factory()->published()->create([
            'title' => 'Dokumentasi Porseni',
            'slug' => 'dokumentasi-porseni',
        ]);

        Gallery::factory()->create([
            'title' => 'Album Draft Internal',
            'slug' => 'album-draft-internal',
            'status' => 'draft',
            'published_at' => null,
        ]);

        Gallery::factory()->create([
            'title' => 'Album Arsip Lama',
            'slug' => 'album-arsip-lama',
            'status' => 'archived',
            'published_at' => now(),
        ]);

        Gallery::factory()->create([
            'title' => 'Album Published Tanpa Tanggal',
            'slug' => 'album-published-tanpa-tanggal',
            'status' => 'published',
            'published_at' => null,
        ]);

        $response = $this->get(route('public.galleries.index'));

        $response->assertOk();
        $response->assertSee('Dokumentasi Porseni');
        $response->assertSee(route('public.galleries.show', ['slug' => $published->slug]), false);
        $response->assertDontSee('Album Draft Internal');
        $response->assertDontSee('Album Arsip Lama');
        $response->assertDontSee('Album Published Tanpa Tanggal');
    }

    public function test_gallery_listing_shows_empty_state_when_no_published_album_is_available(): void
    {
        Gallery::factory()->count(2)->create([
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->get(route('public.galleries.index'))
            ->assertOk()
            ->assertSee('Belum ada album galeri yang dipublikasikan.');
    }

    public function test_gallery_listing_uses_fallback_cover_for_album_without_item(): void
    {
        Gallery::factory()->published()->create([
            'title' => 'Album Tanpa Item',
            'slug' => 'album-tanpa-item',
        ]);

        $this->get(route('public.galleries.index'))
            ->assertOk()
            ->assertSee(asset('assets/theme/default-gallery-image.svg'), false);
    }

    public function test_gallery_detail_renders_for_published_slug_and_uses_fallback_for_missing_media_file(): void
    {
        $gallery = Gallery::factory()->published()->create([
            'title' => 'Dokumentasi Study Tour',
            'slug' => 'dokumentasi-study-tour',
            'description' => "Album kegiatan study tour siswa kelas XII.",
        ]);

        $media = $this->createMediaAsset('image-missing.jpg');

        $gallery->items()->create([
            'media_asset_id' => $media->id,
            'sort_order' => 1,
            'caption' => 'Sesi keberangkatan',
            'alt_text' => 'Foto keberangkatan study tour',
        ]);

        $response = $this->get(route('public.galleries.show', ['slug' => $gallery->slug]));

        $response->assertOk();
        $response->assertSee('Dokumentasi Study Tour');
        $response->assertSee('Album kegiatan study tour siswa kelas XII.');
        $response->assertSee('Sesi keberangkatan');
        $response->assertSee(route('public.galleries.index'), false);
        $response->assertSee(asset('assets/theme/default-gallery-image.svg'), false);
    }

    public function test_gallery_detail_returns_not_found_for_non_public_slug(): void
    {
        $draft = Gallery::factory()->create([
            'slug' => 'galeri-draft',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $archived = Gallery::factory()->create([
            'slug' => 'galeri-arsip',
            'status' => 'archived',
            'published_at' => now(),
        ]);

        $publishedWithoutDate = Gallery::factory()->create([
            'slug' => 'galeri-published-tanpa-tanggal',
            'status' => 'published',
            'published_at' => null,
        ]);

        $this->get(route('public.galleries.show', ['slug' => $draft->slug]))->assertNotFound();
        $this->get(route('public.galleries.show', ['slug' => $archived->slug]))->assertNotFound();
        $this->get(route('public.galleries.show', ['slug' => $publishedWithoutDate->slug]))->assertNotFound();
        $this->get(route('public.galleries.show', ['slug' => 'slug-tidak-ditemukan']))->assertNotFound();
    }

    public function test_gallery_listing_is_paginated_with_nine_items_per_page(): void
    {
        for ($index = 1; $index <= 12; $index++) {
            Gallery::factory()->published()->create([
                'title' => sprintf('Album Publik #%02d', $index),
                'slug' => sprintf('album-publik-%02d', $index),
                'published_at' => now()->subMinutes(12 - $index),
            ]);
        }

        $firstPage = $this->get(route('public.galleries.index'));

        $firstPage->assertOk();
        $firstPage->assertSee('Menampilkan 1-9 dari 12 album.');
        $firstPage->assertSee('Album Publik #12');
        $firstPage->assertDontSee('Album Publik #01');
        $firstPage->assertDontSee(route('public.galleries.show', ['slug' => 'album-publik-01']), false);

        $secondPage = $this->get(route('public.galleries.index', ['page' => 2]));

        $secondPage->assertOk();
        $secondPage->assertSee('Menampilkan 10-12 dari 12 album.');
        $secondPage->assertSee('Album Publik #01');
        $secondPage->assertDontSee('Album Publik #12');
        $secondPage->assertSee(route('public.galleries.show', ['slug' => 'album-publik-01']), false);
    }

    private function createMediaAsset(string $fileName): MediaAsset
    {
        return MediaAsset::query()->create([
            'disk' => 's3',
            'bucket' => 'cms-sekolah-media',
            'path' => 'galleries/public-fallback/'.Str::uuid().'-'.$fileName,
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
