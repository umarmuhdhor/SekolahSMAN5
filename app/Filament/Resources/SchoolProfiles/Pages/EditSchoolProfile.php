<?php

namespace App\Filament\Resources\SchoolProfiles\Pages;

use App\Filament\Resources\SchoolProfiles\SchoolProfileResource;
use Filament\Resources\Pages\EditRecord;

class EditSchoolProfile extends EditRecord
{
    protected static string $resource = SchoolProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return [
            ...$data,
            'updated_by' => auth()->id(),
        ];
    }
}
