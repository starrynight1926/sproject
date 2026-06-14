<?php

namespace App\Filament\Resources\EmployeeGoals\Tables;

use App\Models\GoalType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmployeeGoalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('position.name')
                    ->label('Vị trí')
                    ->sortable()
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('user.name')
                    ->label('Nhân viên')
                    ->sortable()
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('name')
                    ->label('Đầu việc / Chỉ tiêu')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('type')
                    ->label('Loại')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => GoalType::where('key', $state)->value('label') ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'money' => 'success',
                        'quantity' => 'info',
                        'percent' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('target_value')
                    ->label('Định mức')
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('current_value')
                    ->label('Đã thực hiện')
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('unit')
                    ->label('Đơn vị')
                    ->toggleable(),
                TextColumn::make('period')
                    ->label('Kỳ')
                    ->toggleable()
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
