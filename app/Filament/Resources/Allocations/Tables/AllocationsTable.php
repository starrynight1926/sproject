<?php

namespace App\Filament\Resources\Allocations\Tables;

use App\Filament\Support\OrgOptionForms;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AllocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.name')
                    ->label('Dự án')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('orgUnit.name')
                    ->label('Phòng ban')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('position.name')
                    ->label('Vị trí')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Nhân sự')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('parent.orgUnit.name')
                    ->label('Thuộc phòng ban')
                    ->placeholder('—'),
                TextColumn::make('target_value')
                    ->label('Mục tiêu')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('staff_count')
                    ->label('Nhân sự (tỉ lệ/số lượng)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('budget_amount')
                    ->label('Kinh phí')
                    ->money('VND')
                    ->sortable(),
                TextColumn::make('budget_percent')
                    ->label('% Ngân sách')
                    ->suffix('%')
                    ->state(fn ($record) => $record->budget_percent),
            ])
            ->filters([
                SelectFilter::make('project_id')
                    ->label('Dự án')
                    ->relationship('project', 'name'),
                SelectFilter::make('org_unit_id')
                    ->label('Phòng ban')
                    ->options(fn () => OrgOptionForms::orgUnitOptions()),
            ])
            ->defaultSort('created_at', 'desc')
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
