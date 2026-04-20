<?php

namespace App\Filament\Resources\NavigationItems;

use App\Filament\Resources\NavigationItems\Pages\CreateNavigationItem;
use App\Filament\Resources\NavigationItems\Pages\EditNavigationItem;
use App\Filament\Resources\NavigationItems\Pages\ListNavigationItems;
use App\Filament\Resources\NavigationItems\Schemas\NavigationItemForm;
use App\Filament\Resources\NavigationItems\Tables\NavigationItemsTable;
use App\Modules\NavigationMenus\Models\NavigationItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NavigationItemResource extends Resource
{
    protected static ?string $model = NavigationItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static ?string $navigationLabel = 'Navigation Items';

    protected static ?string $modelLabel = 'Navigation Item';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 31;

    protected static ?string $slug = 'navigation-items';

    public static function form(Schema $schema): Schema
    {
        return NavigationItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NavigationItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNavigationItems::route('/'),
            'create' => CreateNavigationItem::route('/create'),
            'edit' => EditNavigationItem::route('/{record}/edit'),
        ];
    }
}
