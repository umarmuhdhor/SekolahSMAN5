<?php

namespace App\Filament\Resources\ThemeSettings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ThemeSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('logoMedia.original_name')
                    ->label('Logo')
                    ->placeholder('Default Logo')
                    ->searchable(),
                TextColumn::make('primary_color')
                    ->label('Primary')
                    ->placeholder('Default')
                    ->formatStateUsing(fn (?string $state): string => $state ?? 'Default'),
                TextColumn::make('secondary_color')
                    ->label('Secondary')
                    ->placeholder('Default')
                    ->formatStateUsing(fn (?string $state): string => $state ?? 'Default'),
                TextColumn::make('accent_color')
                    ->label('Accent')
                    ->placeholder('Default')
                    ->formatStateUsing(fn (?string $state): string => $state ?? 'Default'),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->defaultSort('updated_at', 'desc');
    }
}
