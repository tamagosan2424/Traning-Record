<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workout extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'date', 'memo', 'status'];

    protected $casts = [
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workoutExercises(): HasMany
    {
        return $this->hasMany(WorkoutExercise::class)->orderBy('order');
    }

    /**
     * 合計ボリューム（kg × reps）
     */
    public function getTotalVolumeAttribute(): float
    {
        return $this->workoutExercises->sum(function ($we) {
            return $we->sets->sum(function ($set) {
                return ($set->weight ?? 0) * ($set->reps ?? 0);
            });
        });
    }
}
