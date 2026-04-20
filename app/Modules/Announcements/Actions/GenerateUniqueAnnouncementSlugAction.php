<?php

declare(strict_types=1);

namespace App\Modules\Announcements\Actions;

use App\Modules\Announcements\Models\Announcement;
use Illuminate\Support\Str;

class GenerateUniqueAnnouncementSlugAction
{
    public function execute(string $title, ?string $preferredSlug = null, ?int $ignoreAnnouncementId = null): string
    {
        $baseSource = filled($preferredSlug) ? $preferredSlug : $title;

        $baseSlug = Str::of($baseSource)
            ->slug('-')
            ->limit(255, '')
            ->value();

        if (! filled($baseSlug)) {
            $baseSlug = 'announcement';
        }

        $candidate = $baseSlug;
        $counter = 2;

        while ($this->slugExists($candidate, $ignoreAnnouncementId)) {
            $suffix = '-'.$counter;
            $candidate = Str::limit($baseSlug, 255 - strlen($suffix), '').$suffix;
            $counter++;
        }

        return $candidate;
    }

    private function slugExists(string $slug, ?int $ignoreAnnouncementId = null): bool
    {
        $query = Announcement::query()->where('slug', $slug);

        if ($ignoreAnnouncementId !== null) {
            $query->whereKeyNot($ignoreAnnouncementId);
        }

        return $query->exists();
    }
}
