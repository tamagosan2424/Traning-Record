<?php

namespace Database\Seeders;

use App\Models\BodyPart;
use App\Models\Exercise;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $exercises = [
            // 胸 (chest)
            ['body_part' => 'chest', 'name' => 'ベンチプレス',         'type' => 'weight'],
            ['body_part' => 'chest', 'name' => 'インクラインベンチプレス', 'type' => 'weight'],
            ['body_part' => 'chest', 'name' => 'ダンベルフライ',       'type' => 'weight'],
            ['body_part' => 'chest', 'name' => 'ダンベルプレス',       'type' => 'weight'],
            ['body_part' => 'chest', 'name' => 'チェストプレス（マシン）', 'type' => 'weight'],
            ['body_part' => 'chest', 'name' => 'プッシュアップ',       'type' => 'bodyweight'],

            // 背中 (back)
            ['body_part' => 'back', 'name' => 'デッドリフト',          'type' => 'weight'],
            ['body_part' => 'back', 'name' => 'バーベルロウ',          'type' => 'weight'],
            ['body_part' => 'back', 'name' => 'ラットプルダウン',      'type' => 'weight'],
            ['body_part' => 'back', 'name' => 'シーテッドロウ',        'type' => 'weight'],
            ['body_part' => 'back', 'name' => 'チンニング（懸垂）',    'type' => 'bodyweight'],
            ['body_part' => 'back', 'name' => 'ダンベルロウ',          'type' => 'weight'],

            // 肩 (shoulder)
            ['body_part' => 'shoulder', 'name' => 'ショルダープレス',    'type' => 'weight'],
            ['body_part' => 'shoulder', 'name' => 'サイドレイズ',        'type' => 'weight'],
            ['body_part' => 'shoulder', 'name' => 'フロントレイズ',      'type' => 'weight'],
            ['body_part' => 'shoulder', 'name' => 'リアレイズ',          'type' => 'weight'],
            ['body_part' => 'shoulder', 'name' => 'アーノルドプレス',    'type' => 'weight'],

            // 腕 (arms)
            ['body_part' => 'arms', 'name' => 'バーベルカール',         'type' => 'weight'],
            ['body_part' => 'arms', 'name' => 'ダンベルカール',         'type' => 'weight'],
            ['body_part' => 'arms', 'name' => 'トライセプスプレスダウン', 'type' => 'weight'],
            ['body_part' => 'arms', 'name' => 'スカルクラッシャー',     'type' => 'weight'],
            ['body_part' => 'arms', 'name' => 'ハンマーカール',         'type' => 'weight'],
            ['body_part' => 'arms', 'name' => 'ディップス',             'type' => 'bodyweight'],

            // 脚 (legs)
            ['body_part' => 'legs', 'name' => 'スクワット',             'type' => 'weight'],
            ['body_part' => 'legs', 'name' => 'レッグプレス',           'type' => 'weight'],
            ['body_part' => 'legs', 'name' => 'レッグカール',           'type' => 'weight'],
            ['body_part' => 'legs', 'name' => 'レッグエクステンション', 'type' => 'weight'],
            ['body_part' => 'legs', 'name' => 'ルーマニアンデッドリフト', 'type' => 'weight'],
            ['body_part' => 'legs', 'name' => 'ランジ',                 'type' => 'bodyweight'],
            ['body_part' => 'legs', 'name' => 'カーフレイズ',           'type' => 'weight'],

            // 腹 (core)
            ['body_part' => 'core', 'name' => 'クランチ',               'type' => 'bodyweight'],
            ['body_part' => 'core', 'name' => 'プランク',               'type' => 'bodyweight'],
            ['body_part' => 'core', 'name' => 'レッグレイズ',           'type' => 'bodyweight'],
            ['body_part' => 'core', 'name' => 'アブローラー',           'type' => 'bodyweight'],
            ['body_part' => 'core', 'name' => 'サイドプランク',         'type' => 'bodyweight'],

            // 有酸素 (cardio)
            ['body_part' => 'cardio', 'name' => 'ランニング',           'type' => 'cardio'],
            ['body_part' => 'cardio', 'name' => 'ウォーキング',         'type' => 'cardio'],
            ['body_part' => 'cardio', 'name' => 'バイク（エアロバイク）', 'type' => 'cardio'],
            ['body_part' => 'cardio', 'name' => 'ローイングマシン',     'type' => 'cardio'],
            ['body_part' => 'cardio', 'name' => 'ステアクライマー',     'type' => 'cardio'],
        ];

        foreach ($exercises as $data) {
            $bodyPart = BodyPart::where('slug', $data['body_part'])->first();
            if ($bodyPart) {
                Exercise::firstOrCreate(
                    ['name' => $data['name'], 'user_id' => null],
                    [
                        'body_part_id' => $bodyPart->id,
                        'type'         => $data['type'],
                        'is_default'   => true,
                    ]
                );
            }
        }
    }
}
