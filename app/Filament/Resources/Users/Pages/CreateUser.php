<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * @var array<int, string>
     */
    protected array $rolesToAssign = [];

    protected bool $shouldSyncRoles = false;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws AuthorizationException
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->shouldSyncRoles = array_key_exists('assigned_roles', $data);

        if ($this->shouldSyncRoles && ! auth()->user()?->can('assignRole', User::class)) {
            throw new AuthorizationException('Tidak diizinkan mengubah role user.');
        }

        $this->rolesToAssign = Arr::wrap($data['assigned_roles'] ?? []);
        unset($data['assigned_roles']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if (! $this->shouldSyncRoles) {
            return;
        }

        $this->record->syncRoles($this->rolesToAssign);
    }
}
