<?php

declare(strict_types=1);

namespace App\Modules\Galleries\Actions;

use App\Modules\Galleries\Models\Gallery;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class ListPublishedGalleriesAction
{
    /**
     * @return LengthAwarePaginator<int, Gallery>
     */
    public function execute(int $perPage = 9): LengthAwarePaginator
    {
        if (! Schema::hasTable('galleries') || ! Schema::hasTable('gallery_items') || ! Schema::hasTable('media_assets')) {
            return new LengthAwarePaginator(
                items: [],
                total: 0,
                perPage: $perPage,
                currentPage: 1,
                options: [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => 'page',
                ]
            );
        }

        return Gallery::query()
            ->published()
            ->with(['author', 'items.mediaAsset'])
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }
}
