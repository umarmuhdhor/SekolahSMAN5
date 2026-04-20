<?php

declare(strict_types=1);

namespace App\Modules\News\Actions;

use App\Modules\News\Models\News;
use Illuminate\Support\Str;

class GenerateUniqueNewsSlugAction
{
    public function execute(string $title, ?string $preferredSlug = null, ?int $ignoreNewsId = null): string
    {
        $baseSource = filled($preferredSlug) ? $preferredSlug : $title;

        $baseSlug = Str::of($baseSource)
            ->slug('-')
            ->limit(255, '')
            ->value();

        if (! filled($baseSlug)) {
            $baseSlug = 'news';
        }

        $candidate = $baseSlug;
        $counter = 2;

        while ($this->slugExists($candidate, $ignoreNewsId)) {
            $suffix = '-'.$counter;
            $candidate = Str::limit($baseSlug, 255 - strlen($suffix), '').$suffix;
            $counter++;
        }

        return $candidate;
    }

    private function slugExists(string $slug, ?int $ignoreNewsId = null): bool
    {
        $query = News::query()->where('slug', $slug);

        if ($ignoreNewsId !== null) {
            $query->whereKeyNot($ignoreNewsId);
        }

        return $query->exists();
    }
}
