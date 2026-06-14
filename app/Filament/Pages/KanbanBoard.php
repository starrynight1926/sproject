<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class KanbanBoard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedViewColumns;

    protected static string|UnitEnum|null $navigationGroup = 'Công việc';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Kanban Board';

    protected static ?string $title = 'Kanban Board';

    protected static ?string $slug = 'kanban';

    protected string $view = 'filament.pages.kanban-board';

    public array $columns = [];

    public ?int $filterProject = null;

    public ?string $filterPriority = null;

    public function mount(): void
    {
        $this->columns = [
            ['id' => 'idea', 'title' => 'Idea', 'color' => '#6b7280'],
            ['id' => 'draft', 'title' => 'Draft', 'color' => '#f59e0b'],
            ['id' => 'beta', 'title' => 'In Progress', 'color' => '#3b82f6'],
            ['id' => 'edit', 'title' => 'Review', 'color' => '#8b5cf6'],
            ['id' => 'done', 'title' => 'Done', 'color' => '#10b981'],
        ];
    }

    public function getTasksByColumn(): array
    {
        $query = Task::with(['project', 'assignees']);

        if ($this->filterProject) {
            $query->where('project_id', $this->filterProject);
        }
        if ($this->filterPriority) {
            $query->where('priority', $this->filterPriority);
        }

        $tasks = $query->orderBy('sort_order')->orderBy('updated_at', 'desc')->get();

        $grouped = [];
        foreach ($this->columns as $col) {
            $grouped[$col['id']] = $tasks->where('status', $col['id'])->values()->map(function ($task) {
                $doneChecks = 0;
                $totalChecks = 1;
                if ($task->status === 'done') {
                    $doneChecks = 1;
                }

                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'project_name' => $task->project?->name,
                    'priority' => $task->priority,
                    'status' => $task->status,
                    'due_date' => $task->due_date?->format('d/m'),
                    'due_date_raw' => $task->due_date?->format('Y-m-d'),
                    'is_overdue' => $task->due_date && $task->due_date->isPast() && $task->status !== 'done',
                    'assignees' => $task->assignees->map(fn ($u) => [
                        'id' => $u->id,
                        'name' => $u->name,
                        'initial' => strtoupper(substr($u->name, 0, 1)),
                    ])->toArray(),
                    'checklist' => [$doneChecks, $totalChecks],
                ];
            })->toArray();
        }

        return $grouped;
    }

    public function getProjects(): array
    {
        return Project::pluck('name', 'id')->toArray();
    }

    public function getUsers(): array
    {
        return User::pluck('name', 'id')->toArray();
    }

    public function moveTask(int $taskId, string $toStatus): void
    {
        $task = Task::findOrFail($taskId);
        $task->update(['status' => $toStatus]);
    }

    public function deleteTask(int $taskId): void
    {
        Task::findOrFail($taskId)->delete();
    }

    public function getTrashCount(): int
    {
        return Task::onlyTrashed()->count();
    }

    public function addColumn(string $title): void
    {
        $id = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $title));
        $colors = ['#f87171', '#a78bfa', '#f59e0b', '#60a5fa', '#22d3a0', '#ec4899'];
        $this->columns[] = [
            'id' => $id,
            'title' => $title,
            'color' => $colors[count($this->columns) % count($colors)],
        ];
    }

    public function renameColumn(string $colId, string $newTitle): void
    {
        foreach ($this->columns as &$col) {
            if ($col['id'] === $colId) {
                $col['title'] = $newTitle;
                break;
            }
        }
    }

    public function createTask(string $title, string $colId, ?int $projectId = null, string $priority = 'medium', ?int $assigneeId = null): void
    {
        $task = Task::create([
            'title' => $title,
            'status' => $colId,
            'priority' => $priority,
            'project_id' => $projectId,
            'sort_order' => Task::where('status', $colId)->count(),
        ]);

        if ($assigneeId) {
            $task->assignees()->attach($assigneeId, ['assigned_at' => now()]);
        }
    }

    public function setFilterProject(?int $projectId): void
    {
        $this->filterProject = $projectId ?: null;
    }

    public function setFilterPriority(?string $priority): void
    {
        $this->filterPriority = $priority ?: null;
    }
}
