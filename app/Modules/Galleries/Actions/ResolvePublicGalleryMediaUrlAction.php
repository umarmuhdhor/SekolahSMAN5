<?php

declare(strict_types=1);

namespace App\Modules\Galleries\Actions;

use App\Modules\MediaLibrary\Models\MediaAsset;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ResolvePublicGalleryMediaUrlAction
{
    /**
     * @return array{url: string, is_fallback: bool}
     */
    public function execute(?MediaAsset $mediaAsset): array
    {
        if ($mediaAsset === null || blank($mediaAsset->path) || blank($mediaAsset->disk)) {
            return [
                'url' => $this->fallbackUrl(),
                'is_fallback' => true,
            ];
        }

        try {
            $disk = Storage::disk($mediaAsset->disk);

            if (! $disk->exists($mediaAsset->path)) {
                return [
                    'url' => $this->fallbackUrl(),
                    'is_fallback' => true,
                ];
            }

            return [
                'url' => $disk->url($mediaAsset->path),
                'is_fallback' => false,
            ];
        } catch (Throwable) {
            return [
                'url' => $this->fallbackUrl(),
                'is_fallback' => true,
            ];
        }
    }

    public function fallbackUrl(): string
    {
        return asset('assets/theme/default-gallery-image.svg');
    }
}
