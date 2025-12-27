<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function logged_in_user_can_purchase_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post(
            route('purchase.store', $item->id),
            [
                'payment_method' => 'card',
                'postal_code'    => '123-4567',
                'address'        => '東京都渋谷区',
                'building'       => 'テストビル',
            ]
        );

        $response->assertRedirect(route('purchase.checkout'));

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'status'  => 'pending',
        ]);
    }
}