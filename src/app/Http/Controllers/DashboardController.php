<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 直近5件のワークアウト
        $recentWorkouts = Workout::where('user_id', $user->id)
            ->with(['workoutExercises.exercise.bodyPart', 'workoutExercises.sets'])
            ->orderByDesc('date')
            ->take(5)
            ->get();

        // 今週の統計
        $weekStart = now()->startOfWeek();
        $weekEnd   = now()->endOfWeek();

        $weekWorkouts = Workout::where('user_id', $user->id)
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->with(['workoutExercises.sets'])
            ->get();

        $weekCount  = $weekWorkouts->count();
        $weekVolume = $weekWorkouts->sum(fn ($w) => $w->total_volume);

        // 今月の統計
        $monthCount = Workout::where('user_id', $user->id)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->count();

        // 連続記録（ストリーク）
        $streak = $this->calculateStreak($user->id);

        $stats = [
            'week_count'   => $weekCount,
            'week_volume'  => $weekVolume,
            'month_count'  => $monthCount,
            'streak'       => $streak,
        ];

        return view('dashboard', compact('recentWorkouts', 'stats'));
    }

    private function calculateStreak(int $userId): int
    {
        $dates = Workout::where('user_id', $userId)
            ->orderByDesc('date')
            ->pluck('date')
            ->map(fn ($d) => $d->toDateString())
            ->unique()
            ->values();

        if ($dates->isEmpty()) {
            return 0;
        }

        $streak  = 0;
        $current = now()->toDateString();

        foreach ($dates as $date) {
            if ($date === $current) {
                $streak++;
                $current = now()->subDays($streak)->toDateString();
            } else {
                break;
            }
        }

        return $streak;
    }
}
