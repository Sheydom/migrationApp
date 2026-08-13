<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Actions\Action;


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
                TextInput::make('email')->confirmed(fn(string $operation): bool => $operation === 'create')
                    ->label('Email address')
                    ->email()
                    ->required()->unique(ignoreRecord: true)->validationMessages(['unique' => 'A client with this email address already exists.']),
                TextInput::make('email_confirmation')
                    ->label('Email Confirmation')
                    ->email()
                    ->required()->visibleOn('create'),
                DatePicker::make('expire_date'),
                Select::make('status')->options(['Refused' => 'Refused', 'Submitted' => 'Submitted', 'New' => 'New', 'in Progress' => 'In progress', 'Review AI' => 'Review AI', 'Closed' => 'Closed'])->default('New')->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                TextInput::make('folder_path')
                    ->disabled()
                    ->suffixAction(
                        Action::make('open')
                            ->icon('heroicon-m-folder-open')
                            ->url(fn($record) => filled($record?->folder_path) ? 'https://cloud.dominic-knabe.com/apps/files/?dir=/' .

                                urlencode($record->folder_path) : null

                            )
                            ->openUrlInNewTab()

                    )->visible(fn($record): bool => filled($record?->folder_path))


            ]);
    }
}
