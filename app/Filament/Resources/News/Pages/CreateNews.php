<?php

namespace App\Filament\Resources\News\Pages;

use App\Filament\Resources\News\NewsResource;
use App\Modules\News\Actions\GenerateUniqueNewsSlugAction;
use App\Modules\News\Models\News;
use App\Modules\News\Support\NewsStatus;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Auth\Access\AuthorizationException;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws AuthorizationException
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $status = (string) ($data['status'] ?? NewsStatus::DRAFT);

        if (! in_array($status, NewsStatus::all(), true)) {
            $status = NewsStatus::DRAFT;
        }

        $data['status'] = $status;
        $data['author_id'] = (int) ($data['author_id'] ?? auth()->id());
        $data['slug'] = app(GenerateUniqueNewsSlugAction::class)->execute(
            title: (string) ($data['title'] ?? ''),
            preferredSlug: isset($data['slug']) ? (string) $data['slug'] : null,
        );

        if ($status === NewsStatus::PUBLISHED) {
            $news = new News([
                'author_id' => $data['author_id'],
            ]);

            if (! auth()->user()?->can('publish', $news)) {
                throw new AuthorizationException('Tidak diizinkan mem-publish berita.');
            }

            $data['published_at'] = now();
        } else {
            $data['published_at'] = null;
        }

        return $data;
    }
}
