<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\WorkoutController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {

    // ダッシュボード
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ワークアウト（previous-setsは {workout} と競合するため先に定義）
    Route::get('/workouts/previous-sets', [WorkoutController::class, 'previousSets'])
        ->name('workouts.previous-sets');
    Route::resource('workouts', WorkoutController::class);

    // 種目マスタ
    Route::get('/exercises', [ExerciseController::class, 'index'])->name('exercises.index');
    Route::post('/exercises', [ExerciseController::class, 'store'])->name('exercises.store');
    Route::put('/exercises/{exercise}', [ExerciseController::class, 'update'])->name('exercises.update');
    Route::delete('/exercises/{exercise}', [ExerciseController::class, 'destroy'])->name('exercises.destroy');
    Route::post('/exercises/{exercise}/favorite', [ExerciseController::class, 'toggleFavorite'])
        ->name('exercises.favorite');

    // 統計
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
    Route::get('/statistics/exercise-data', [StatisticsController::class, 'exerciseData'])
        ->name('statistics.exercise-data');

    // プロフィール（Breeze標準）
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
