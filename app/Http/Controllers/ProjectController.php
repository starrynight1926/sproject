<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Project::with(['owner:id,name,email', 'members:id,name,email'])
            ->withCount(['tasks', 'goals']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($request->has('user_id')) {
            $userId = $request->input('user_id');
            $query->where(function ($q) use ($userId) {
                $q->where('owner_id', $userId)
                    ->orWhereHas('members', fn ($m) => $m->where('users.id', $userId));
            });
        }

        $projects = $query->orderByDesc('updated_at')->get();

        return response()->json($projects);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:active,done,archive',
            'deadline' => 'nullable|date',
            'owner_id' => 'required|exists:users,id',
        ]);

        $project = Project::create($validated);

        ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $project->owner_id,
            'role' => 'owner',
        ]);

        return response()->json($project->load('owner:id,name,email', 'members:id,name,email'), 201);
    }

    public function show(Project $project): JsonResponse
    {
        $project->load([
            'owner:id,name,email',
            'members:id,name,email',
            'goals',
            'tasks' => fn ($q) => $q->orderBy('sort_order'),
            'tasks.assignees:id,name,email',
        ]);
        $project->loadCount(['tasks', 'goals']);

        return response()->json($project);
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:active,done,archive',
            'deadline' => 'nullable|date',
        ]);

        $project->update($validated);

        return response()->json($project->load('owner:id,name,email', 'members:id,name,email'));
    }

    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return response()->json(null, 204);
    }

    public function addMember(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:owner,manager,member',
        ]);

        $project->members()->syncWithoutDetaching([
            $validated['user_id'] => ['role' => $validated['role']],
        ]);

        return response()->json($project->load('members:id,name,email'));
    }

    public function removeMember(Project $project, int $userId): JsonResponse
    {
        $project->members()->detach($userId);

        return response()->json($project->load('members:id,name,email'));
    }
}
