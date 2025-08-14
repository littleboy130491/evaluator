<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupAreaResource\Pages;
use App\Filament\Resources\GroupAreaResource\RelationManagers;
use App\Models\GroupArea;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GroupAreaResource extends Resource
{
    protected static ?string $model = GroupArea::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->description;
                    }),
                Tables\Columns\TextColumn::make('outlets_count')
                    ->counts('outlets')
                    ->label('Outlets'),
                Tables\Columns\TextColumn::make('auditors_count')
                    ->counts('auditors')
                    ->label('Auditors'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OutletsRelationManager::class,
            RelationManagers\AuditorsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroupAreas::route('/'),
            'create' => Pages\CreateGroupArea::route('/create'),
            'edit' => Pages\EditGroupArea::route('/{record}/edit'),
        ];
    }
}
