<?php

namespace App\Filament\Resources\Phases\Pages;

use App\Filament\Resources\Phases\PhaseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPhase extends EditRecord
{
    protected static string $resource = PhaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
