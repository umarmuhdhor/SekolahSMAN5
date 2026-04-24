<?php

namespace App\Filament\Resources\NavigationItems\Schemas;

use App\Modules\NavigationMenus\Support\NavigationItemTarget;
use App\Modules\NavigationMenus\Support\NavigationLinkType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NavigationItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('navigation_menu_id')
                    ->label('Navigation Menu')
                    ->relationship('menu', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('parent_id')
                    ->label('Parent Item')
                    ->relationship('parent', 'label')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Opsional. Parent wajib berasal dari menu yang sama.'),
                TextInput::make('label')
                    ->label('Label')
                    ->required()
                    ->maxLength(255),
                Select::make('link_type')
                    ->label('Jenis Link')
                    ->options(NavigationLinkType::labels())
                    ->default(NavigationLinkType::URL)
                    ->required()
                    ->native(false),
                TextInput::make('link_value')
                    ->label('Nilai Link')
                    ->required()
                    ->maxLength(255)
                    ->helperText('URL: /berita atau https://contoh.com. Route: gunakan nama route Laravel.'),
                Select::make('target')
                    ->label('Target')
                    ->options(NavigationItemTarget::labels())
                    ->default(NavigationItemTarget::SELF)
                    ->required()
                    ->native(false),
                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(1),
                Toggle::make('is_visible')
                    ->label('Tampilkan Item')
                    ->default(true),
            ]);
    }
}
