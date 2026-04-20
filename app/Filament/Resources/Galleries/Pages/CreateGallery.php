<?php

namespace App\Filament\Resources\Galleries\Pages;

use App\Filament\Resources\Galleries\GalleryResource;
use App\Modules\Galleries\Actions\GenerateUniqueGallerySlugAction;
use App\Modules\Galleries\Models\Gallery;
use App\Modules\Galleries\Support\GalleryStatus;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Auth\Access\AuthorizationException;

class CreateGallery extends CreateRecord
{
    protected static string $resource = GalleryResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws AuthorizationException
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $status = (string) ($data['status'] ?? GalleryStatus::DRAFT);

        if (! in_array($status, GalleryStatus::all(), true)) {
            $status = GalleryStatus::DRAFT;
        }

        $data['status'] = $status;
        $data['author_id'] = (int) ($data['author_id'] ?? auth()->id());
        $data['slug'] = app(GenerateUniqueGallerySlugAction::class)->execute(
            title: (string) ($data['title'] ?? ''),
            preferredSlug: isset($data['slug']) ? (string) $data['slug'] : null,
        );

        if ($status === GalleryStatus::PUBLISHED) {
            $gallery = new Gallery([
                'author_id' => $data['author_id'],
            ]);

            if (! auth()->user()?->can('publish', $gallery)) {
                throw new AuthorizationException('Tidak diizinkan mem-publish gallery.');
            }

            $data['published_at'] = now();

            return $data;
        }

        $data['published_at'] = null;

        return $data;
    }
}
