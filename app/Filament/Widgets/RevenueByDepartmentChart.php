<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Services\ProjectMetricsService;
use Filament\Widgets\ChartWidget;

class RevenueByDepartmentChart extends ChartWidget
{
    protected ?string $heading = 'Doanh thu theo công việc (Kế hoạch vs Thực tế)';

    protected static ?int $sort = -4;

    protected function getData(): array
    {
        $project = Project::where('status', 'active')->first() ?? Project::first();

        if (! $project) {
            return ['datasets' => [], 'labels' => []];
        }

        $data = app(ProjectMetricsService::class)->metricByTask($project, 'target');

        return [
            'datasets' => [
                [
                    'label' => 'Thực tế',
                    'data' => $data['current'],
                    'backgroundColor' => '#22d3a0',
                ],
                [
                    'label' => 'Kế hoạch',
                    'data' => $data['target'],
                    'backgroundColor' => 'rgba(148,163,184,0.35)',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'scales' => [
                'x' => ['beginAtZero' => true],
            ],
        ];
    }
}
