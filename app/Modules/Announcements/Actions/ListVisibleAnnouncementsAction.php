<?php

declare(strict_types=1);

namespace App\Modules\Announcements\Actions;

use App\Modules\Announcements\Models\Announcement;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class ListVisibleAnnouncementsAction
{
    /**
     * @return LengthAwarePaginator<int, Announcement>
     */
    public function execute(int $perPage = 9): LengthAwarePaginator
    {
        if (! Schema::hasTable('announcements')) {
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

        return Announcement::query()
            ->visibleOnPublic()
            ->with('author')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }
}
