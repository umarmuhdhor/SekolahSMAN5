<?php

namespace App\Filament\Resources\MediaAssets\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MediaAssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('path')
                    ->label('File Media')
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->disk((string) config('media_library.disk', config('filesystems.default')))
                    ->directory((string) config('media_library.directory', 'media-library'))
                    ->visibility((string) config('media_library.visibility', 'private'))
                    ->acceptedFileTypes((array) config('media_library.allowed_mime_types', []))
                    ->maxSize((int) max(array_map('intval', array_values((array) config('media_library.max_size_kb', ['*' => 10240])))))
                    ->openable()
                    ->downloadable()
                    ->storeFileNamesIn('original_name')
                    ->getUploadedFileNameForStorageUsing(
                        static function (TemporaryUploadedFile $file): string {
                            $baseName = Str::of(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                                ->slug('_')
                                ->limit(100, '')
                                ->value();

                            $extension = $file->getClientOriginalExtension();
                            $safeBaseName = filled($baseName) ? $baseName : 'media_file';

                            return Str::ulid().'_'.$safeBaseName.'.'.$extension;
                        }
                    ),
                Hidden::make('original_name'),
                TextInput::make('alt_text')
                    ->label('Alt Text')
                    ->maxLength(255),
                Textarea::make('caption')
                    ->label('Caption')
                    ->rows(3)
                    ->columnSpanFull(),
                Placeholder::make('mime_type_info')
                    ->label('MIME Type')
                    ->content(fn ($record): string => $record?->mime_type ?? '-')
                    ->visible(fn (string $operation): bool => $operation === 'edit'),
                Placeholder::make('size_info')
                    ->label('Ukuran File')
                    ->content(function ($record): string {
                        if (! $record?->size_bytes) {
                            return '-';
                        }

                        return number_format(((int) $record->size_bytes) / 1024, 2).' KB';
                    })
                    ->visible(fn (string $operation): bool => $operation === 'edit'),
            ]);
    }
}
