<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingSet extends Model
{
    protected $table = 'sets';

    protected $fillable = [
        'workout_exercise_id',
        'set_number',
        'weight',
        'reps',
        'duration_min',
        'distance_km',
    ];

    protected $casts = [
        'weight'      => 'float',
        'distance_km' => 'float',
    ];

    public function workoutExercise(): BelongsTo
    {
        return $this->belongsTo(WorkoutExercise::class);
    }

    /**
     * このセットのボリューム（kg × reps）
     */
    public function getVolumeAttribute(): float
    {
        return ($this->weight ?? 0) * ($this->reps ?? 0);
    }
}
