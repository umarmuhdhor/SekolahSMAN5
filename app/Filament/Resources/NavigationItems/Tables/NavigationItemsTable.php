<?php

namespace App\Filament\Resources\NavigationItems\Tables;

use App\Modules\NavigationMenus\Support\NavigationLinkType;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NavigationItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('menu.name')
                    ->label('Menu')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parent.label')
                    ->label('Parent')
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('label')
                    ->label('Label')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('link_type')
                    ->label('Tipe Link')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => NavigationLinkType::labels()[$state] ?? $state),
                TextColumn::make('link_value')
                    ->label('Link')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
                IconColumn::make('is_visible')
                    ->label('Visible')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('navigation_menu_id')
                    ->label('Menu')
                    ->relationship('menu', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('link_type')
                    ->label('Tipe Link')
                    ->options(NavigationLinkType::labels()),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('updated_at', 'desc');
    }
}
