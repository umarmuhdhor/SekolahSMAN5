<?php

namespace App\Filament\Resources\ThemeSettings\Pages;

use App\Filament\Resources\ThemeSettings\ThemeSettingResource;
use App\Modules\ThemeSettings\Models\ThemeSetting;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateThemeSetting extends CreateRecord
{
    protected static string $resource = ThemeSettingResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (ThemeSetting::query()->exists()) {
            throw ValidationException::withMessages([
                'data.primary_color' => 'Theme aktif hanya boleh memiliki satu konfigurasi.',
            ]);
        }

        return [
            ...$data,
            'singleton_key' => 'default',
            'is_active' => true,
            'updated_by' => auth()->id(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return ThemeSettingResource::getUrl('index');
    }
}
