<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Services\ProjectMetricsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectKpiOverview extends BaseWidget
{
    protected static ?int $sort = -5;

    protected function getStats(): array
    {
        $project = Project::where('status', 'active')->first() ?? Project::first();

        if (! $project) {
            return [];
        }

        $summary = app(ProjectMetricsService::class)->summary($project);

        return [
            Stat::make('Doanh thu', number_format($summary['revenue_percent'], 1).'%')
                ->description(number_format($summary['revenue_current']).' / '.number_format($summary['revenue_target']))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color($summary['revenue_percent'] >= 100 ? 'success' : ($summary['revenue_percent'] >= 50 ? 'warning' : 'danger')),

            Stat::make('Công việc', $summary['tasks_done'].' / '.$summary['tasks_total'])
                ->description('Đầu việc đã hoàn thành')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($summary['tasks_total'] > 0 && $summary['tasks_done'] === $summary['tasks_total'] ? 'success' : 'info'),

            Stat::make('Thời gian', $summary['time_on_track'].' / '.$summary['time_total'])
                ->description('Đầu việc đạt tiến độ')
                ->descriptionIcon('heroicon-m-clock')
                ->color($summary['time_total'] > 0 && $summary['time_on_track'] === $summary['time_total'] ? 'success' : 'warning'),

            Stat::make('Nhân sự', $summary['staff_kpi_ok'].' / '.$summary['staff_total'])
                ->description('Đạt KPI trên 80%')
                ->descriptionIcon('heroicon-m-users')
                ->color($summary['staff_total'] > 0 && $summary['staff_kpi_ok'] === $summary['staff_total'] ? 'success' : 'warning'),
        ];
    }
}
