<?php

namespace App\Filament\Resources\ExcelFormulas;

use App\Filament\Resources\ExcelFormulas\Pages\CreateExcelFormula;
use App\Filament\Resources\ExcelFormulas\Pages\EditExcelFormula;
use App\Filament\Resources\ExcelFormulas\Pages\ListExcelFormulas;
use App\Filament\Resources\ExcelFormulas\Schemas\ExcelFormulaForm;
use App\Filament\Resources\ExcelFormulas\Tables\ExcelFormulasTable;
use App\Models\ExcelFormula;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ExcelFormulaResource extends Resource
{
    protected static ?string $model = ExcelFormula::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTableCells;

    protected static string|UnitEnum|null $navigationGroup = 'Khác';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ExcelFormulaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExcelFormulasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExcelFormulas::route('/'),
            'create' => CreateExcelFormula::route('/create'),
            'edit' => EditExcelFormula::route('/{record}/edit'),
        ];
    }
}
