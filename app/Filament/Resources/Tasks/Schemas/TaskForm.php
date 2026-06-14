<?php

namespace App\Filament\Resources\Tasks\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->placeholder('Tên task'),
                Select::make('project_id')
                    ->relationship('project', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('goal_id')
                    ->relationship('goal', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Không gắn goal'),
                Select::make('status')
                    ->options([
                        'idea' => 'Idea',
                        'draft' => 'Draft',
                        'beta' => 'Beta',
                        'edit' => 'Edit',
                        'done' => 'Done',
                    ])
                    ->default('idea')
                    ->required(),
                Select::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ])
                    ->default('medium')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3)
                    ->placeholder('Mô tả task...'),
                DatePicker::make('start_date')
                    ->displayFormat('d/m/Y'),
                DatePicker::make('due_date')
                    ->displayFormat('d/m/Y'),
                Select::make('assignees')
                    ->relationship('assignees', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
