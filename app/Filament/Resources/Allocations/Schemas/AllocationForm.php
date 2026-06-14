<?php

namespace App\Filament\Resources\Allocations\Schemas;

use App\Filament\Support\MoneyInput;
use App\Filament\Support\OrgOptionForms;
use App\Filament\Support\SelectCreateOption;
use App\Models\Allocation;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class AllocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Phạm vi phân bổ')
                    ->description('Chọn dự án và phòng ban được nhận phân bổ. Có thể chọn thêm vị trí/nhân sự cụ thể trong phòng ban đó nếu cần.')
                    ->icon(Heroicon::OutlinedShare)
                    ->columns(2)
                    ->components([
                        Select::make('project_id')
                            ->label('Dự án')
                            ->relationship('project', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),
                        SelectCreateOption::inline(
                            Select::make('org_unit_id')
                                ->label('Phòng ban')
                                ->relationship('orgUnit', 'name')
                                ->options(fn () => OrgOptionForms::orgUnitOptions())
                                ->getOptionLabelFromRecordUsing(fn ($record) => OrgOptionForms::orgUnitOptions()[$record->id] ?? $record->name)
                                ->searchable()
                                ->preload()
                                ->native(false)
                                ->createOptionForm(OrgOptionForms::orgUnit())
                        ),
                        SelectCreateOption::inline(
                            Select::make('position_id')
                                ->label('Vị trí')
                                ->relationship('position', 'name')
                                ->searchable()
                                ->preload()
                                ->native(false)
                                ->createOptionForm(OrgOptionForms::position())
                        ),
                        SelectCreateOption::inline(
                            Select::make('user_id')
                                ->label('Nhân sự')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->native(false)
                                ->createOptionForm(OrgOptionForms::user())
                        ),
                    ]),
                Section::make('Chỉ tiêu phân bổ')
                    ->description('Nhập số liệu phân bổ cho đối tượng đã chọn ở trên. Có thể nhập vượt mức để theo dõi/điều chỉnh.')
                    ->icon(Heroicon::OutlinedChartBar)
                    ->columns(3)
                    ->components([
                        TextInput::make('target_value')
                            ->label('Mục tiêu')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->mask(MoneyInput::mask())
                            ->stripCharacters(MoneyInput::stripCharacters()),
                        TextInput::make('staff_count')
                            ->label('Nhân sự')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->suffix('người'),
                        TextInput::make('budget_amount')
                            ->label('Kinh phí')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->prefix('₫')
                            ->mask(MoneyInput::mask())
                            ->stripCharacters(MoneyInput::stripCharacters())
                            ->helperText('% so với tổng ngân sách dự án sẽ tự tính.'),
                    ]),
                Section::make('Ghi chú')
                    ->icon(Heroicon::OutlinedPencilSquare)
                    ->collapsible()
                    ->collapsed(fn (?Allocation $record) => blank($record?->note))
                    ->components([
                        Textarea::make('note')
                            ->label(false)
                            ->placeholder('Ghi chú thêm về phân bổ này...')
                            ->columnSpanFull()
                            ->rows(2),
                    ]),
            ]);
    }
}
