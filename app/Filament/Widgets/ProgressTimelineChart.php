<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Services\ProjectMetricsService;
use Filament\Widgets\ChartWidget;

class ProgressTimelineChart extends ChartWidget
{
    protected ?string $heading = 'Tiến độ báo cáo theo thời gian';

    protected static ?int $sort = -7;

    protected function getData(): array
    {
        $project = Project::where('status', 'active')->first() ?? Project::first();

        if (! $project) {
            return ['datasets' => [], 'labels' => []];
        }

        $data = app(ProjectMetricsService::class)->progressTimeline($project);

        return [
            'datasets' => [
                [
                    'label' => 'Tiến độ báo cáo',
                    'data' => $data['data'],
                    'borderColor' => '#60a5fa',
                    'backgroundColor' => 'rgba(96,165,250,0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
