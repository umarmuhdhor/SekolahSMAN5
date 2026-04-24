<?php

declare(strict_types=1);

namespace App\Modules\News\Actions;

use App\Modules\News\Models\News;
use Illuminate\Support\Facades\Schema;

class FindPublishedNewsBySlugAction
{
    public function execute(string $slug): ?News
    {
        if (! Schema::hasTable('news')) {
            return null;
        }

        return News::query()
            ->published()
            ->with('author')
            ->where('slug', $slug)
            ->first();
    }
}
