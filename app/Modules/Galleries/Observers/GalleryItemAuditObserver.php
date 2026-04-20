<?php

declare(strict_types=1);

namespace App\Modules\Galleries\Observers;

use App\Modules\AuditLogs\Actions\RecordAuditLogAction;
use App\Modules\AuditLogs\Support\AuditEventType;
use App\Modules\AuditLogs\Support\AuditModule;
use App\Modules\Galleries\Actions\NormalizeGalleryItemSortOrderAction;
use App\Modules\Galleries\Models\Gallery;
use App\Modules\Galleries\Models\GalleryItem;

class GalleryItemAuditObserver
{
    public function created(GalleryItem $galleryItem): void
    {
        app(NormalizeGalleryItemSortOrderAction::class)->execute($galleryItem->gallery);

        if (! auth()->check()) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::GALLERY_ITEM_ADDED,
            module: AuditModule::GALLERIES,
            actorId: auth()->id(),
            entityType: Gallery::class,
            entityId: $galleryItem->gallery_id,
            metadata: $this->itemMetadata($galleryItem),
        );
    }

    public function updated(GalleryItem $galleryItem): void
    {
        if (! $galleryItem->wasChanged('sort_order')) {
            return;
        }

        app(NormalizeGalleryItemSortOrderAction::class)->execute($galleryItem->gallery);

        if (! auth()->check()) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::GALLERY_ITEMS_REORDERED,
            module: AuditModule::GALLERIES,
            actorId: auth()->id(),
            entityType: Gallery::class,
            entityId: $galleryItem->gallery_id,
            before: [
                'sort_order' => (int) $galleryItem->getOriginal('sort_order'),
            ],
            after: [
                'sort_order' => (int) $galleryItem->sort_order,
            ],
            metadata: [
                ...$this->itemMetadata($galleryItem),
                'item_id' => $galleryItem->getKey(),
            ],
        );
    }

    public function deleted(GalleryItem $galleryItem): void
    {
        $gallery = $galleryItem->gallery()->first();

        if ($gallery !== null) {
            app(NormalizeGalleryItemSortOrderAction::class)->execute($gallery);
        }

        if (! auth()->check()) {
            return;
        }

        app(RecordAuditLogAction::class)->execute(
            eventType: AuditEventType::GALLERY_ITEM_REMOVED,
            module: AuditModule::GALLERIES,
            actorId: auth()->id(),
            entityType: Gallery::class,
            entityId: $galleryItem->gallery_id,
            metadata: $this->itemMetadata($galleryItem),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function itemMetadata(GalleryItem $galleryItem): array
    {
        return [
            'item_id' => $galleryItem->getKey(),
            'media_asset_id' => $galleryItem->media_asset_id,
            'sort_order' => $galleryItem->sort_order,
            'caption' => $galleryItem->caption,
            'alt_text' => $galleryItem->alt_text,
        ];
    }
}
