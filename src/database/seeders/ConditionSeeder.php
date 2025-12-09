<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Condition;

class ConditionSeeder extends Seeder
{
    public function run(): void
    {
        $conditions = [
            ['condition' => '良好'],
            ['condition' => '目立った傷や汚れなし'],
            ['condition' => 'やや傷や汚れあり'],
            ['condition' => '状態が悪い'],
        ];

        foreach ($conditions as $cond) {
            Condition::create($cond);
        }
    }
}