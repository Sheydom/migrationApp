<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ChecklistItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'checklistItems';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Checklist';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([


                TextInput::make('title')
                    ->required()
                    ->maxLength(255)->disabledOn('edit'),

                Textarea::make('description')
                    ->rows(8)
                    ->columnSpanFull(),

                Grid::make([
                    'default' => 1,
                    'md' => 2,
                ])
                    ->schema([
                        Toggle::make('is_completed')
                            ->label('Completed')
                            ->live()
                            ->afterStateUpdated(
                                function (bool $state, Set $set): void {
                                    $set(
                                        'completed_at',
                                        $state ? now() : null
                                    );
                                }
                            ),

                        DateTimePicker::make('completed_at')
                            ->label('Completed at')
                            ->dehydrated(),

//                        TextInput::make('sort_order')
//                            ->label('Priority')
//                            ->required()
//                            ->numeric()
//                            ->minValue(0)
//                            ->default(0),
                    ]),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
//                TextColumn::make('sort_order')->width('20px')
//                    ->label('Priority')
//                    ->numeric()
//                    ->sortable(),
                IconColumn::make('is_completed')->width('20px')
                    ->label('Completed')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),


                TextColumn::make('completed_at')->width('120px')
                    ->label('Completed at')
                    ->dateTime()
                    ->placeholder('Not completed')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
