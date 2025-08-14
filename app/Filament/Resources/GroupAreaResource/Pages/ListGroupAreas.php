<?php

namespace App\Filament\Resources\GroupAreaResource\Pages;

use App\Filament\Resources\GroupAreaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGroupAreas extends ListRecords
{
    protected static string $resource = GroupAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
