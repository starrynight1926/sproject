<?php

namespace App\Filament\Resources\Phases\Schemas;

use App\Filament\Support\RelationOptionForms;
use App\Filament\Support\SelectCreateOption;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PhaseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SelectCreateOption::inline(
                    Select::make('project_id')
                        ->relationship('project', 'name')
                        ->label('Dự án')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->createOptionForm(RelationOptionForms::project())
                ),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Tên đợt')
                    ->placeholder('VD: Đợt 1, Giai đoạn khởi động'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->label('Thứ tự'),
                DatePicker::make('start_date')
                    ->label('Ngày bắt đầu')
                    ->displayFormat('d/m/Y')
                    ->native(false),
                DatePicker::make('end_date')
                    ->label('Ngày kết thúc')
                    ->displayFormat('d/m/Y')
                    ->native(false)
                    ->afterOrEqual('start_date'),
            ]);
    }
}
