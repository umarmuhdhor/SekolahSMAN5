<?php

declare(strict_types=1);

namespace App\Modules\Galleries\Actions;

use App\Modules\Galleries\Models\Gallery;
use Illuminate\Support\Facades\DB;

class NormalizeGalleryItemSortOrderAction
{
    public function execute(Gallery $gallery): void
    {
        $items = $gallery->items()
            ->select(['id'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($items->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($items): void {
            foreach ($items->values() as $index => $item) {
                $item->forceFill([
                    'sort_order' => 10000 + $index,
                ])->saveQuietly();
            }

            foreach ($items->values() as $index => $item) {
                $item->forceFill([
                    'sort_order' => $index + 1,
                ])->saveQuietly();
            }
        });
    }
}
