<?php

namespace App\Filament\Resources\ExcelFormulas\Pages;

use App\Filament\Resources\ExcelFormulas\ExcelFormulaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExcelFormula extends EditRecord
{
    protected static string $resource = ExcelFormulaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
