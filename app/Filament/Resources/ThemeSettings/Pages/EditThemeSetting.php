<?php

namespace App\Filament\Resources\ThemeSettings\Pages;

use App\Filament\Resources\ThemeSettings\ThemeSettingResource;
use Filament\Resources\Pages\EditRecord;

class EditThemeSetting extends EditRecord
{
    protected static string $resource = ThemeSettingResource::class;

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
            'is_active' => true,
            'updated_by' => auth()->id(),
        ];
    }
}
