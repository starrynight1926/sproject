<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Models\Task;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class GanttChart extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Công việc';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Gantt Chart';

    protected static ?string $title = 'Gantt Chart';

    protected static ?string $slug = 'gantt';

    protected string $view = 'filament.pages.gantt-chart';

    public ?int $editingModuleIndex = null;

    public array $modules = [];

    public function mount(): void
    {
        $this->loadModules();
    }

    protected function loadModules(): void
    {
        $tasks = Task::with('project')
            ->whereNotNull('start_date')
            ->whereNotNull('due_date')
            ->orderBy('start_date')
            ->get();

        $grouped = $tasks->groupBy('project_id');

        $this->modules = [];
        foreach ($grouped as $projectId => $projectTasks) {
            $project = $projectTasks->first()->project;
            $minStart = $projectTasks->min('start_date');
            $maxEnd = $projectTasks->max('due_date');

            $startMonth = Carbon::parse($minStart)->month - 1;
            $endMonth = Carbon::parse($maxEnd)->month;

            $colors = ['#22d3a0', '#f59e0b', '#60a5fa', '#a78bfa', '#f87171', '#34d399', '#94a3b8', '#ec4899'];
            $textColors = ['#0a2e24', '#3d2800', '#0c2a4a', '#2a1e4a', '#4a1515', '#042e1e', '#1a1e28', '#4a1528'];
            $colorIndex = count($this->modules) % count($colors);

            $subtasks = $projectTasks->map(function ($task) {
                $statusWeights = ['idea' => 0, 'draft' => 25, 'beta' => 50, 'edit' => 75, 'done' => 100];

                return [
                    'id' => $task->id,
                    'name' => $task->title,
                    'note' => $task->description ?? '',
                    'weight' => 20,
                    'done' => $task->status === 'done',
                    'status' => $task->status,
                ];
            })->values()->toArray();

            $totalWeight = array_sum(array_column($subtasks, 'weight'));
            if ($totalWeight > 0) {
                $evenWeight = intval(100 / count($subtasks));
                foreach ($subtasks as &$sub) {
                    $sub['weight'] = $evenWeight;
                }
                unset($sub);
            }

            $this->modules[] = [
                'project_id' => $projectId,
                'name' => $project?->name ?? 'Không có dự án',
                'sub' => $project?->description ?? '',
                'start' => $startMonth,
                'end' => min($endMonth, 12),
                'color' => $colors[$colorIndex],
                'textColor' => $textColors[$colorIndex],
                'subtasks' => $subtasks,
            ];
        }
    }

    public function addModule(string $name, string $description = ''): void
    {
        if (blank($name)) {
            return;
        }

        $project = Project::create([
            'owner_id' => auth()->id(),
            'name' => $name,
            'description' => $description,
            'status' => 'active',
        ]);

        Task::create([
            'title' => 'Lập kế hoạch: '.$name,
            'project_id' => $project->id,
            'status' => 'idea',
            'priority' => 'medium',
            'start_date' => now(),
            'due_date' => now()->addMonth(),
            'sort_order' => 0,
        ]);

        $this->loadModules();
    }

    public function openModule(int $index): void
    {
        $this->editingModuleIndex = $index;
    }

    public function closeModule(): void
    {
        $this->editingModuleIndex = null;
    }

    public function toggleSubtask(int $moduleIndex, int $subtaskIndex): void
    {
        if (! isset($this->modules[$moduleIndex]['subtasks'][$subtaskIndex])) {
            return;
        }

        $sub = &$this->modules[$moduleIndex]['subtasks'][$subtaskIndex];
        $sub['done'] = ! $sub['done'];

        $task = Task::find($sub['id']);
        if ($task) {
            $task->update(['status' => $sub['done'] ? 'done' : 'beta']);
            $sub['status'] = $sub['done'] ? 'done' : 'beta';
        }
    }

    public function updateSubtaskWeight(int $moduleIndex, int $subtaskIndex, int $weight): void
    {
        if (isset($this->modules[$moduleIndex]['subtasks'][$subtaskIndex])) {
            $this->modules[$moduleIndex]['subtasks'][$subtaskIndex]['weight'] = $weight;
        }
    }

    public function updateSubtaskName(int $moduleIndex, int $subtaskIndex, string $name): void
    {
        if (! isset($this->modules[$moduleIndex]['subtasks'][$subtaskIndex])) {
            return;
        }

        $this->modules[$moduleIndex]['subtasks'][$subtaskIndex]['name'] = $name;
        $task = Task::find($this->modules[$moduleIndex]['subtasks'][$subtaskIndex]['id']);
        if ($task) {
            $task->update(['title' => $name]);
        }
    }

    public function updateSubtaskNote(int $moduleIndex, int $subtaskIndex, string $note): void
    {
        if (! isset($this->modules[$moduleIndex]['subtasks'][$subtaskIndex])) {
            return;
        }

        $this->modules[$moduleIndex]['subtasks'][$subtaskIndex]['note'] = $note;
        $task = Task::find($this->modules[$moduleIndex]['subtasks'][$subtaskIndex]['id']);
        if ($task) {
            $task->update(['description' => $note]);
        }
    }

    public function deleteSubtask(int $moduleIndex, int $subtaskIndex): void
    {
        if (! isset($this->modules[$moduleIndex]['subtasks'][$subtaskIndex])) {
            return;
        }

        array_splice($this->modules[$moduleIndex]['subtasks'], $subtaskIndex, 1);
    }

    public function addSubtask(int $moduleIndex, string $name): void
    {
        if (! isset($this->modules[$moduleIndex]) || blank($name)) {
            return;
        }

        $module = $this->modules[$moduleIndex];
        $task = Task::create([
            'title' => $name,
            'project_id' => $module['project_id'],
            'status' => 'idea',
            'priority' => 'medium',
            'start_date' => now(),
            'due_date' => now()->addWeek(),
            'sort_order' => count($module['subtasks']),
        ]);

        $this->modules[$moduleIndex]['subtasks'][] = [
            'id' => $task->id,
            'name' => $name,
            'note' => '',
            'weight' => 15,
            'done' => false,
            'status' => 'idea',
        ];
    }

    public function calcPercent(int $moduleIndex): int
    {
        $subtasks = $this->modules[$moduleIndex]['subtasks'] ?? [];
        if (empty($subtasks)) {
            return 0;
        }

        $totalWeight = array_sum(array_column($subtasks, 'weight'));
        if ($totalWeight === 0) {
            return 0;
        }

        $doneWeight = 0;
        foreach ($subtasks as $sub) {
            if ($sub['done']) {
                $doneWeight += $sub['weight'];
            }
        }

        return intval(round(($doneWeight / $totalWeight) * 100));
    }
}
