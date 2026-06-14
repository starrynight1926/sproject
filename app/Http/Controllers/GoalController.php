<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index(Project $project): JsonResponse
    {
        $goals = $project->goals()
            ->withCount('tasks')
            ->orderBy('created_at')
            ->get()
            ->map(function ($goal) {
                $goal->progress_percent = $goal->progressPercent();
                return $goal;
            });

        return response()->json($goals);
    }

    public function store(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:money,quantity,percent',
            'target_value' => 'required|numeric|min:0',
            'current_value' => 'sometimes|numeric|min:0',
        ]);

        $goal = $project->goals()->create($validated);

        return response()->json($goal, 201);
    }

    public function show(Project $project, Goal $goal): JsonResponse
    {
        $goal->loadCount('tasks');
        $goal->progress_percent = $goal->progressPercent();

        return response()->json($goal);
    }

    public function update(Request $request, Project $project, Goal $goal): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|in:money,quantity,percent',
            'target_value' => 'sometimes|numeric|min:0',
            'current_value' => 'sometimes|numeric|min:0',
        ]);

        $goal->update($validated);
        $goal->progress_percent = $goal->progressPercent();

        return response()->json($goal);
    }

    public function destroy(Project $project, Goal $goal): JsonResponse
    {
        $goal->delete();

        return response()->json(null, 204);
    }
}
