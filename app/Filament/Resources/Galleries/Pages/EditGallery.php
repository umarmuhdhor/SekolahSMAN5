<?php

namespace App\Filament\Resources\Galleries\Pages;

use App\Filament\Resources\Galleries\GalleryResource;
use App\Modules\Galleries\Actions\GenerateUniqueGallerySlugAction;
use App\Modules\Galleries\Support\GalleryStatus;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Auth\Access\AuthorizationException;

class EditGallery extends EditRecord
{
    protected static string $resource = GalleryResource::class;

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

        if (! in_array($status, GalleryStatus::all(), true)) {
            $status = GalleryStatus::DRAFT;
        }

        $data['status'] = $status;
        $data['author_id'] = (int) ($this->record->author_id ?? auth()->id());
        $data['slug'] = app(GenerateUniqueGallerySlugAction::class)->execute(
            title: (string) ($data['title'] ?? $this->record->title),
            preferredSlug: isset($data['slug']) ? (string) $data['slug'] : null,
            ignoreGalleryId: (int) $this->record->getKey(),
        );

        $originalStatus = (string) $this->record->status;

        if ($status === GalleryStatus::PUBLISHED) {
            if ($originalStatus !== GalleryStatus::PUBLISHED && ! auth()->user()?->can('publish', $this->record)) {
                throw new AuthorizationException('Tidak diizinkan mem-publish gallery.');
            }

            $data['published_at'] = $this->record->published_at ?? now();

            return $data;
        }

        if ($originalStatus === GalleryStatus::PUBLISHED && ! auth()->user()?->can('unpublish', $this->record)) {
            throw new AuthorizationException('Tidak diizinkan melakukan unpublish gallery.');
        }

        $data['published_at'] = null;

        return $data;
    }
}
