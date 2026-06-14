<?php

namespace App\Filament\Resources\Goals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GoalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('project.name')
                    ->sortable()
                    ->label('Project'),
                TextColumn::make('metric_type')
                    ->label('Loại')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'target' => 'Mục tiêu',
                        'cost' => 'Chi phí',
                        'resource' => 'Nguồn lực',
                        'time' => 'Thời gian',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'target' => 'success',
                        'cost' => 'warning',
                        'resource' => 'info',
                        'time' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('phase.name')
                    ->label('Phase')
                    ->toggleable(),
                TextColumn::make('unit')
                    ->label('Đơn vị')
                    ->toggleable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'money' => 'success',
                        'quantity' => 'info',
                        'percent' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('current_value')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->label('Current'),
                TextColumn::make('target_value')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->label('Target'),
                TextColumn::make('progress')
                    ->label('Progress')
                    ->getStateUsing(fn ($record) => $record->target_value > 0
                        ? round(($record->current_value / $record->target_value) * 100, 1) . '%'
                        : '0%')
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->target_value <= 0 => 'gray',
                        ($record->current_value / $record->target_value) >= 1 => 'success',
                        ($record->current_value / $record->target_value) >= 0.5 => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('metric_type')
                    ->label('Loại')
                    ->options([
                        'target' => 'Mục tiêu',
                        'cost' => 'Chi phí',
                        'resource' => 'Nguồn lực',
                        'time' => 'Thời gian',
                    ]),
                SelectFilter::make('type')
                    ->options([
                        'money' => 'Money',
                        'quantity' => 'Quantity',
                        'percent' => 'Percent',
                    ]),
                SelectFilter::make('project_id')
                    ->relationship('project', 'name')
                    ->label('Project')
                    ->searchable()
                    ->preload(),
            ])
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
