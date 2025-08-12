<?php

namespace App\Filament\Resources\EvaluationCriteriaResource\Pages;

use App\Filament\Resources\EvaluationCriteriaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvaluationCriteria extends EditRecord
{
    protected static string $resource = EvaluationCriteriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
