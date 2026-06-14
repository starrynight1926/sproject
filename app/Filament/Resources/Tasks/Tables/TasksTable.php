<?php

namespace App\Filament\Resources\Tasks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('project.name')
                    ->sortable()
                    ->label('Project'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'idea' => 'gray',
                        'draft' => 'warning',
                        'beta' => 'info',
                        'edit' => 'primary',
                        'done' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'warning',
                        'high' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('goal.name')
                    ->label('Goal')
                    ->toggleable(),
                TextColumn::make('assignees.name')
                    ->label('Assignees')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('due_date')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'idea' => 'Idea',
                        'draft' => 'Draft',
                        'beta' => 'Beta',
                        'edit' => 'Edit',
                        'done' => 'Done',
                    ]),
                SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ]),
                SelectFilter::make('project_id')
                    ->relationship('project', 'name')
                    ->label('Project')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('sort_order')
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
