<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExcelFormulaController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public: Excel Formulas (read-only)
Route::get('/excel-formulas', [ExcelFormulaController::class, 'index']);
Route::get('/excel-formulas/categories', [ExcelFormulaController::class, 'categories']);
Route::get('/excel-formulas/{excelFormula}', [ExcelFormulaController::class, 'show']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Excel Formulas (write)
    Route::post('/excel-formulas', [ExcelFormulaController::class, 'store']);
    Route::put('/excel-formulas/{excelFormula}', [ExcelFormulaController::class, 'update']);
    Route::delete('/excel-formulas/{excelFormula}', [ExcelFormulaController::class, 'destroy']);

    // Projects
    Route::apiResource('projects', ProjectController::class);
    Route::post('/projects/{project}/members', [ProjectController::class, 'addMember']);
    Route::delete('/projects/{project}/members/{userId}', [ProjectController::class, 'removeMember']);

    // Goals (nested under projects)
    Route::apiResource('projects.goals', GoalController::class);

    // Tasks (nested under projects)
    Route::apiResource('projects.tasks', TaskController::class);
    Route::post('/projects/{project}/tasks/reorder', [TaskController::class, 'reorder']);
});
