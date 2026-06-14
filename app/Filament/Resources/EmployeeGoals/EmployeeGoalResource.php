<?php

namespace App\Filament\Resources\EmployeeGoals;

use App\Filament\Resources\EmployeeGoals\Pages\CreateEmployeeGoal;
use App\Filament\Resources\EmployeeGoals\Pages\EditEmployeeGoal;
use App\Filament\Resources\EmployeeGoals\Pages\ListEmployeeGoals;
use App\Filament\Resources\EmployeeGoals\Schemas\EmployeeGoalForm;
use App\Filament\Resources\EmployeeGoals\Tables\EmployeeGoalsTable;
use App\Models\EmployeeGoal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EmployeeGoalResource extends Resource
{
    protected static ?string $model = EmployeeGoal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Thiết lập tổ chức';

    protected static ?string $navigationLabel = 'Định mức công việc';

    protected static ?string $modelLabel = 'Định mức công việc';

    protected static ?string $pluralModelLabel = 'Định mức công việc';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return EmployeeGoalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeeGoalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployeeGoals::route('/'),
            'create' => CreateEmployeeGoal::route('/create'),
            'edit' => EditEmployeeGoal::route('/{record}/edit'),
        ];
    }
}
