<?php

declare(strict_types=1);

namespace App\Modules\Announcements\Actions;

use App\Modules\Announcements\Models\Announcement;
use Illuminate\Support\Facades\Schema;

class FindVisibleAnnouncementBySlugAction
{
    public function execute(string $slug): ?Announcement
    {
        if (! Schema::hasTable('announcements')) {
            return null;
        }

        return Announcement::query()
            ->visibleOnPublic()
            ->with('author')
            ->where('slug', $slug)
            ->first();
    }
}
