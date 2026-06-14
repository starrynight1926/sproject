<?php

namespace App\Http\Controllers;

use App\Models\ExcelFormula;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExcelFormulaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ExcelFormula::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('syntax', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $formulas = $query->orderBy('category')->orderBy('name')->get();

        return response()->json($formulas);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'syntax' => 'required|string',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'note' => 'nullable|string',
            'example' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
        ]);

        $formula = ExcelFormula::create($validated);

        return response()->json($formula, 201);
    }

    public function show(ExcelFormula $excelFormula): JsonResponse
    {
        return response()->json($excelFormula);
    }

    public function update(Request $request, ExcelFormula $excelFormula): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'syntax' => 'sometimes|required|string',
            'category' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'note' => 'nullable|string',
            'example' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
        ]);

        $excelFormula->update($validated);

        return response()->json($excelFormula);
    }

    public function destroy(ExcelFormula $excelFormula): JsonResponse
    {
        $excelFormula->delete();

        return response()->json(null, 204);
    }

    public function categories(): JsonResponse
    {
        $categories = ExcelFormula::distinct()->pluck('category')->sort()->values();

        return response()->json($categories);
    }
}
