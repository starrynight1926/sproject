<?php

namespace App\Filament\Resources\Goals\Schemas;

use App\Filament\Support\MoneyInput;
use App\Filament\Support\RelationOptionForms;
use App\Filament\Support\SelectCreateOption;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GoalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Tên mục tiêu'),
                Select::make('metric_type')
                    ->label('Loại chỉ số')
                    ->options([
                        'target' => 'Mục tiêu',
                        'cost' => 'Chi phí',
                        'resource' => 'Nguồn lực',
                        'time' => 'Thời gian',
                    ])
                    ->default('target')
                    ->required(),
                SelectCreateOption::inline(
                    Select::make('project_id')
                        ->relationship('project', 'name')
                        ->label('Dự án')
                        ->searchable()
                        ->preload()
                        ->createOptionForm(RelationOptionForms::project())
                ),
                SelectCreateOption::inline(
                    Select::make('phase_id')
                        ->relationship('phase', 'name')
                        ->label('Đợt (Phase)')
                        ->searchable()
                        ->preload()
                        ->createOptionForm(RelationOptionForms::phase())
                ),
                SelectCreateOption::inline(
                    Select::make('task_id')
                        ->relationship('task', 'title')
                        ->label('Công việc')
                        ->searchable()
                        ->preload()
                        ->createOptionForm(RelationOptionForms::task())
                ),
                SelectCreateOption::inline(
                    Select::make('work_item_id')
                        ->relationship('workItem', 'title')
                        ->label('Đầu việc')
                        ->searchable()
                        ->preload()
                        ->createOptionForm(RelationOptionForms::workItem())
                ),
                Select::make('type')
                    ->options([
                        'money' => 'Money',
                        'quantity' => 'Quantity',
                        'percent' => 'Percent',
                    ])
                    ->default('quantity')
                    ->required(),
                TextInput::make('unit')
                    ->label('Đơn vị')
                    ->maxLength(255)
                    ->placeholder('VD: giờ, VNĐ, người, %'),
                TextInput::make('target_value')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Target Value')
                    ->placeholder('100')
                    ->mask(MoneyInput::mask())
                    ->stripCharacters(MoneyInput::stripCharacters()),
                TextInput::make('current_value')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Current Value')
                    ->placeholder('0')
                    ->mask(MoneyInput::mask())
                    ->stripCharacters(MoneyInput::stripCharacters()),
            ]);
    }
}
