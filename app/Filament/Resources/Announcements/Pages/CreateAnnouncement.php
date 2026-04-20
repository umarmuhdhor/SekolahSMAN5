<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Filament\Resources\Announcements\AnnouncementResource;
use App\Modules\Announcements\Actions\GenerateUniqueAnnouncementSlugAction;
use App\Modules\Announcements\Models\Announcement;
use App\Modules\Announcements\Support\AnnouncementStatus;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Auth\Access\AuthorizationException;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws AuthorizationException
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $status = (string) ($data['status'] ?? AnnouncementStatus::DRAFT);

        if (! in_array($status, AnnouncementStatus::all(), true)) {
            $status = AnnouncementStatus::DRAFT;
        }

        $data['status'] = $status;
        $data['author_id'] = (int) ($data['author_id'] ?? auth()->id());
        $data['slug'] = app(GenerateUniqueAnnouncementSlugAction::class)->execute(
            title: (string) ($data['title'] ?? ''),
            preferredSlug: isset($data['slug']) ? (string) $data['slug'] : null,
        );

        if ($status === AnnouncementStatus::PUBLISHED) {
            $announcement = new Announcement([
                'author_id' => $data['author_id'],
            ]);

            if (! auth()->user()?->can('publish', $announcement)) {
                throw new AuthorizationException('Tidak diizinkan mem-publish pengumuman.');
            }

            $data['published_at'] = now();

            return $data;
        }

        $data['published_at'] = null;

        return $data;
    }
}
