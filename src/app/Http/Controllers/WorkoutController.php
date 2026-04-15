<?php

namespace App\Http\Controllers;

use App\Models\BodyPart;
use App\Models\Exercise;
use App\Models\TrainingSet;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WorkoutController extends Controller
{
    public function index(Request $request): View
    {
        $query = Workout::where('user_id', Auth::id())
            ->with(['workoutExercises.exercise.bodyPart', 'workoutExercises.sets'])
            ->orderByDesc('date');

        // 日付フィルタ
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // 部位フィルタ
        if ($request->filled('body_part')) {
            $query->whereHas('workoutExercises.exercise', function ($q) use ($request) {
                $q->whereHas('bodyPart', function ($q2) use ($request) {
                    $q2->where('slug', $request->body_part);
                });
            });
        }

        $workouts  = $query->paginate(10)->withQueryString();
        $bodyParts = BodyPart::orderBy('order')->get();

        return view('workouts.index', compact('workouts', 'bodyParts'));
    }

    public function create(): View
    {
        $exercises = Exercise::availableFor(Auth::id())
            ->with('bodyPart')
            ->orderBy('name')
            ->get()
            ->groupBy('bodyPart.name');

        $bodyParts       = BodyPart::orderBy('order')->get();
        $favoriteIds     = Auth::user()->favoriteExercises->pluck('id')->toArray();

        return view('workouts.create', compact('exercises', 'bodyParts', 'favoriteIds'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date'                              => 'required|date',
            'memo'                              => 'nullable|string|max:500',
            'exercises'                         => 'required|array|min:1',
            'exercises.*.exercise_id'           => 'required|exists:exercises,id',
            'exercises.*.sets'                  => 'required|array|min:1',
            'exercises.*.sets.*.weight'         => 'nullable|numeric|min:0',
            'exercises.*.sets.*.reps'           => 'nullable|integer|min:0',
            'exercises.*.sets.*.duration_min'   => 'nullable|integer|min:0',
            'exercises.*.sets.*.distance_km'    => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $workout = Workout::create([
                'user_id' => Auth::id(),
                'date'    => $validated['date'],
                'memo'    => $validated['memo'] ?? null,
                'status'  => 'completed',
            ]);

            foreach ($validated['exercises'] as $order => $exerciseData) {
                $we = WorkoutExercise::create([
                    'workout_id'  => $workout->id,
                    'exercise_id' => $exerciseData['exercise_id'],
                    'order'       => $order,
                ]);

                foreach ($exerciseData['sets'] as $setNum => $setData) {
                    TrainingSet::create([
                        'workout_exercise_id' => $we->id,
                        'set_number'          => $setNum + 1,
                        'weight'              => $setData['weight'] ?? null,
                        'reps'                => $setData['reps'] ?? null,
                        'duration_min'        => $setData['duration_min'] ?? null,
                        'distance_km'         => $setData['distance_km'] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('workouts.index')
            ->with('success', 'ワークアウトを記録しました！');
    }

    public function show(Workout $workout): View
    {
        $this->authorize('view', $workout);

        $workout->load(['workoutExercises.exercise.bodyPart', 'workoutExercises.sets']);

        return view('workouts.show', compact('workout'));
    }

    public function edit(Workout $workout): View
    {
        $this->authorize('update', $workout);

        $workout->load(['workoutExercises.exercise', 'workoutExercises.sets']);

        $exercises = Exercise::availableFor(Auth::id())
            ->with('bodyPart')
            ->orderBy('name')
            ->get()
            ->groupBy('bodyPart.name');

        $bodyParts = BodyPart::orderBy('order')->get();

        return view('workouts.edit', compact('workout', 'exercises', 'bodyParts'));
    }

    public function update(Request $request, Workout $workout): RedirectResponse
    {
        $this->authorize('update', $workout);

        $validated = $request->validate([
            'date'                              => 'required|date',
            'memo'                              => 'nullable|string|max:500',
            'exercises'                         => 'required|array|min:1',
            'exercises.*.exercise_id'           => 'required|exists:exercises,id',
            'exercises.*.sets'                  => 'required|array|min:1',
            'exercises.*.sets.*.weight'         => 'nullable|numeric|min:0',
            'exercises.*.sets.*.reps'           => 'nullable|integer|min:0',
            'exercises.*.sets.*.duration_min'   => 'nullable|integer|min:0',
            'exercises.*.sets.*.distance_km'    => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $workout) {
            $workout->update([
                'date' => $validated['date'],
                'memo' => $validated['memo'] ?? null,
            ]);

            // 既存の種目・セットを削除して再登録
            $workout->workoutExercises()->each(function ($we) {
                $we->sets()->delete();
                $we->delete();
            });

            foreach ($validated['exercises'] as $order => $exerciseData) {
                $we = WorkoutExercise::create([
                    'workout_id'  => $workout->id,
                    'exercise_id' => $exerciseData['exercise_id'],
                    'order'       => $order,
                ]);

                foreach ($exerciseData['sets'] as $setNum => $setData) {
                    TrainingSet::create([
                        'workout_exercise_id' => $we->id,
                        'set_number'          => $setNum + 1,
                        'weight'              => $setData['weight'] ?? null,
                        'reps'                => $setData['reps'] ?? null,
                        'duration_min'        => $setData['duration_min'] ?? null,
                        'distance_km'         => $setData['distance_km'] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('workouts.show', $workout)
            ->with('success', 'ワークアウトを更新しました！');
    }

    public function destroy(Workout $workout): RedirectResponse
    {
        $this->authorize('delete', $workout);

        $workout->delete();

        return redirect()->route('workouts.index')
            ->with('success', 'ワークアウトを削除しました。');
    }

    /**
     * 特定種目の前回記録を返す（Ajax用）
     */
    public function previousSets(Request $request)
    {
        $exerciseId = $request->integer('exercise_id');

        $prevWorkoutExercise = WorkoutExercise::where('exercise_id', $exerciseId)
            ->whereHas('workout', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->with('sets')
            ->orderByDesc('id')
            ->first();

        if (! $prevWorkoutExercise) {
            return response()->json(['sets' => []]);
        }

        return response()->json([
            'sets' => $prevWorkoutExercise->sets->map(fn ($s) => [
                'set_number'   => $s->set_number,
                'weight'       => $s->weight,
                'reps'         => $s->reps,
                'duration_min' => $s->duration_min,
                'distance_km'  => $s->distance_km,
            ]),
        ]);
    }
}
