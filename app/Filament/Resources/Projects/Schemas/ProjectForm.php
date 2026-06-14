<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Support\MoneyInput;
use App\Filament\Support\OrgOptionForms;
use App\Filament\Support\SelectCreateOption;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Tên dự án'),
                SelectCreateOption::inline(
                    Select::make('owner_id')
                        ->relationship('owner', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->createOptionForm(OrgOptionForms::user())
                ),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(3)
                    ->placeholder('Mô tả dự án...'),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'done' => 'Done',
                        'archive' => 'Archive',
                    ])
                    ->default('active')
                    ->required(),
                DatePicker::make('deadline')
                    ->displayFormat('d/m/Y')
                    ->native(false),
                TextInput::make('budget')
                    ->label('Tổng ngân sách')
                    ->numeric()
                    ->default(0)
                    ->prefix('₫')
                    ->mask(MoneyInput::mask())
                    ->stripCharacters(MoneyInput::stripCharacters()),
            ]);
    }
}
