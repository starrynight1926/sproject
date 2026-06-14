<?php

namespace App\Filament\Widgets;

use App\Models\Goal;
use Filament\Widgets\ChartWidget;

class GoalProgressChart extends ChartWidget
{
    protected ?string $heading = 'Goal Progress';

    protected static ?int $sort = -1;

    protected function getData(): array
    {
        $goals = Goal::all();

        $labels = [];
        $currentValues = [];
        $remainingValues = [];

        foreach ($goals as $goal) {
            $labels[] = $goal->name;
            $current = min($goal->current_value, $goal->target_value);
            $currentValues[] = $current;
            $remainingValues[] = max($goal->target_value - $current, 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Current',
                    'data' => $currentValues,
                    'backgroundColor' => '#10b981',
                ],
                [
                    'label' => 'Remaining',
                    'data' => $remainingValues,
                    'backgroundColor' => '#e5e7eb',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
