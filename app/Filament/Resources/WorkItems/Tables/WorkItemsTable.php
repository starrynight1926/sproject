<?php

namespace App\Filament\Resources\WorkItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WorkItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('task.title')
                    ->label('Công việc')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Đầu việc')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'idea' => 'Ý tưởng',
                        'draft' => 'Bản thảo',
                        'beta' => 'Beta',
                        'edit' => 'Đang chỉnh sửa',
                        'done' => 'Hoàn thành',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'done' => 'success',
                        'beta' => 'info',
                        'edit' => 'warning',
                        'draft' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('progress')
                    ->label('Tiến độ')
                    ->getStateUsing(fn ($record) => $record->progressPercent() . '%')
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        $record->progressPercent() >= 100 => 'success',
                        $record->progressPercent() >= 50 => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('start_date')
                    ->label('Bắt đầu')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Hạn')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'idea' => 'Ý tưởng',
                        'draft' => 'Bản thảo',
                        'beta' => 'Beta',
                        'edit' => 'Đang chỉnh sửa',
                        'done' => 'Hoàn thành',
                    ]),
                SelectFilter::make('task_id')
                    ->relationship('task', 'title')
                    ->label('Công việc')
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
