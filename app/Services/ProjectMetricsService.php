<?php

namespace App\Services;

use App\Models\Project;
use App\Models\WorkItem;
use App\Models\WorkItemReport;
use Carbon\Carbon;

class ProjectMetricsService
{
    public function summary(Project $project): array
    {
        $revenueGoal = $project->goals()
            ->where('metric_type', 'target')
            ->where('type', 'money')
            ->whereNull('phase_id')
            ->whereNull('task_id')
            ->whereNull('work_item_id')
            ->first();

        $tasks = $project->tasks()->with(['workItems', 'assignees'])->get();
        $workItems = $tasks->flatMap(fn ($task) => $task->workItems);

        $totalWork = $workItems->count();
        $doneWork = $workItems->where('status', 'done')->count();

        $withDue = $workItems->filter(fn ($w) => $w->due_date !== null);
        $onTrack = $withDue->filter(fn ($w) => $w->status === 'done' || ! $w->due_date->isPast());

        $staff = $this->staffKpi($tasks);

        return [
            'revenue_current' => (float) ($revenueGoal->current_value ?? 0),
            'revenue_target' => (float) ($revenueGoal->target_value ?? 0),
            'revenue_percent' => $revenueGoal ? $revenueGoal->progressPercent() : 0,
            'tasks_done' => $doneWork,
            'tasks_total' => $totalWork,
            'time_on_track' => $onTrack->count(),
            'time_total' => $withDue->count(),
            'staff_kpi_ok' => $staff['ok'],
            'staff_total' => $staff['total'],
        ];
    }

    protected function staffKpi($tasks): array
    {
        $users = $tasks->flatMap(fn ($task) => $task->assignees)->unique('id');

        $ok = 0;
        foreach ($users as $user) {
            $assignedTasks = $tasks->filter(fn ($task) => $task->assignees->contains('id', $user->id));
            $workItems = $assignedTasks->flatMap(fn ($task) => $task->workItems);

            if ($workItems->isEmpty()) {
                continue;
            }

            $kpi = $workItems->where('status', 'done')->count() / $workItems->count() * 100;

            if ($kpi >= 80) {
                $ok++;
            }
        }

        return ['ok' => $ok, 'total' => $users->count()];
    }

    /**
     * Compare target vs current of a metric (cost/target/resource/time) for each "department" (Task) in a project.
     */
    public function metricByTask(Project $project, string $metricType): array
    {
        $tasks = $project->tasks()->with(['goals', 'workItems.goals'])->get();

        $labels = [];
        $current = [];
        $target = [];

        foreach ($tasks as $task) {
            $goal = $task->goals->firstWhere('metric_type', $metricType);

            if ($goal) {
                $cur = (float) $goal->current_value;
                $tgt = (float) $goal->target_value;
            } else {
                $workItemGoals = $task->workItems->flatMap(fn ($w) => $w->goals)->where('metric_type', $metricType);
                $cur = (float) $workItemGoals->sum('current_value');
                $tgt = (float) $workItemGoals->sum('target_value');
            }

            if ($tgt <= 0 && $cur <= 0) {
                continue;
            }

            $labels[] = $task->title;
            $current[] = $cur;
            $target[] = $tgt;
        }

        return compact('labels', 'current', 'target');
    }

    public function progressTimeline(Project $project): array
    {
        $workItemIds = WorkItem::whereIn('task_id', $project->tasks()->pluck('id'))->pluck('id');

        $reports = WorkItemReport::whereIn('work_item_id', $workItemIds)
            ->selectRaw('report_date, SUM(progress_value) as total')
            ->groupBy('report_date')
            ->orderBy('report_date')
            ->get();

        return [
            'labels' => $reports->map(fn ($r) => Carbon::parse($r->report_date)->format('d/m'))->toArray(),
            'data' => $reports->map(fn ($r) => (float) $r->total)->toArray(),
        ];
    }
}
