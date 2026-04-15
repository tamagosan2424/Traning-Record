<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // nullならシステム標準
            $table->foreignId('body_part_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['weight', 'bodyweight', 'cardio'])->default('weight');
            $table->boolean('is_default')->default(false); // システム標準種目
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
