<?php

namespace App\Filament\Resources\Announcements\Tables;

use App\Modules\Announcements\Models\Announcement;
use App\Modules\Announcements\Support\AnnouncementStatus;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AnnouncementsTable
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
                    ->formatStateUsing(fn (string $state): string => AnnouncementStatus::labels()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        AnnouncementStatus::PUBLISHED => 'success',
                        AnnouncementStatus::ARCHIVED => 'gray',
                        default => 'warning',
                    }),
                TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('publish_start_at')
                    ->label('Mulai Tayang')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('publish_end_at')
                    ->label('Akhir Tayang')
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
                    ->options(AnnouncementStatus::labels()),
                SelectFilter::make('author_id')
                    ->label('Author')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('active_window')
                    ->label('Window Aktif')
                    ->query(function (Builder $query): Builder {
                        return $query
                            ->where(static function (Builder $query): void {
                                $query->whereNull('publish_start_at')->orWhere('publish_start_at', '<=', now());
                            })
                            ->where(static function (Builder $query): void {
                                $query->whereNull('publish_end_at')->orWhere('publish_end_at', '>=', now());
                            });
                    }),
                Filter::make('publish_range')
                    ->label('Rentang Tayang')
                    ->schema([
                        DatePicker::make('from')->label('Dari'),
                        DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, string $date): Builder => $query->whereDate('publish_start_at', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, string $date): Builder => $query->whereDate('publish_end_at', '<=', $date)
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('publish')
                    ->label('Publish')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Announcement $record): bool => $record->status !== AnnouncementStatus::PUBLISHED)
                    ->authorize(fn (Announcement $record): bool => auth()->user()?->can('publish', $record) ?? false)
                    ->action(function (Announcement $record): void {
                        $record->update([
                            'status' => AnnouncementStatus::PUBLISHED,
                            'published_at' => now(),
                        ]);
                    }),
                Action::make('unpublish')
                    ->label('Unpublish')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn (Announcement $record): bool => $record->status === AnnouncementStatus::PUBLISHED)
                    ->authorize(fn (Announcement $record): bool => auth()->user()?->can('unpublish', $record) ?? false)
                    ->action(function (Announcement $record): void {
                        $record->update([
                            'status' => AnnouncementStatus::DRAFT,
                            'published_at' => null,
                        ]);
                    }),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
