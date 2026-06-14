<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request, Project $project): JsonResponse
    {
        $query = $project->tasks()
            ->with(['assignees:id,name,email', 'goal:id,name'])
            ->orderBy('sort_order');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        if ($assignee = $request->input('assignee_id')) {
            $query->whereHas('assignees', fn ($q) => $q->where('users.id', $assignee));
        }

        $tasks = $query->get();

        return response()->json($tasks);
    }

    public function store(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'goal_id' => 'nullable|exists:goals,id',
            'status' => 'sometimes|in:idea,draft,beta,edit,done',
            'priority' => 'sometimes|in:low,medium,high',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'sort_order' => 'sometimes|integer',
            'assignee_ids' => 'nullable|array',
            'assignee_ids.*' => 'exists:users,id',
        ]);

        $assigneeIds = $validated['assignee_ids'] ?? [];
        unset($validated['assignee_ids']);

        $task = $project->tasks()->create($validated);

        if ($assigneeIds) {
            $task->assignees()->attach(
                collect($assigneeIds)->mapWithKeys(fn ($id) => [$id => ['assigned_at' => now()]])->all()
            );
        }

        return response()->json($task->load('assignees:id,name,email'), 201);
    }

    public function show(Project $project, Task $task): JsonResponse
    {
        $task->load(['assignees:id,name,email', 'goal:id,name', 'media']);

        return response()->json($task);
    }

    public function update(Request $request, Project $project, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'goal_id' => 'nullable|exists:goals,id',
            'status' => 'sometimes|in:idea,draft,beta,edit,done',
            'priority' => 'sometimes|in:low,medium,high',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'sort_order' => 'sometimes|integer',
            'assignee_ids' => 'nullable|array',
            'assignee_ids.*' => 'exists:users,id',
        ]);

        $assigneeIds = $validated['assignee_ids'] ?? null;
        unset($validated['assignee_ids']);

        $task->update($validated);

        if ($assigneeIds !== null) {
            $task->assignees()->sync(
                collect($assigneeIds)->mapWithKeys(fn ($id) => [$id => ['assigned_at' => now()]])->all()
            );
        }

        return response()->json($task->load('assignees:id,name,email'));
    }

    public function destroy(Project $project, Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(null, 204);
    }

    public function reorder(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|exists:tasks,id',
            'tasks.*.sort_order' => 'required|integer',
            'tasks.*.status' => 'sometimes|in:idea,draft,beta,edit,done',
        ]);

        foreach ($validated['tasks'] as $taskData) {
            Task::where('id', $taskData['id'])
                ->where('project_id', $project->id)
                ->update(collect($taskData)->except('id')->all());
        }

        return response()->json(['message' => 'Reordered successfully']);
    }
}
