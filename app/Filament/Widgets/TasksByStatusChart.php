<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;

class TasksByStatusChart extends ChartWidget
{
    protected ?string $heading = 'Tasks by Status';

    protected static ?int $sort = -8;

    protected function getData(): array
    {
        $statuses = ['idea', 'draft', 'beta', 'edit', 'done'];
        $counts = [];

        foreach ($statuses as $status) {
            $counts[] = Task::where('status', $status)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tasks',
                    'data' => $counts,
                    'backgroundColor' => [
                        '#6b7280', // idea - gray
                        '#f59e0b', // draft - amber
                        '#3b82f6', // beta - blue
                        '#8b5cf6', // edit - violet
                        '#10b981', // done - emerald
                    ],
                ],
            ],
            'labels' => ['Idea', 'Draft', 'Beta', 'Edit', 'Done'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
