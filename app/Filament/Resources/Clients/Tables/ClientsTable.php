<?php

namespace App\Filament\Resources\Clients\Tables;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;
use App\Models\Client;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table->recordUrl(fn(Client $record): string => ClientResource::getUrl('edit', ['record' => $record, 'relation' => 'checklist']))
            ->modifyQueryUsing(function (Builder $query): Builder {
                return $query->withCount(['checklistItems as total_tasks', 'checklistItems as completed_tasks' => function (Builder $query) {
                    $query->where('is_completed', true);
                },]);
            })->columns([
                TextColumn::make('status')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('completed_tasks')
                    ->label('Checklist')->formatStateUsing(function ($state, $record): string {
                        if ($record->total_tasks === 0) {
                            return 'No checklist';
                        }
                        if ($record->remaining_tasks === 0) {
                            return 'Complete';
                        }
                        return "{$record->completed_tasks} / {$record->total_tasks} ";
                    })->sortable(),
                TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('birth_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('gender')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('occupation')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nationality')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('passport_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('country_of_residence')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('current_visa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('expire_date')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('folder_path')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
