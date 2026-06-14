<?php

namespace App\Filament\Resources\EmployeeGoals\Schemas;

use App\Models\GoalType;
use App\Models\GoalUnit;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeeGoalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('position_id')
                    ->relationship('position', 'name')
                    ->label('Vị trí')
                    ->searchable()
                    ->preload(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Nhân viên')
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Đầu việc / Chỉ tiêu')
                    ->placeholder('VD: Thiết kế file ấn phẩm'),
                Select::make('type')
                    ->options(fn () => GoalType::orderBy('sort_order')->pluck('label', 'key')->toArray())
                    ->default('quantity')
                    ->required(),
                Select::make('unit')
                    ->label('Đơn vị')
                    ->options(fn () => GoalUnit::orderBy('sort_order')->pluck('name', 'name')->toArray())
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Tên đơn vị')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->createOptionUsing(function (array $data) {
                        GoalUnit::firstOrCreate(['name' => $data['name']], ['name' => $data['name']]);

                        return $data['name'];
                    }),
                TextInput::make('target_value')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Định mức'),
                TextInput::make('current_value')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Đã thực hiện'),
                TextInput::make('period')
                    ->label('Kỳ')
                    ->maxLength(255)
                    ->placeholder('VD: 2026'),
            ]);
    }
}
