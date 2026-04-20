<?php

namespace App\Filament\Resources\Galleries\Tables;

use App\Modules\Galleries\Models\Gallery;
use App\Modules\Galleries\Support\GalleryStatus;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GalleriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => GalleryStatus::labels()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        GalleryStatus::PUBLISHED => 'success',
                        GalleryStatus::ARCHIVED => 'gray',
                        default => 'warning',
                    }),
                TextColumn::make('items_count')
                    ->label('Jumlah Item')
                    ->counts('items')
                    ->sortable(),
                TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Publish At')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(GalleryStatus::labels()),
                SelectFilter::make('author_id')
                    ->label('Author')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('publish')
                    ->label('Publish')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Gallery $record): bool => $record->status !== GalleryStatus::PUBLISHED)
                    ->authorize(fn (Gallery $record): bool => auth()->user()?->can('publish', $record) ?? false)
                    ->action(function (Gallery $record): void {
                        $record->update([
                            'status' => GalleryStatus::PUBLISHED,
                            'published_at' => now(),
                        ]);
                    }),
                Action::make('unpublish')
                    ->label('Unpublish')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn (Gallery $record): bool => $record->status === GalleryStatus::PUBLISHED)
                    ->authorize(fn (Gallery $record): bool => auth()->user()?->can('unpublish', $record) ?? false)
                    ->action(function (Gallery $record): void {
                        $record->update([
                            'status' => GalleryStatus::DRAFT,
                            'published_at' => null,
                        ]);
                    }),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
