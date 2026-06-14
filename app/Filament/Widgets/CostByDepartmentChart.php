<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Services\ProjectMetricsService;
use Filament\Widgets\ChartWidget;

class CostByDepartmentChart extends ChartWidget
{
    protected ?string $heading = 'Chi phí theo công việc (Kế hoạch vs Thực tế)';

    protected static ?int $sort = -4;

    protected function getData(): array
    {
        $project = Project::where('status', 'active')->first() ?? Project::first();

        if (! $project) {
            return ['datasets' => [], 'labels' => []];
        }

        $data = app(ProjectMetricsService::class)->metricByTask($project, 'cost');

        return [
            'datasets' => [
                [
                    'label' => 'Đã chi',
                    'data' => $data['current'],
                    'backgroundColor' => '#f59e0b',
                ],
                [
                    'label' => 'Ngân sách',
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
            'scales' => [
                'y' => ['beginAtZero' => true],
            ],
        ];
    }
}
