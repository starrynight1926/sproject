<?php

namespace App\Filament\Resources\ExcelFormulas\Pages;

use App\Filament\Resources\ExcelFormulas\ExcelFormulaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExcelFormulas extends ListRecords
{
    protected static string $resource = ExcelFormulaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
