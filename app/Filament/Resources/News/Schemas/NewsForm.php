<?php

namespace App\Filament\Resources\News\Schemas;

use App\Modules\News\Support\NewsStatus;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('Slug')
                    ->helperText('Kosongkan untuk generate otomatis dari judul.')
                    ->maxLength(255)
                    ->dehydrateStateUsing(
                        static fn (?string $state): ?string => filled($state)
                            ? Str::of($state)->slug('-')->limit(255, '')->value()
                            : null
                    ),
                Select::make('status')
                    ->label('Status')
                    ->options(NewsStatus::labels())
                    ->default(NewsStatus::DRAFT)
                    ->required()
                    ->native(false)
                    ->helperText('Gunakan aksi Publish/Unpublish untuk kontrol visibilitas publik.'),
                Select::make('cover_media_asset_id')
                    ->label('Cover Media')
                    ->relationship('cover', 'original_name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Textarea::make('excerpt')
                    ->label('Ringkasan')
                    ->rows(3)
                    ->maxLength(500)
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->label('Konten')
                    ->required()
                    ->rows(12)
                    ->columnSpanFull(),
                Placeholder::make('author_name')
                    ->label('Penulis')
                    ->content(fn ($record): string => $record?->author?->name ?? '-'),
            ]);
    }
}
