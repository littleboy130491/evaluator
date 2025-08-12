<?php

namespace App\Filament\Resources\EvaluationResource\Pages;

use App\Filament\Resources\EvaluationResource;
use App\Services\EvaluationService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvaluation extends EditRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
            
            Actions\Action::make('approve')
                ->label('Approve')
                ->color('success')
                ->icon('heroicon-o-check')
                ->visible(fn () => $this->record->status === 'pending' && auth()->user()->can('approve_evaluations'))
                ->action(function () {
                    app(EvaluationService::class)->approveEvaluation($this->record->id);
                    $this->refreshFormData(['status']);
                    $this->notify('success', 'Evaluation approved successfully');
                }),
                
            Actions\Action::make('reject')
                ->label('Reject')
                ->color('danger')
                ->icon('heroicon-o-x-mark')
                ->visible(fn () => $this->record->status === 'pending' && auth()->user()->can('approve_evaluations'))
                ->action(function () {
                    app(EvaluationService::class)->rejectEvaluation($this->record->id);
                    $this->refreshFormData(['status']);
                    $this->notify('success', 'Evaluation rejected');
                }),
        ];
    }
    
    protected function afterSave(): void
    {
        // Recalculate total score after saving
        app(EvaluationService::class)->calculateTotalScore($this->record->id);
    }
}