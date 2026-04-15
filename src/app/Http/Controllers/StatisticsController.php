<?php

namespace App\Http\Controllers;

use App\Models\BodyPart;
use App\Models\Exercise;
use App\Models\TrainingSet;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $exercises = Exercise::availableFor($user->id)
            ->with('bodyPart')
            ->orderBy('name')
            ->get();

        $bodyParts = BodyPart::orderBy('order')->get();

        // 部位別トレーニング頻度（今月）
        $bodyPartFrequency = $this->getBodyPartFrequency($user->id);

        // 今月の統計
        $monthStats = $this->getMonthStats($user->id);

        return view('statistics.index', compact(
            'exercises',
            'bodyParts',
            'bodyPartFrequency',
            'monthStats'
        ));
    }

    public function exerciseData(Request $request)
    {
        $exerciseId = $request->exercise_id;
        $period     = $request->period ?? 'month3'; // month1, month3, month6, year1

        $daysMap = ['month1' => 30, 'month3' => 90, 'month6' => 180, 'year1' => 365];
        $days    = $daysMap[$period] ?? 90;

        $data = WorkoutExercise::where('exercise_id', $exerciseId)
            ->whereHas('workout', function ($q) use ($days) {
                $q->where('user_id', Auth::id())
                    ->where('date', '>=', now()->subDays($days));
            })
            ->with(['sets', 'workout'])
            ->get()
            ->map(function ($we) {
                $maxWeight = $we->sets->max('weight') ?? 0;
                $maxReps   = $we->sets->where('weight', $maxWeight)->max('reps') ?? 0;
                $volume    = $we->sets->sum(fn ($s) => ($s->weight ?? 0) * ($s->reps ?? 0));

                // 1RM推定（Epley式）
                $estimated1rm = $maxReps > 0
                    ? round($maxWeight * (1 + $maxReps / 30), 1)
                    : $maxWeight;

                return [
                    'date'         => $we->workout->date->toDateString(),
                    'max_weight'   => $maxWeight,
                    'estimated1rm' => $estimated1rm,
                    'volume'       => $volume,
                ];
            })
            ->sortBy('date')
            ->values();

        return response()->json($data);
    }

    private function getBodyPartFrequency(int $userId): array
    {
        $bodyParts = BodyPart::orderBy('order')->get();
        $result    = [];

        foreach ($bodyParts as $bp) {
            $count = Workout::where('user_id', $userId)
                ->whereYear('date', now()->year)
                ->whereMonth('date', now()->month)
                ->whereHas('workoutExercises.exercise', function ($q) use ($bp) {
                    $q->where('body_part_id', $bp->id);
                })
                ->count();

            $result[] = [
                'name'  => $bp->name,
                'slug'  => $bp->slug,
                'icon'  => $bp->icon,
                'count' => $count,
            ];
        }

        return $result;
    }

    private function getMonthStats(int $userId): array
    {
        $workouts = Workout::where('user_id', $userId)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->with(['workoutExercises.sets'])
            ->get();

        $totalVolume = $workouts->sum(fn ($w) => $w->total_volume);

        return [
            'count'        => $workouts->count(),
            'total_volume' => $totalVolume,
        ];
    }
}
