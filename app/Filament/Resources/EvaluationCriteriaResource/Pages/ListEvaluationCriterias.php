<?php

namespace App\Filament\Resources\EvaluationCriteriaResource\Pages;

use App\Filament\Resources\EvaluationCriteriaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvaluationCriterias extends ListRecords
{
    protected static string $resource = EvaluationCriteriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
