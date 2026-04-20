<?php

namespace App\Filament\Resources\News\Tables;

use App\Modules\News\Models\News;
use App\Modules\News\Support\NewsStatus;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NewsTable
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
                    ->formatStateUsing(fn (string $state): string => NewsStatus::labels()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        NewsStatus::PUBLISHED => 'success',
                        NewsStatus::ARCHIVED => 'gray',
                        default => 'warning',
                    }),
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
                    ->options(NewsStatus::labels()),
                SelectFilter::make('author_id')
                    ->label('Author')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('published_range')
                    ->label('Rentang Publish')
                    ->schema([
                        DatePicker::make('published_from')->label('Dari'),
                        DatePicker::make('published_until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'] ?? null,
                                fn (Builder $query, string $date): Builder => $query->whereDate('published_at', '>=', $date)
                            )
                            ->when(
                                $data['published_until'] ?? null,
                                fn (Builder $query, string $date): Builder => $query->whereDate('published_at', '<=', $date)
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('publish')
                    ->label('Publish')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (News $record): bool => $record->status !== NewsStatus::PUBLISHED)
                    ->authorize(fn (News $record): bool => auth()->user()?->can('publish', $record) ?? false)
                    ->action(function (News $record): void {
                        $record->update([
                            'status' => NewsStatus::PUBLISHED,
                            'published_at' => now(),
                        ]);
                    }),
                Action::make('unpublish')
                    ->label('Unpublish')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn (News $record): bool => $record->status === NewsStatus::PUBLISHED)
                    ->authorize(fn (News $record): bool => auth()->user()?->can('unpublish', $record) ?? false)
                    ->action(function (News $record): void {
                        $record->update([
                            'status' => NewsStatus::DRAFT,
                            'published_at' => null,
                        ]);
                    }),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
