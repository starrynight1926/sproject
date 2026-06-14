<?php

namespace App\Filament\Resources\WorkItems\Schemas;

use App\Filament\Support\MoneyInput;
use App\Filament\Support\RelationOptionForms;
use App\Filament\Support\SelectCreateOption;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WorkItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SelectCreateOption::inline(
                    Select::make('task_id')
                        ->relationship('task', 'title')
                        ->label('Công việc')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->createOptionForm(RelationOptionForms::task())
                ),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Tên đầu việc'),
                Textarea::make('description')
                    ->label('Mô tả')
                    ->rows(3)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'idea' => 'Ý tưởng',
                        'draft' => 'Bản thảo',
                        'beta' => 'Beta',
                        'edit' => 'Đang chỉnh sửa',
                        'done' => 'Hoàn thành',
                    ])
                    ->default('idea')
                    ->required(),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->label('Thứ tự'),
                DatePicker::make('start_date')
                    ->label('Ngày bắt đầu')
                    ->displayFormat('d/m/Y')
                    ->native(false),
                DatePicker::make('due_date')
                    ->label('Hạn hoàn thành')
                    ->displayFormat('d/m/Y')
                    ->native(false)
                    ->afterOrEqual('start_date'),
                Repeater::make('goals')
                    ->relationship('goals')
                    ->label('Mục tiêu của đầu việc')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('metric_type')
                            ->label('Loại')
                            ->options([
                                'target' => 'Mục tiêu',
                                'cost' => 'Chi phí',
                                'resource' => 'Nguồn lực',
                                'time' => 'Thời gian',
                            ])
                            ->default('target')
                            ->required(),
                        TextInput::make('name')
                            ->label('Tên')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('unit')
                            ->label('Đơn vị')
                            ->maxLength(255),
                        TextInput::make('target_value')
                            ->label('Chỉ tiêu')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->mask(MoneyInput::mask())
                            ->stripCharacters(MoneyInput::stripCharacters()),
                        TextInput::make('current_value')
                            ->label('Hiện tại')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->mask(MoneyInput::mask())
                            ->stripCharacters(MoneyInput::stripCharacters()),
                    ])
                    ->columns(5)
                    ->defaultItems(0)
                    ->addActionLabel('+ Thêm mục tiêu'),
            ]);
    }
}
