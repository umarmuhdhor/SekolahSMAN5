<?php

namespace App\Filament\Resources\AuditLogs\Tables;

use App\Modules\AuditLogs\Support\AuditModule;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),
                TextColumn::make('actor.name')
                    ->label('Actor')
                    ->placeholder('System')
                    ->searchable(query: static function (Builder $query, string $search): Builder {
                        return $query->orWhereHas('actor', static fn (Builder $query): Builder => $query->where('name', 'like', "%{$search}%"));
                    }),
                TextColumn::make('module')
                    ->label('Module')
                    ->badge(),
                TextColumn::make('event_type')
                    ->label('Event Type')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('entity_type')
                    ->label('Entity Type')
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('entity_id')
                    ->label('Entity ID')
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('ip')
                    ->label('IP')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('actor_id')
                    ->label('Actor')
                    ->relationship('actor', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('module')
                    ->label('Module')
                    ->options(AuditModule::labels()),
                Filter::make('created_at_range')
                    ->label('Rentang Waktu')
                    ->schema([
                        DatePicker::make('created_from')->label('Dari'),
                        DatePicker::make('created_until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                static fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                static fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])
            ->recordActions([])
            ->defaultSort('created_at', 'desc');
    }
}
