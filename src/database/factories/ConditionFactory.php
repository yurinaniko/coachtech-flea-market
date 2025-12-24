<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Condition;

class ConditionFactory extends Factory
{
    protected $model = Condition::class;
    public function definition()
    {
        return [
            'condition' => $this->faker->word(),
        ];
    }
}
