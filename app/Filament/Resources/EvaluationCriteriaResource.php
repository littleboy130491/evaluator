<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluationCriteriaResource\Pages;
use App\Filament\Resources\EvaluationCriteriaResource\RelationManagers;
use App\Models\EvaluationCriteria;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EvaluationCriteriaResource extends Resource
{
    protected static ?string $model = EvaluationCriteria::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Evaluation Criteria';
    
    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Criteria Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter criteria name')
                            ->columnSpan(2),
                        Forms\Components\Textarea::make('description')
                            ->placeholder('Enter detailed description')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('max_score')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->placeholder('Maximum score value')
                            ->columnSpan(1),
                        Forms\Components\Select::make('category')
                            ->options([
                                'service' => 'Service',
                                'cleanliness' => 'Cleanliness',
                                'food_quality' => 'Food Quality',
                                'ambiance' => 'Ambiance',
                                'staff' => 'Staff',
                                'other' => 'Other',
                            ])
                            ->placeholder('Select a category')
                            ->columnSpan(1),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true)
                            ->helperText('Inactive criteria will not appear in evaluations')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('max_score')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'service' => 'info',
                        'cleanliness' => 'success',
                        'food_quality' => 'warning',
                        'ambiance' => 'danger',
                        'staff' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'service' => 'Service',
                        'cleanliness' => 'Cleanliness',
                        'food_quality' => 'Food Quality',
                        'ambiance' => 'Ambiance',
                        'staff' => 'Staff',
                        'other' => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
                    ->label('Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvaluationCriterias::route('/'),
            'create' => Pages\CreateEvaluationCriteria::route('/create'),
            'view' => Pages\ViewEvaluationCriteria::route('/{record}'),
            'edit' => Pages\EditEvaluationCriteria::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
