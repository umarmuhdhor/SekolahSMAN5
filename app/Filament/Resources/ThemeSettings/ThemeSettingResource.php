<?php

namespace App\Filament\Resources\ThemeSettings;

use App\Filament\Resources\ThemeSettings\Pages\CreateThemeSetting;
use App\Filament\Resources\ThemeSettings\Pages\EditThemeSetting;
use App\Filament\Resources\ThemeSettings\Pages\ListThemeSettings;
use App\Filament\Resources\ThemeSettings\Schemas\ThemeSettingForm;
use App\Filament\Resources\ThemeSettings\Tables\ThemeSettingsTable;
use App\Modules\ThemeSettings\Models\ThemeSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ThemeSettingResource extends Resource
{
    protected static ?string $model = ThemeSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?string $navigationLabel = 'Theme Settings';

    protected static ?string $modelLabel = 'Theme Settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 20;

    protected static ?string $slug = 'theme-settings';

    public static function form(Schema $schema): Schema
    {
        return ThemeSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ThemeSettingsTable::configure($table);
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
            'index' => ListThemeSettings::route('/'),
            'create' => CreateThemeSetting::route('/create'),
            'edit' => EditThemeSetting::route('/{record}/edit'),
        ];
    }
}
