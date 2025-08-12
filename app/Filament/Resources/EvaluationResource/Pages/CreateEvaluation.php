<?php

namespace App\Filament\Resources\EvaluationResource\Pages;

use App\Filament\Resources\EvaluationResource;
use App\Services\EvaluationService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateEvaluation extends CreateRecord
{
    protected static string $resource = EvaluationResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = $data['user_id'] ?? Auth::id();
        return $data;
    }
    
    protected function afterCreate(): void
    {
        // Calculate total score after creation
        app(EvaluationService::class)->calculateTotalScore($this->record->id);
    }
}