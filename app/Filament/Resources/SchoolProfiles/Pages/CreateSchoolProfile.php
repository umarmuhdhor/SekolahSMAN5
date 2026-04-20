<?php

namespace App\Filament\Resources\SchoolProfiles\Pages;

use App\Filament\Resources\SchoolProfiles\SchoolProfileResource;
use App\Modules\SchoolProfile\Models\SchoolProfile;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateSchoolProfile extends CreateRecord
{
    protected static string $resource = SchoolProfileResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (SchoolProfile::query()->exists()) {
            throw ValidationException::withMessages([
                'data.school_name' => 'Profil sekolah hanya boleh memiliki satu record.',
            ]);
        }

        return [
            ...$data,
            'singleton_key' => 'default',
            'updated_by' => auth()->id(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return SchoolProfileResource::getUrl('index');
    }
}
