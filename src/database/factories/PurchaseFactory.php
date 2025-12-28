<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'price' => 1000,
            'status' => 'completed',
            'payment_method' => 'card',
            'sending_postcode' => '123-4567',
            'sending_address' => '東京都',
            'sending_building' => null,
        ];
    }
}