<?php

namespace App\Http\Controllers;

use App\Models\BodyPart;
use App\Models\Exercise;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExerciseController extends Controller
{
    public function index(): View
    {
        $bodyParts = BodyPart::with(['exercises' => function ($q) {
            $q->availableFor(Auth::id())->orderBy('name');
        }])->orderBy('order')->get();

        $favoriteIds = Auth::user()->favoriteExercises->pluck('id')->toArray();

        return view('exercises.index', compact('bodyParts', 'favoriteIds'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'body_part_id' => 'required|exists:body_parts,id',
            'type'         => 'required|in:weight,bodyweight,cardio',
        ]);

        Exercise::create([
            'user_id'      => Auth::id(),
            'name'         => $validated['name'],
            'body_part_id' => $validated['body_part_id'],
            'type'         => $validated['type'],
            'is_default'   => false,
        ]);

        return back()->with('success', '種目を追加しました。');
    }

    public function update(Request $request, Exercise $exercise): RedirectResponse
    {
        abort_if($exercise->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'body_part_id' => 'required|exists:body_parts,id',
            'type'         => 'required|in:weight,bodyweight,cardio',
        ]);

        $exercise->update($validated);

        return back()->with('success', '種目を更新しました。');
    }

    public function destroy(Exercise $exercise): RedirectResponse
    {
        abort_if($exercise->user_id !== Auth::id(), 403);

        $exercise->delete();

        return back()->with('success', '種目を削除しました。');
    }

    public function toggleFavorite(Exercise $exercise): RedirectResponse
    {
        $user = Auth::user();
        $user->favoriteExercises()->toggle($exercise->id);

        return back();
    }
}
