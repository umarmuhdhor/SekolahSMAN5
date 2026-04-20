<?php

namespace App\Filament\Resources\MediaAssets\Pages;

use App\Filament\Resources\MediaAssets\MediaAssetResource;
use App\Modules\MediaLibrary\Actions\PrepareMediaAssetPayloadAction;
use Filament\Resources\Pages\CreateRecord;

class CreateMediaAsset extends CreateRecord
{
    protected static string $resource = MediaAssetResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [
            ...app(PrepareMediaAssetPayloadAction::class)->execute($data),
            'uploaded_by' => auth()->id(),
        ];
    }
}
