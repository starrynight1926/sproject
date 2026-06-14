<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Filament\Support\OrgOptionForms;
use App\Filament\Support\RelationOptionForms;
use App\Filament\Support\SelectCreateOption;
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
                SelectCreateOption::inline(
                    Select::make('project_id')
                        ->relationship('project', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->createOptionForm(RelationOptionForms::project())
                ),
                SelectCreateOption::inline(
                    Select::make('goal_id')
                        ->relationship('goal', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Không gắn goal')
                        ->createOptionForm(RelationOptionForms::goal())
                ),
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
                    ->displayFormat('d/m/Y')
                    ->native(false),
                DatePicker::make('due_date')
                    ->displayFormat('d/m/Y')
                    ->native(false)
                    ->afterOrEqual('start_date'),
                SelectCreateOption::inline(
                    Select::make('assignees')
                        ->relationship('assignees', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->columnSpanFull()
                        ->createOptionForm(OrgOptionForms::user())
                ),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
