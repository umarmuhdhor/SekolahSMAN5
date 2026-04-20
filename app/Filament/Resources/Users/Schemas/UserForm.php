<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(User::class, 'email', ignoreRecord: true),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->confirmed()
                    ->minLength(8)
                    ->rule(Password::min(8))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                TextInput::make('password_confirmation')
                    ->label('Konfirmasi Password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(false),
                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->inline(false)
                    ->default(true)
                    ->required(),
                Select::make('assigned_roles')
                    ->label('Role')
                    ->options(fn (): array => Role::query()->orderBy('name')->pluck('name', 'name')->all())
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(fn (): bool => auth()->user()?->can('assignRole', User::class) ?? false)
                    ->dehydrated(fn (): bool => auth()->user()?->can('assignRole', User::class) ?? false),
            ]);
    }
}
