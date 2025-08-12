<?php

namespace App\Filament\Resources\EvaluationResource\RelationManagers;

use App\Models\EvaluationCriteria;
use App\Services\EvaluationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CriteriaScoresRelationManager extends RelationManager
{
    protected static string $relationship = 'criteriaScores';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('evaluation_criteria_id')
                    ->label('Criteria')
                    ->options(function (RelationManager $livewire) {
                        $evaluation = $livewire->getOwnerRecord();
                        $existingCriteriaIds = $evaluation->criteriaScores()->pluck('evaluation_criteria_id')->toArray();
                        
                        return EvaluationCriteria::where('is_active', true)
                            ->whereNotIn('id', $existingCriteriaIds)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->columnSpan(2)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $criteria = EvaluationCriteria::find($state);
                            $set('max_score', $criteria ? $criteria->max_score : null);
                        }
                    })
                    ->helperText('Only criteria not yet scored for this evaluation are shown'),
                
                Forms\Components\TextInput::make('max_score')
                    ->label('Maximum Score')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(1),
                
                Forms\Components\TextInput::make('score')
                    ->label('Score')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(function (callable $get) {
                        $criteriaId = $get('evaluation_criteria_id');
                        if (!$criteriaId) return 100; // Default max
                        
                        $criteria = EvaluationCriteria::find($criteriaId);
                        return $criteria ? $criteria->max_score : 100;
                    })
                    ->columnSpan(1),
                
                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpan('full'),
            ])
            ->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('criteria.name')
                    ->label('Criteria')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('criteria.category')
                    ->label('Category')
                    ->badge()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('score')
                    ->label('Score')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('criteria.max_score')
                    ->label('Max Score')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(30),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('criteria.category')
                    ->label('Category')
                    ->options([
                        'service' => 'Service',
                        'cleanliness' => 'Cleanliness',
                        'food_quality' => 'Food Quality',
                        'ambiance' => 'Ambiance',
                        'staff' => 'Staff',
                        'other' => 'Other',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data, string $model): mixed {
                        $evaluation = $this->getOwnerRecord();
                        
                        return app(EvaluationService::class)->scoreCriteria(
                            $evaluation->id,
                            $data['evaluation_criteria_id'],
                            [
                                'score' => $data['score'],
                                'notes' => $data['notes'] ?? null,
                            ]
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form(fn (Form $form) => $form
                        ->schema([
                            Forms\Components\Select::make('evaluation_criteria_id')
                                ->label('Criteria')
                                ->options(function (RelationManager $livewire, $record) {
                                    $evaluation = $livewire->getOwnerRecord();
                                    $existingCriteriaIds = $evaluation->criteriaScores()
                                        ->where('id', '!=', $record->id)
                                        ->pluck('evaluation_criteria_id')
                                        ->toArray();
                                    
                                    return EvaluationCriteria::where('is_active', true)
                                        ->whereNotIn('id', $existingCriteriaIds)
                                        ->pluck('name', 'id');
                                })
                                ->required()
                                ->searchable()
                                ->preload()
                                ->columnSpan(2)
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state) {
                                        $criteria = EvaluationCriteria::find($state);
                                        $set('max_score', $criteria ? $criteria->max_score : null);
                                    }
                                })
                                ->helperText('Only criteria not yet scored for this evaluation are shown'),
                            
                            Forms\Components\TextInput::make('max_score')
                                ->label('Maximum Score')
                                ->disabled()
                                ->dehydrated(false)
                                ->columnSpan(1),
                            
                            Forms\Components\TextInput::make('score')
                                ->label('Score')
                                ->numeric()
                                ->required()
                                ->minValue(0)
                                ->maxValue(function (callable $get) {
                                    $criteriaId = $get('evaluation_criteria_id');
                                    if (!$criteriaId) return 100;
                                    
                                    $criteria = EvaluationCriteria::find($criteriaId);
                                    return $criteria ? $criteria->max_score : 100;
                                })
                                ->columnSpan(1),
                            
                            Forms\Components\Textarea::make('notes')
                                ->label('Notes')
                                ->columnSpan('full'),
                        ])
                        ->columns(3)
                    )
                    ->mutateFormDataUsing(function (array $data): array {
                        // Add the max_score for the form
                        $criteria = EvaluationCriteria::find($data['evaluation_criteria_id']);
                        $data['max_score'] = $criteria ? $criteria->max_score : null;
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}