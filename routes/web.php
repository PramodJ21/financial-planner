<?php

use App\Http\Controllers\GoalPlannerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});
Route::get('/goal-planner', [GoalPlannerController::class, 'index'])->name('goalplanner.index');
Route::post('/goal-planner/calculate', [GoalPlannerController::class, 'calculate'])->name('goalplanner.calculate');
Route::get('/goal-planner/result', [GoalPlannerController::class, 'result'])->name('goalplanner.result');
