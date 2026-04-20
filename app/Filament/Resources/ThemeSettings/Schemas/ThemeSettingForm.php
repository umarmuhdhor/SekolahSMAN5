<?php

namespace App\Filament\Resources\ThemeSettings\Schemas;

use App\Modules\ThemeSettings\Support\ThemeDefaults;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ThemeSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('logo_media_id')
                    ->label('Logo Sekolah')
                    ->relationship('logoMedia', 'original_name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Kosongkan untuk menggunakan logo default sistem.'),
                TextInput::make('primary_color')
                    ->label('Primary Color')
                    ->placeholder(ThemeDefaults::palette()['primary'])
                    ->helperText('Format HEX 6 digit, contoh: #1D4ED8.')
                    ->maxLength(7)
                    ->nullable()
                    ->rule('regex:/^#[0-9A-Fa-f]{6}$/')
                    ->validationMessages([
                        'regex' => 'Primary color harus dalam format HEX valid (#RRGGBB).',
                    ])
                    ->dehydrateStateUsing(fn (?string $state): ?string => ThemeDefaults::normalizeHex($state)),
                TextInput::make('secondary_color')
                    ->label('Secondary Color')
                    ->placeholder(ThemeDefaults::palette()['secondary'])
                    ->helperText('Format HEX 6 digit, contoh: #0F766E.')
                    ->maxLength(7)
                    ->nullable()
                    ->rule('regex:/^#[0-9A-Fa-f]{6}$/')
                    ->validationMessages([
                        'regex' => 'Secondary color harus dalam format HEX valid (#RRGGBB).',
                    ])
                    ->dehydrateStateUsing(fn (?string $state): ?string => ThemeDefaults::normalizeHex($state)),
                TextInput::make('accent_color')
                    ->label('Accent Color')
                    ->placeholder(ThemeDefaults::palette()['accent'])
                    ->helperText('Format HEX 6 digit, contoh: #F59E0B.')
                    ->maxLength(7)
                    ->nullable()
                    ->rule('regex:/^#[0-9A-Fa-f]{6}$/')
                    ->validationMessages([
                        'regex' => 'Accent color harus dalam format HEX valid (#RRGGBB).',
                    ])
                    ->dehydrateStateUsing(fn (?string $state): ?string => ThemeDefaults::normalizeHex($state)),
            ]);
    }
}
