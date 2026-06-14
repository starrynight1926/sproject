<?php

namespace App\Filament\Support;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class RelationOptionForms
{
    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public static function project(): array
    {
        return [
            TextInput::make('name')
                ->label('Tên dự án')
                ->required()
                ->maxLength(255),
            Select::make('status')
                ->label('Trạng thái')
                ->options([
                    'active' => 'Active',
                    'done' => 'Done',
                    'archive' => 'Archive',
                ])
                ->default('active')
                ->required(),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public static function phase(): array
    {
        return [
            Select::make('project_id')
                ->label('Dự án')
                ->relationship('project', 'name')
                ->required()
                ->searchable()
                ->preload(),
            TextInput::make('name')
                ->label('Tên đợt')
                ->required()
                ->maxLength(255),
            DatePicker::make('start_date')
                ->label('Ngày bắt đầu')
                ->displayFormat('d/m/Y')
                ->native(false),
            DatePicker::make('end_date')
                ->label('Ngày kết thúc')
                ->displayFormat('d/m/Y')
                ->native(false)
                ->afterOrEqual('start_date'),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public static function task(): array
    {
        return [
            TextInput::make('title')
                ->label('Tên task')
                ->required()
                ->maxLength(255),
            Select::make('project_id')
                ->label('Dự án')
                ->relationship('project', 'name')
                ->required()
                ->searchable()
                ->preload(),
            Select::make('status')
                ->label('Trạng thái')
                ->options([
                    'idea' => 'Idea',
                    'draft' => 'Draft',
                    'beta' => 'Beta',
                    'edit' => 'Edit',
                    'done' => 'Done',
                ])
                ->default('idea')
                ->required(),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public static function workItem(): array
    {
        return [
            TextInput::make('title')
                ->label('Tên đầu việc')
                ->required()
                ->maxLength(255),
            Select::make('task_id')
                ->label('Công việc')
                ->relationship('task', 'title')
                ->required()
                ->searchable()
                ->preload(),
            Select::make('status')
                ->label('Trạng thái')
                ->options([
                    'idea' => 'Ý tưởng',
                    'draft' => 'Bản thảo',
                    'beta' => 'Beta',
                    'edit' => 'Đang chỉnh sửa',
                    'done' => 'Hoàn thành',
                ])
                ->default('idea')
                ->required(),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public static function goal(): array
    {
        return [
            TextInput::make('name')
                ->label('Tên mục tiêu')
                ->required()
                ->maxLength(255),
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
            TextInput::make('target_value')
                ->label('Target Value')
                ->numeric()
                ->default(0)
                ->required()
                ->mask(MoneyInput::mask())
                ->stripCharacters(MoneyInput::stripCharacters()),
        ];
    }
}
