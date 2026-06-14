<?php

namespace App\Filament\Resources\Goals\Schemas;

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
                Select::make('project_id')
                    ->relationship('project', 'name')
                    ->label('Dự án')
                    ->searchable()
                    ->preload(),
                Select::make('phase_id')
                    ->relationship('phase', 'name')
                    ->label('Đợt (Phase)')
                    ->searchable()
                    ->preload(),
                Select::make('task_id')
                    ->relationship('task', 'title')
                    ->label('Công việc')
                    ->searchable()
                    ->preload(),
                Select::make('work_item_id')
                    ->relationship('workItem', 'title')
                    ->label('Đầu việc')
                    ->searchable()
                    ->preload(),
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
                    ->placeholder('100'),
                TextInput::make('current_value')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Current Value')
                    ->placeholder('0'),
            ]);
    }
}
