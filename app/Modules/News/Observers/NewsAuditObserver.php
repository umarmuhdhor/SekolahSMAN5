<?php

declare(strict_types=1);

namespace App\Modules\News\Observers;

use App\Modules\AuditLogs\Actions\RecordAuditLogAction;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\News\Models\News;
use App\Modules\News\Support\NewsStatus;
use Illuminate\Support\Arr;

class NewsAuditObserver
{
    public function created(News $news): void
    {
        if (! auth()->check()) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::NEWS_CREATED,
            module: AuditModule::NEWS,
            actorId: auth()->id(),
            entityType: News::class,
            entityId: $news->getKey(),
            after: $this->snapshot($news),
        );
    }

    public function updated(News $news): void
    {
        if (! auth()->check()) {
            return;
        }

        $changes = Arr::except($news->getChanges(), ['updated_at']);

        if ($changes === []) {
            return;
        }

        $originalStatus = (string) $news->getOriginal('status');
        $currentStatus = (string) $news->status;

        $isPublishedTransition = $originalStatus !== NewsStatus::PUBLISHED
            && $currentStatus === NewsStatus::PUBLISHED;

        $isUnpublishedTransition = $originalStatus === NewsStatus::PUBLISHED
            && $currentStatus !== NewsStatus::PUBLISHED;

        if ($isPublishedTransition) {
            app(RecordAuditLogAction::class)->execute(
                eventType: AuditEventType::NEWS_PUBLISHED,
                module: AuditModule::NEWS,
                actorId: auth()->id(),
                entityType: News::class,
                entityId: $news->getKey(),
                before: [
                    'status' => $originalStatus,
                    'published_at' => $news->getOriginal('published_at'),
                ],
                after: [
                    'status' => $currentStatus,
                    'published_at' => $news->published_at,
                ],
            );
        }

        if ($isUnpublishedTransition) {
            app(RecordAuditLogAction::class)->execute(
                eventType: AuditEventType::NEWS_UNPUBLISHED,
                module: AuditModule::NEWS,
                actorId: auth()->id(),
                entityType: News::class,
                entityId: $news->getKey(),
                before: [
                    'status' => $originalStatus,
                    'published_at' => $news->getOriginal('published_at'),
                ],
                after: [
                    'status' => $currentStatus,
                    'published_at' => $news->published_at,
                ],
            );
        }

        $changedKeys = array_keys($changes);

        if ($isPublishedTransition || $isUnpublishedTransition) {
            $changedKeys = array_values(array_diff($changedKeys, ['status', 'published_at']));
        }

        if ($changedKeys === []) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::NEWS_UPDATED,
            module: AuditModule::NEWS,
            actorId: auth()->id(),
            entityType: News::class,
            entityId: $news->getKey(),
            before: Arr::only($news->getOriginal(), $changedKeys),
            after: Arr::only($news->getAttributes(), $changedKeys),
        );
    }

    public function deleted(News $news): void
    {
        if (! auth()->check()) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::NEWS_DELETED,
            module: AuditModule::NEWS,
            actorId: auth()->id(),
            entityType: News::class,
            entityId: $news->getKey(),
            before: $this->snapshot($news),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function snapshot(News $news): array
    {
        return [
            'title' => $news->title,
            'slug' => $news->slug,
            'status' => $news->status,
            'published_at' => $news->published_at,
            'author_id' => $news->author_id,
            'cover_media_asset_id' => $news->cover_media_asset_id,
        ];
    }
}
