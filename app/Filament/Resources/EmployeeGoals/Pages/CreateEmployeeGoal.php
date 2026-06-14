<?php

namespace App\Filament\Resources\EmployeeGoals\Pages;

use App\Filament\Resources\EmployeeGoals\EmployeeGoalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployeeGoal extends CreateRecord
{
    protected static string $resource = EmployeeGoalResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
