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
        $images = [
            'dummy/onion.jpeg',
            'dummy/watch.jpeg',
            'dummy/shoes.jpeg',
            'dummy/bag.jpeg',
            'dummy/laptop.jpeg',
            'dummy/hdd.jpeg',
            'dummy/mike.jpeg',
            'dummy/tumbler.jpeg',
            'dummy/coffee-mill.jpeg',
            'dummy/makeup.jpeg',
        ];

        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word(),
            'brand' => $this->faker->company(),
            'price' => $this->faker->numberBetween(1000, 50000),
            'description' => $this->faker->sentence(),
            'condition_id' => Condition::factory(),
            'img_url' => $this->faker->randomElement($images),
        ];
    }
}
