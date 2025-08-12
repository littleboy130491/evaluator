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
                    ->options(EvaluationCriteria::where('is_active', true)->pluck('name', 'id'))
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
                    }),
                
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
                    ->label('Internal Notes')
                    ->columnSpan('full'),
                
                Forms\Components\Textarea::make('evaluator_comments')
                    ->label('Evaluator Comments')
                    ->columnSpan('full'),
                
                Forms\Components\TextInput::make('evidence_url')
                    ->label('Evidence URL')
                    ->url()
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
                
                Tables\Columns\TextColumn::make('evaluator_comments')
                    ->label('Comments')
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
                                'evaluator_comments' => $data['evaluator_comments'] ?? null,
                                'evidence_url' => $data['evidence_url'] ?? null,
                            ]
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
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