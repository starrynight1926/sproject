<?php

namespace App\Filament\Resources\Phases;

use App\Filament\Resources\Phases\Pages\CreatePhase;
use App\Filament\Resources\Phases\Pages\EditPhase;
use App\Filament\Resources\Phases\Pages\ListPhases;
use App\Filament\Resources\Phases\Schemas\PhaseForm;
use App\Filament\Resources\Phases\Tables\PhasesTable;
use App\Models\Phase;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PhaseResource extends Resource
{
    protected static ?string $model = Phase::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Dự án';

    protected static ?string $navigationLabel = 'Đợt (Phases)';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return PhaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PhasesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPhases::route('/'),
            'create' => CreatePhase::route('/create'),
            'edit' => EditPhase::route('/{record}/edit'),
        ];
    }
}
