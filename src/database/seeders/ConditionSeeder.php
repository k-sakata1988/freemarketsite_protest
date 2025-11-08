<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConditionSeeder extends Seeder
{
    public function run(): void
    {
        $conditions = [
            '良好',
            '目立った汚れなし',
            'やや傷や汚れあり',
            '状態が悪い',
        ];

        foreach ($conditions as $condition) {
            DB::table('conditions')->insert([
                'name' => $condition,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}