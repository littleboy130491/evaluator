<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluationResource\Pages;
use App\Filament\Resources\EvaluationResource\RelationManagers;
use App\Models\Evaluation;
use App\Models\User;
use App\Services\EvaluationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Evaluations';
    
    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Evaluation Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Evaluator')
                            ->default(fn () => Auth::id())
                            ->required()
                            ->disabled(fn ($record) => $record && $record->status !== 'pending'),
                        
                        Forms\Components\Select::make('outlet_id')
                            ->relationship('outlet', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(fn ($record) => $record && $record->status !== 'pending'),
                        
                        Forms\Components\DatePicker::make('evaluation_date')
                            ->required()
                            ->default(now())
                            ->disabled(fn ($record) => $record && $record->status !== 'pending'),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'completed' => 'Completed',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending')
                            ->required()
                            ->disabled(fn ($record) => $record && in_array($record->status, ['approved', 'rejected'])),
                        
                        Forms\Components\Textarea::make('notes')
                            ->columnSpan('full')
                            ->disabled(fn ($record) => $record && in_array($record->status, ['approved', 'rejected'])),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Evaluator')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('outlet.name')
                    ->label('Outlet')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('evaluation_date')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'completed' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Evaluator'),
                
                Tables\Filters\SelectFilter::make('outlet_id')
                    ->relationship('outlet', 'name')
                    ->label('Outlet'),
                
                Tables\Filters\Filter::make('evaluation_date')
                    ->form([
                        Forms\Components\DatePicker::make('evaluation_date_from'),
                        Forms\Components\DatePicker::make('evaluation_date_to'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['evaluation_date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('evaluation_date', '>=', $date),
                            )
                            ->when(
                                $data['evaluation_date_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('evaluation_date', '<=', $date),
                            );
                    }),
                
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Evaluation $record) => $record->status === 'completed' && Auth::user()->can('approve evaluations'))
                    ->action(function (Evaluation $record) {
                        app(EvaluationService::class)->approveEvaluation($record->id, Auth::id());
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Evaluation $record) => $record->status === 'completed' && Auth::user()->can('reject evaluations'))
                    ->action(function (Evaluation $record) {
                        app(EvaluationService::class)->rejectEvaluation($record->id, Auth::id());
                    }),
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
            RelationManagers\CriteriaScoresRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvaluations::route('/'),
            'create' => Pages\CreateEvaluation::route('/create'),
            'view' => Pages\ViewEvaluation::route('/{record}'),
            'edit' => Pages\EditEvaluation::route('/{record}/edit'),
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