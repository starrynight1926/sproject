<?php

namespace App\Filament\Resources\Phases\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PhasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.name')
                    ->label('Dự án')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Tên đợt')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Bắt đầu')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Kết thúc')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('goals_count')
                    ->label('Số mục tiêu')
                    ->counts('goals'),
            ])
            ->defaultSort('sort_order')
            ->filters([
                SelectFilter::make('project_id')
                    ->relationship('project', 'name')
                    ->label('Dự án')
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
