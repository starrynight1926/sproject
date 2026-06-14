<?php

namespace App\Filament\Pages;

use App\Models\Task;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Trash extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrash;

    protected static string|UnitEnum|null $navigationGroup = 'Báo cáo';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Thùng rác';

    protected static ?string $title = 'Thùng rác';

    protected static ?string $slug = 'trash';

    protected string $view = 'filament.pages.trash';

    public function getTrashedTasks(): array
    {
        return Task::onlyTrashed()
            ->with('project')
            ->orderBy('deleted_at', 'desc')
            ->get()
            ->map(fn ($task) => [
                'id' => $task->id,
                'title' => $task->title,
                'project_name' => $task->project?->name,
                'status' => $task->status,
                'deleted_at' => $task->deleted_at?->format('d/m/Y H:i'),
            ])
            ->toArray();
    }

    public function restoreTask(int $taskId): void
    {
        Task::onlyTrashed()->where('id', $taskId)->first()?->restore();
    }

    public function forceDeleteTask(int $taskId): void
    {
        Task::onlyTrashed()->where('id', $taskId)->first()?->forceDelete();
    }

    public function emptyTrash(): void
    {
        Task::onlyTrashed()->get()->each(fn ($task) => $task->forceDelete());
    }
}
