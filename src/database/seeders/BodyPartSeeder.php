<?php

namespace Database\Seeders;

use App\Models\BodyPart;
use Illuminate\Database\Seeder;

class BodyPartSeeder extends Seeder
{
    public function run(): void
    {
        $bodyParts = [
            ['name' => '胸',     'slug' => 'chest',    'icon' => '💪', 'order' => 1],
            ['name' => '背中',   'slug' => 'back',     'icon' => '🔙', 'order' => 2],
            ['name' => '肩',     'slug' => 'shoulder', 'icon' => '🏋️', 'order' => 3],
            ['name' => '腕',     'slug' => 'arms',     'icon' => '🦾', 'order' => 4],
            ['name' => '脚',     'slug' => 'legs',     'icon' => '🦵', 'order' => 5],
            ['name' => '腹',     'slug' => 'core',     'icon' => '🧘', 'order' => 6],
            ['name' => '有酸素', 'slug' => 'cardio',   'icon' => '🏃', 'order' => 7],
        ];

        foreach ($bodyParts as $part) {
            BodyPart::firstOrCreate(['slug' => $part['slug']], $part);
        }
    }
}
