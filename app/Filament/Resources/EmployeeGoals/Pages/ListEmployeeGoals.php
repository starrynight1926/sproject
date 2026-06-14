<?php

namespace App\Filament\Resources\EmployeeGoals\Pages;

use App\Filament\Resources\EmployeeGoals\EmployeeGoalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeGoals extends ListRecords
{
    protected static string $resource = EmployeeGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
