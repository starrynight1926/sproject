<?php

namespace App\Filament\Resources\ExcelFormulas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExcelFormulaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('VD: VLOOKUP'),
                Select::make('category')
                    ->required()
                    ->options([
                        'Tìm kiếm' => 'Tìm kiếm',
                        'Thống kê' => 'Thống kê',
                        'Logic' => 'Logic',
                        'Văn bản' => 'Văn bản',
                        'Ngày giờ' => 'Ngày giờ',
                        'Toán học' => 'Toán học',
                    ])
                    ->searchable(),
                TextInput::make('syntax')
                    ->required()
                    ->columnSpanFull()
                    ->placeholder('VD: =VLOOKUP(lookup_value, table_array, col_index, [range_lookup])'),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull()
                    ->rows(3),
                Textarea::make('note')
                    ->columnSpanFull()
                    ->rows(2)
                    ->placeholder('Ghi chú thêm, mẹo sử dụng...'),
                TextInput::make('example')
                    ->maxLength(255)
                    ->placeholder('VD: =VLOOKUP(A1, B:C, 2, FALSE)'),
                TagsInput::make('tags')
                    ->placeholder('Thêm tag...')
                    ->separator(','),
            ]);
    }
}
