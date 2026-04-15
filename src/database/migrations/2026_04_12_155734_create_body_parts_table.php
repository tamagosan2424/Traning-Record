<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('body_parts', function (Blueprint $table) {
            $table->id();
            $table->string('name');       // 例: 胸
            $table->string('slug');       // 例: chest
            $table->string('icon')->nullable(); // 絵文字アイコン
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('body_parts');
    }
};
