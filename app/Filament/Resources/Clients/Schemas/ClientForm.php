<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->required(),
                DatePicker::make('birth_date'),
                TextInput::make('gender'),
                TextInput::make('occupation'),
                TextInput::make('nationality')
                    ->required(),
                TextInput::make('passport_number'),
                TextInput::make('country_of_residence'),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('current_visa'),
                DatePicker::make('expire_date'),
                TextInput::make('status')
                    ->required()
                    ->default('new'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                TextInput::make('folder_path')->disabled(),
            ]);
    }
}
