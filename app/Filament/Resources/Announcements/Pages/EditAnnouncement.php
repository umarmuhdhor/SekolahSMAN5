<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Filament\Resources\Announcements\AnnouncementResource;
use App\Modules\Announcements\Actions\GenerateUniqueAnnouncementSlugAction;
use App\Modules\Announcements\Support\AnnouncementStatus;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Auth\Access\AuthorizationException;

class EditAnnouncement extends EditRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws AuthorizationException
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $status = (string) ($data['status'] ?? $this->record->status);

        if (! in_array($status, AnnouncementStatus::all(), true)) {
            $status = AnnouncementStatus::DRAFT;
        }

        $data['status'] = $status;
        $data['author_id'] = (int) ($this->record->author_id ?? auth()->id());
        $data['slug'] = app(GenerateUniqueAnnouncementSlugAction::class)->execute(
            title: (string) ($data['title'] ?? $this->record->title),
            preferredSlug: isset($data['slug']) ? (string) $data['slug'] : null,
            ignoreAnnouncementId: (int) $this->record->getKey(),
        );

        $originalStatus = (string) $this->record->status;

        if ($status === AnnouncementStatus::PUBLISHED) {
            if ($originalStatus !== AnnouncementStatus::PUBLISHED && ! auth()->user()?->can('publish', $this->record)) {
                throw new AuthorizationException('Tidak diizinkan mem-publish pengumuman.');
            }

            $data['published_at'] = $this->record->published_at ?? now();

            return $data;
        }

        if ($originalStatus === AnnouncementStatus::PUBLISHED && ! auth()->user()?->can('unpublish', $this->record)) {
            throw new AuthorizationException('Tidak diizinkan melakukan unpublish pengumuman.');
        }

        $data['published_at'] = null;

        return $data;
    }
}
