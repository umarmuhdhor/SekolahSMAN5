<?php

namespace App\Filament\Resources\NavigationMenus\Schemas;

use App\Modules\NavigationMenus\Support\NavigationMenuLocation;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NavigationMenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label('Menu Key')
                    ->required()
                    ->maxLength(64)
                    ->alphaDash()
                    ->unique(ignoreRecord: true)
                    ->helperText('Gunakan key unik, contoh: main_header atau main_footer.'),
                TextInput::make('name')
                    ->label('Nama Menu')
                    ->required()
                    ->maxLength(255),
                Select::make('location')
                    ->label('Lokasi')
                    ->options(NavigationMenuLocation::labels())
                    ->default(NavigationMenuLocation::HEADER)
                    ->required()
                    ->native(false),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->columnSpanFull()
                    ->nullable(),
            ]);
    }
}
