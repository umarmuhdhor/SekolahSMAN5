<?php

namespace App\Filament\Resources\Galleries\Schemas;

use App\Modules\Galleries\Support\GalleryStatus;
use App\Modules\MediaLibrary\Models\MediaAsset;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class GalleryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Album')
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
                    ->options(GalleryStatus::labels())
                    ->default(GalleryStatus::DRAFT)
                    ->required()
                    ->native(false)
                    ->helperText('Gunakan aksi Publish/Unpublish untuk kontrol visibilitas publik.'),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(4)
                    ->columnSpanFull(),
                Repeater::make('items')
                    ->label('Item Gallery')
                    ->relationship('items')
                    ->orderColumn('sort_order')
                    ->schema([
                        Select::make('media_asset_id')
                            ->label('Media')
                            ->required()
                            ->options(fn (): array => MediaAsset::query()->orderByDesc('id')->pluck('original_name', 'id')->all())
                            ->searchable()
                            ->preload(),
                        TextInput::make('caption')
                            ->label('Caption')
                            ->maxLength(255),
                        TextInput::make('alt_text')
                            ->label('Alt Text')
                            ->maxLength(255),
                    ])
                    ->defaultItems(0)
                    ->collapsible()
                    ->cloneable()
                    ->reorderableWithButtons()
                    ->columnSpanFull(),
                Placeholder::make('author_name')
                    ->label('Author')
                    ->content(fn ($record): string => $record?->author?->name ?? '-'),
            ]);
    }
}
