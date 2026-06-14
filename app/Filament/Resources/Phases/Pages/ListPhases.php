<?php

namespace App\Filament\Resources\Phases\Pages;

use App\Filament\Resources\Phases\PhaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPhases extends ListRecords
{
    protected static string $resource = PhaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
