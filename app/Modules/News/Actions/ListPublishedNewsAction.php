<?php

declare(strict_types=1);

namespace App\Modules\News\Actions;

use App\Modules\News\Models\News;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class ListPublishedNewsAction
{
    /**
     * @return LengthAwarePaginator<int, News>
     */
    public function execute(int $perPage = 9): LengthAwarePaginator
    {
        if (! Schema::hasTable('news')) {
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

        return News::query()
            ->published()
            ->with('author')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }
}
