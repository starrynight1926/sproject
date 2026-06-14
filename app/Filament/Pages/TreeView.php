<?php

namespace App\Filament\Pages;

use App\Models\Project;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class TreeView extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShare;

    protected static string|UnitEnum|null $navigationGroup = 'Báo cáo';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Sơ đồ cây';

    protected static ?string $title = 'Sơ đồ cây dự án';

    protected static ?string $slug = 'tree-view';

    protected string $view = 'filament.pages.tree-view';

    public ?int $filterProject = null;

    protected static array $metricLabels = [
        'target' => 'Mục tiêu',
        'cost' => 'Chi phí',
        'resource' => 'Nguồn lực',
        'time' => 'Thời gian',
    ];

    protected static array $statusLabels = [
        'idea' => 'Ý tưởng', 'draft' => 'Bản thảo', 'beta' => 'Beta',
        'edit' => 'Đang chỉnh sửa', 'done' => 'Hoàn thành',
        'pending' => 'Chờ', 'in_progress' => 'Đang làm', 'completed' => 'Hoàn thành', 'active' => 'Đang hoạt động',
    ];

    public function getProjects(): array
    {
        return Project::pluck('name', 'id')->toArray();
    }

    public function setFilterProject(?int $projectId): void
    {
        $this->filterProject = $projectId ?: null;
    }

    public function getTree(): array
    {
        $query = Project::with([
            'goals',
            'tasks.goals',
            'tasks.workItems.goals',
            'tasks.workItems.reports',
        ]);

        if ($this->filterProject) {
            $query->where('id', $this->filterProject);
        }

        return $query->get()->map(fn (Project $project) => $this->buildProjectNode($project))->toArray();
    }

    protected function buildProjectNode(Project $project): array
    {
        $children = $project->tasks->map(fn ($task) => $this->buildTaskNode($task))->toArray();

        $ownGoals = $project->goals->whereNull('task_id')->whereNull('phase_id')->whereNull('work_item_id');

        $badges = $this->buildGoalBars($ownGoals);

        if ($divergence = $this->buildDivergenceBadge($project)) {
            $badges[] = $divergence;
        }

        return [
            'label' => '📁 '.$project->name,
            'subtitle' => self::$statusLabels[$project->status] ?? $project->status,
            'badges' => $badges,
            'level' => 'root',
            'children' => $children,
        ];
    }

    protected function buildTaskNode($task): array
    {
        $workItemNodes = $task->workItems->map(fn ($workItem) => $this->buildWorkItemNode($workItem))->toArray();

        $ownGoals = $task->goals->whereNull('phase_id')->whereNull('work_item_id');

        return [
            'label' => '🏢 '.$task->title,
            'subtitle' => self::$statusLabels[$task->status] ?? $task->status,
            'badges' => $this->buildGoalBars($ownGoals),
            'level' => 'node',
            'children' => $workItemNodes,
        ];
    }

    /**
     * Mỗi goal "của riêng" node (phase_id/work_item_id null) hiển thị thành 1 thanh tiến độ
     * theo dạng giá trị hiện tại / mục tiêu, màu sắc theo loại chỉ số.
     */
    protected function buildGoalBars($goals): array
    {
        return $goals->map(function ($goal) {
            $percent = $goal->progressPercent();

            $color = match (true) {
                $goal->type === 'percent' => 'purple',
                $goal->metric_type === 'time' => 'green',
                $goal->metric_type === 'resource' => 'red',
                $goal->type === 'money' => 'amber',
                default => 'blue',
            };

            if ($goal->type === 'percent') {
                $text = $goal->name.': '.$this->formatValue((float) $goal->current_value, 'percent', null);
            } else {
                $current = $this->formatValue((float) $goal->current_value, $goal->type, $goal->unit);
                $target = $this->formatValue((float) $goal->target_value, $goal->type, $goal->unit);
                $text = "{$goal->name}: {$current} / {$target} ({$percent}%)";
            }

            return [
                'text' => $text,
                'percent' => $percent,
                'color' => $color,
            ];
        })->values()->toArray();
    }

    protected function buildWorkItemNode($workItem): array
    {
        $children = $workItem->goals->map(fn ($goal) => $this->buildGoalLeaf($goal))->toArray();

        $reportsCount = $workItem->reports->count();
        if ($reportsCount > 0) {
            $lastDate = $workItem->reports->first()?->report_date?->format('d/m/Y');
            $children[] = [
                'label' => '📋 Báo cáo',
                'subtitle' => "{$reportsCount} lần · gần nhất {$lastDate}",
                'badges' => [],
                'level' => 'leaf',
                'children' => [],
            ];
        }

        $progress = $workItem->progressPercent();

        return [
            'label' => '📌 '.$workItem->title,
            'subtitle' => self::$statusLabels[$workItem->status] ?? $workItem->status,
            'badges' => [[
                'text' => "Tiến độ: {$progress}%",
                'percent' => $progress,
                'color' => $progress >= 100 ? 'green' : ($progress >= 50 ? 'amber' : 'red'),
            ]],
            'level' => 'node',
            'children' => $children,
        ];
    }

    protected function buildGoalLeaf($goal): array
    {
        $target = (float) $goal->target_value;
        $current = (float) $goal->current_value;
        $percent = $target > 0 ? round(($current / $target) * 100, 1) : 0;
        $metricLabel = self::$metricLabels[$goal->metric_type] ?? $goal->metric_type;

        return [
            'label' => $metricLabel.': '.$goal->name,
            'subtitle' => $this->formatValue($current, $goal->type, $goal->unit).' / '.$this->formatValue($target, $goal->type, $goal->unit),
            'badges' => [[
                'text' => $percent.'%'.($percent > 100 ? ' ⚠ vượt mức' : ''),
                'percent' => min(100, $percent),
                'color' => $percent > 100 ? 'red' : ($percent >= 100 ? 'green' : ($percent >= 50 ? 'amber' : 'blue')),
            ]],
            'level' => 'leaf',
            'children' => [],
        ];
    }

    /**
     * Cảnh báo "lệch pha" giữa % Doanh thu và % Tiến độ công việc (trung bình có trọng số theo Nguồn lực).
     */
    protected function buildDivergenceBadge(Project $project): ?array
    {
        $revenueGoal = $project->goals->first(fn ($g) => $g->metric_type === 'target' && $g->type === 'money' && $g->task_id === null);

        if (! $revenueGoal) {
            return null;
        }

        $revenuePercent = $revenueGoal->progressPercent();

        $weightedSum = 0;
        $weightTotal = 0;

        foreach ($project->tasks as $task) {
            $targetGoal = $task->goals->first(fn ($g) => $g->metric_type === 'target' && $g->phase_id === null && $g->work_item_id === null);
            $resourceGoal = $task->goals->first(fn ($g) => $g->metric_type === 'resource' && $g->phase_id === null && $g->work_item_id === null);

            if (! $targetGoal || ! $resourceGoal) {
                continue;
            }

            $weight = (float) $resourceGoal->target_value;
            $weightedSum += $targetGoal->progressPercent() * $weight;
            $weightTotal += $weight;
        }

        if ($weightTotal <= 0) {
            return null;
        }

        $workPercent = round($weightedSum / $weightTotal, 1);
        $diff = round($workPercent - $revenuePercent, 1);

        if (abs($diff) < 15) {
            return ['text' => "Tiến độ công việc {$workPercent}% ~ Doanh thu {$revenuePercent}% (đồng pha)", 'color' => 'green'];
        }

        if ($diff > 0) {
            return ['text' => "⚠ Tiến độ công việc {$workPercent}% cao hơn Doanh thu {$revenuePercent}% (hoàn thành việc nhưng chưa hiệu quả)", 'color' => 'amber'];
        }

        return ['text' => "⚠ Doanh thu {$revenuePercent}% cao hơn Tiến độ công việc {$workPercent}% (đạt nhờ yếu tố ngoài kế hoạch)", 'color' => 'amber'];
    }

    protected function formatValue(float $value, string $type, ?string $unit): string
    {
        $formatted = $this->formatNumber($value);

        if ($type === 'percent') {
            return $formatted.'%';
        }

        return $unit ? $formatted.' '.$unit : $formatted;
    }

    protected function formatNumber(float $value): string
    {
        return floor($value) == $value
            ? number_format($value, 0)
            : number_format($value, 2);
    }
}
