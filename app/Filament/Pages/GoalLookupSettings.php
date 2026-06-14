<?php

namespace App\Filament\Pages;

use App\Models\GoalType;
use App\Models\GoalUnit;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class GoalLookupSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static string|UnitEnum|null $navigationGroup = 'Cài đặt hệ thống';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Cài đặt định mức';

    protected static ?string $title = 'Cài đặt định mức';

    protected static ?string $slug = 'goal-lookup-settings';

    protected string $view = 'filament.pages.goal-lookup-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->data = [
            'goal_types' => GoalType::orderBy('sort_order')->get(['key', 'label'])->toArray(),
            'goal_units' => GoalUnit::orderBy('sort_order')->get(['name'])->toArray(),
        ];

        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Type công việc')
                    ->description('Danh sách loại định mức công việc (dùng trong "Định mức công việc").')
                    ->schema([
                        Repeater::make('goal_types')
                            ->label('')
                            ->schema([
                                TextInput::make('key')
                                    ->label('Mã (key)')
                                    ->required()
                                    ->maxLength(50),
                                TextInput::make('label')
                                    ->label('Tên hiển thị')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->reorderable()
                            ->addActionLabel('+ Thêm loại')
                            ->defaultItems(0),
                    ]),

                Section::make('Đơn vị')
                    ->description('Danh sách đơn vị tính (file, video, hợp đồng, người, ...).')
                    ->schema([
                        Repeater::make('goal_units')
                            ->label('')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Tên đơn vị')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->reorderable()
                            ->addActionLabel('+ Thêm đơn vị')
                            ->defaultItems(0),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        DB::transaction(function () use ($state) {
            GoalType::query()->delete();
            foreach ($state['goal_types'] ?? [] as $index => $type) {
                GoalType::create([
                    'key' => $type['key'],
                    'label' => $type['label'],
                    'sort_order' => $index,
                ]);
            }

            GoalUnit::query()->delete();
            foreach ($state['goal_units'] ?? [] as $index => $unit) {
                GoalUnit::create([
                    'name' => $unit['name'],
                    'sort_order' => $index,
                ]);
            }
        });

        $this->mount();

        \Filament\Notifications\Notification::make()
            ->title('Đã lưu cài đặt định mức')
            ->success()
            ->send();
    }
}
