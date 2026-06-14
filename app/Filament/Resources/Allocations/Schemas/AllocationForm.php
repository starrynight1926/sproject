<?php

namespace App\Filament\Resources\Allocations\Schemas;

use App\Models\Allocation;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
                    ->description('Chọn dự án và đối tượng được nhận phân bổ (phòng ban, hoặc vị trí/nhân sự thuộc một phòng ban đã phân bổ).')
                    ->icon(Heroicon::OutlinedShare)
                    ->columns(2)
                    ->components([
                        Select::make('project_id')
                            ->label('Dự án')
                            ->relationship('project', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('parent_id', null)),
                        Select::make('parent_id')
                            ->label('Thuộc phân bổ phòng ban')
                            ->options(function (Get $get) {
                                $projectId = $get('project_id');

                                if (! $projectId) {
                                    return [];
                                }

                                return Allocation::query()
                                    ->where('project_id', $projectId)
                                    ->whereNull('parent_id')
                                    ->with('orgUnit')
                                    ->get()
                                    ->mapWithKeys(fn (Allocation $allocation) => [
                                        $allocation->id => $allocation->orgUnit?->name ?? "#{$allocation->id}",
                                    ]);
                            })
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->placeholder('— Phân bổ cấp phòng ban —')
                            ->helperText('Chỉ chọn khi chia tiếp xuống vị trí/nhân sự trong phòng ban.'),
                        Select::make('org_unit_id')
                            ->label('Phòng ban')
                            ->relationship('orgUnit', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->columnSpanFull(),
                        Select::make('position_id')
                            ->label('Vị trí')
                            ->relationship('position', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('user_id')
                            ->label('Nhân sự')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false),
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
                            ->minValue(0),
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
