<?php

declare(strict_types=1);

namespace App\Modules\Galleries\Actions;

use App\Modules\Galleries\Models\Gallery;
use Illuminate\Support\Str;

class GenerateUniqueGallerySlugAction
{
    public function execute(string $title, ?string $preferredSlug = null, ?int $ignoreGalleryId = null): string
    {
        $baseSource = filled($preferredSlug) ? $preferredSlug : $title;

        $baseSlug = Str::of($baseSource)
            ->slug('-')
            ->limit(255, '')
            ->value();

        if (! filled($baseSlug)) {
            $baseSlug = 'gallery';
        }

        $candidate = $baseSlug;
        $counter = 2;

        while ($this->slugExists($candidate, $ignoreGalleryId)) {
            $suffix = '-'.$counter;
            $candidate = Str::limit($baseSlug, 255 - strlen($suffix), '').$suffix;
            $counter++;
        }

        return $candidate;
    }

    private function slugExists(string $slug, ?int $ignoreGalleryId = null): bool
    {
        $query = Gallery::query()->where('slug', $slug);

        if ($ignoreGalleryId !== null) {
            $query->whereKeyNot($ignoreGalleryId);
        }

        return $query->exists();
    }
}
