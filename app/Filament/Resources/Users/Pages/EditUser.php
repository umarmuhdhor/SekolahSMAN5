<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /**
     * @var array<int, string>
     */
    protected array $rolesToAssign = [];

    protected bool $shouldSyncRoles = false;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (auth()->user()?->can('assignRole', User::class)) {
            $data['assigned_roles'] = $this->record->roles->pluck('name')->all();
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws AuthorizationException
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->shouldSyncRoles = array_key_exists('assigned_roles', $data);

        if ($this->shouldSyncRoles && ! auth()->user()?->can('assignRole', User::class)) {
            throw new AuthorizationException('Tidak diizinkan mengubah role user.');
        }

        $this->rolesToAssign = Arr::wrap($data['assigned_roles'] ?? []);
        unset($data['assigned_roles']);

        return $data;
    }

    protected function afterSave(): void
    {
        if (! $this->shouldSyncRoles) {
            return;
        }

        $this->record->syncRoles($this->rolesToAssign);
    }
}
