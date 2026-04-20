<?php

namespace App\Filament\Resources\SchoolProfiles\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SchoolProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('school_name')
                    ->label('Nama Sekolah')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Deskripsi Sekolah')
                    ->rows(4)
                    ->columnSpanFull(),
                Textarea::make('address')
                    ->label('Alamat')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('email')
                    ->label('Email Kontak')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Telepon Kontak')
                    ->required()
                    ->maxLength(30)
                    ->rule('regex:/^[0-9+()\-\s]{8,30}$/')
                    ->validationMessages([
                        'regex' => 'Format telepon tidak valid.',
                    ]),
                TextInput::make('website')
                    ->label('Website')
                    ->url()
                    ->maxLength(255)
                    ->nullable(),
                TextInput::make('facebook_url')
                    ->label('Facebook URL')
                    ->url()
                    ->maxLength(255)
                    ->nullable(),
                TextInput::make('instagram_url')
                    ->label('Instagram URL')
                    ->url()
                    ->maxLength(255)
                    ->nullable(),
                TextInput::make('youtube_url')
                    ->label('YouTube URL')
                    ->url()
                    ->maxLength(255)
                    ->nullable(),
            ]);
    }
}
