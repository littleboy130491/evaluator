<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistoryResource\Pages;
use App\Models\History;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HistoryResource extends Resource
{
    protected static ?string $model = History::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    
    protected static ?string $navigationGroup = 'Audit';

    protected static ?int $navigationSort = 90;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('History Details')
                    ->schema([
                        Forms\Components\TextInput::make('historiable_type')
                            ->label('Record Type')
                            ->disabled(),
                        Forms\Components\TextInput::make('historiable_id')
                            ->label('Record ID')
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('User')
                            ->disabled(),
                        Forms\Components\TextInput::make('action')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Timestamp')
                            ->disabled(),
                    ])->columns(2),
                Forms\Components\Section::make('Changes')
                    ->schema([
                        Forms\Components\JsonEditor::make('old_values')
                            ->label('Old Values')
                            ->disabled(),
                        Forms\Components\JsonEditor::make('new_values')
                            ->label('New Values')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('historiable_type')
                    ->label('Record Type')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('historiable_id')
                    ->label('Record ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'status_changed' => 'info',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'status_changed' => 'Status Changed',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListHistories::route('/'),
            'view' => Pages\ViewHistory::route('/{record}'),
        ];
    }
}