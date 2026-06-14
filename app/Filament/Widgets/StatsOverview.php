<?php

namespace App\Filament\Widgets;

use App\Models\ExcelFormula;
use App\Models\Goal;
use App\Models\Project;
use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = -10;

    protected function getStats(): array
    {
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'active')->count();
        $totalTasks = Task::count();
        $doneTasks = Task::where('status', 'done')->count();
        $totalGoals = Goal::count();
        $totalFormulas = ExcelFormula::count();

        return [
            Stat::make('Projects', $totalProjects)
                ->description($activeProjects . ' active')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Tasks', $totalTasks)
                ->description($doneTasks . '/' . $totalTasks . ' done')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($doneTasks === $totalTasks ? 'success' : 'warning'),
            Stat::make('Goals', $totalGoals)
                ->description('Across ' . $totalProjects . ' projects')
                ->descriptionIcon('heroicon-m-flag')
                ->color('info'),
            Stat::make('Excel Formulas', $totalFormulas)
                ->description('Formula library')
                ->descriptionIcon('heroicon-m-table-cells')
                ->color('primary'),
        ];
    }
}
