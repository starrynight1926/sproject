<?php

namespace App\Filament\Resources\EmployeeGoals\Pages;

use App\Filament\Resources\EmployeeGoals\EmployeeGoalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeGoal extends EditRecord
{
    protected static string $resource = EmployeeGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
