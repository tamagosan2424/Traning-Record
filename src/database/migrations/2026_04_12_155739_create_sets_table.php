<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_exercise_id')->constrained()->cascadeOnDelete();
            $table->integer('set_number');
            $table->decimal('weight', 6, 2)->nullable();       // kg（重量トレーニング）
            $table->integer('reps')->nullable();                // 回数
            $table->integer('duration_min')->nullable();        // 分（有酸素）
            $table->decimal('distance_km', 6, 3)->nullable();  // km（有酸素）
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sets');
    }
};
