<?php

namespace App\Filament\Resources\Announcements\Schemas;

use App\Modules\Announcements\Support\AnnouncementStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AnnouncementForm
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
                    ->options(AnnouncementStatus::labels())
                    ->default(AnnouncementStatus::DRAFT)
                    ->required()
                    ->native(false)
                    ->helperText('Gunakan aksi Publish/Unpublish untuk kontrol publikasi.'),
                DateTimePicker::make('publish_start_at')
                    ->label('Publish Start')
                    ->seconds(false),
                DateTimePicker::make('publish_end_at')
                    ->label('Publish End')
                    ->seconds(false)
                    ->afterOrEqual('publish_start_at')
                    ->validationMessages([
                        'after_or_equal' => 'Publish End harus sama atau setelah Publish Start.',
                    ]),
                Textarea::make('excerpt')
                    ->label('Ringkasan')
                    ->rows(3)
                    ->maxLength(500)
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->label('Konten')
                    ->required()
                    ->rows(10)
                    ->columnSpanFull(),
                Placeholder::make('author_name')
                    ->label('Penulis')
                    ->content(fn ($record): string => $record?->author?->name ?? '-'),
            ]);
    }
}
