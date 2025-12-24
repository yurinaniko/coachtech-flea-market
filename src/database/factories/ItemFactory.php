<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\User;
use App\Models\Condition;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word(),
            'brand' => $this->faker->company(),
            'condition_id' => Condition::factory(),
            'description' => $this->faker->sentence(),
            'price' => 1000,
            'img_url' => 'images/test.jpg',
        ];
    }
}
