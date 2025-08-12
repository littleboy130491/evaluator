<?php

namespace App\Filament\Resources\EvaluationResource\Pages;

use App\Filament\Resources\EvaluationResource;
use App\Services\EvaluationService;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEvaluation extends ViewRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => $this->record->status !== 'approved' || auth()->user()->can('edit_approved_evaluations')),
                
            Actions\Action::make('approve')
                ->label('Approve')
                ->color('success')
                ->icon('heroicon-o-check')
                ->visible(fn () => $this->record->status === 'pending' && auth()->user()->can('approve_evaluations'))
                ->action(function () {
                    app(EvaluationService::class)->approveEvaluation($this->record->id);
                    $this->refresh();
                    $this->notify('success', 'Evaluation approved successfully');
                }),
                
            Actions\Action::make('reject')
                ->label('Reject')
                ->color('danger')
                ->icon('heroicon-o-x-mark')
                ->visible(fn () => $this->record->status === 'pending' && auth()->user()->can('approve_evaluations'))
                ->action(function () {
                    app(EvaluationService::class)->rejectEvaluation($this->record->id);
                    $this->refresh();
                    $this->notify('success', 'Evaluation rejected');
                }),
        ];
    }
}