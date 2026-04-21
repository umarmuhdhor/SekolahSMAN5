<?php

declare(strict_types=1);

namespace App\Modules\Galleries\Actions;

use App\Modules\Galleries\Models\Gallery;
use Illuminate\Support\Facades\Schema;

class FindPublishedGalleryBySlugAction
{
    public function execute(string $slug): ?Gallery
    {
        if (! Schema::hasTable('galleries') || ! Schema::hasTable('gallery_items') || ! Schema::hasTable('media_assets')) {
            return null;
        }

        return Gallery::query()
            ->published()
            ->with(['author', 'items.mediaAsset'])
            ->where('slug', $slug)
            ->first();
    }
}
