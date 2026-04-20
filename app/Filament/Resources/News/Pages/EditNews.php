<?php

namespace App\Filament\Resources\News\Pages;

use App\Filament\Resources\News\NewsResource;
use App\Modules\News\Actions\GenerateUniqueNewsSlugAction;
use App\Modules\News\Support\NewsStatus;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Auth\Access\AuthorizationException;

class EditNews extends EditRecord
{
    protected static string $resource = NewsResource::class;

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

        if (! in_array($status, NewsStatus::all(), true)) {
            $status = NewsStatus::DRAFT;
        }

        $data['status'] = $status;
        $data['author_id'] = (int) ($this->record->author_id ?? auth()->id());
        $data['slug'] = app(GenerateUniqueNewsSlugAction::class)->execute(
            title: (string) ($data['title'] ?? $this->record->title),
            preferredSlug: isset($data['slug']) ? (string) $data['slug'] : null,
            ignoreNewsId: (int) $this->record->getKey(),
        );

        $originalStatus = (string) $this->record->status;

        if ($status === NewsStatus::PUBLISHED) {
            if ($originalStatus !== NewsStatus::PUBLISHED && ! auth()->user()?->can('publish', $this->record)) {
                throw new AuthorizationException('Tidak diizinkan mem-publish berita.');
            }

            $data['published_at'] = $this->record->published_at ?? now();

            return $data;
        }

        if ($originalStatus === NewsStatus::PUBLISHED && ! auth()->user()?->can('unpublish', $this->record)) {
            throw new AuthorizationException('Tidak diizinkan melakukan unpublish berita.');
        }

        $data['published_at'] = null;

        return $data;
    }
}
