<?php

namespace App\Filament\Resources\MediaAssets\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('original_name')
                    ->label('Nama File')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mime_type')
                    ->label('MIME')
                    ->badge()
                    ->searchable(),
                TextColumn::make('size_bytes')
                    ->label('Ukuran')
                    ->formatStateUsing(
                        static fn (int $state): string => number_format($state / 1024, 2).' KB'
                    )
                    ->sortable(),
                TextColumn::make('uploader.name')
                    ->label('Uploader')
                    ->toggleable(),
                IconColumn::make('disk')
                    ->label('Storage')
                    ->boolean()
                    ->trueIcon('heroicon-o-cloud-arrow-up')
                    ->falseIcon('heroicon-o-circle-stack')
                    ->state(fn ($record): bool => $record->disk === 's3')
                    ->tooltip(fn ($record): string => (string) $record->disk),
                TextColumn::make('created_at')
                    ->label('Diunggah')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
