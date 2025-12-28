<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
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
            'status'  => 'completed',
        ]);
    }

    private function validPurchaseData(): array
    {
        return [
            'payment_method' => 'card',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル101',
        ];
    }

    public function test_payment_method_is_required()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $data = $this->validPurchaseData();
        unset($data['payment_method']);

        $response = $this->actingAs($user)
            ->post(route('purchase.store', $item), $data);

        $response->assertSessionHasErrors(['payment_method']);
    }

    public function test_payment_method_must_be_valid_value()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $data = $this->validPurchaseData();
        $data['payment_method'] = 'cash';

        $response = $this->actingAs($user)
            ->post(route('purchase.store', $item), $data);

        $response->assertSessionHasErrors(['payment_method']);
    }

    public function test_postal_code_is_required()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $data = $this->validPurchaseData();
        unset($data['postal_code']);

        $response = $this->actingAs($user)
            ->post(route('purchase.store', $item), $data);

        $response->assertSessionHasErrors(['postal_code']);
    }

    public function test_postal_code_format_is_invalid()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $data = $this->validPurchaseData();
        $data['postal_code'] = '1234567';

        $response = $this->actingAs($user)
            ->post(route('purchase.store', $item), $data);

        $response->assertSessionHasErrors(['postal_code']);
    }

    public function test_address_is_required()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $data = $this->validPurchaseData();
        unset($data['address']);

        $response = $this->actingAs($user)
            ->post(route('purchase.store', $item), $data);

        $response->assertSessionHasErrors(['address']);
    }

    public function test_building_can_be_null()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $data = $this->validPurchaseData();
        $data['building'] = null;

        $response = $this->actingAs($user)
            ->post(route('purchase.store', $item), $data);

        $response->assertSessionHasNoErrors();
    }

    public function test_purchased_item_cannot_be_purchased_again()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'status'  => 'completed',
        ]);

        $data = $this->validPurchaseData();

        $response = $this->actingAs($user)
            ->post(route('purchase.store', $item), $data);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_purchase_item()
    {
        $item = Item::factory()->create();

        $data = $this->validPurchaseData();

        $response = $this->post(route('purchase.store', $item), $data);

        $response->assertRedirect('/login');
    }

    public function test_konbini_purchase_sets_pending_status()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $data = $this->validPurchaseData();
        $data['payment_method'] = 'konbini';

        $this->actingAs($user)
            ->post(route('purchase.store', $item), $data);

        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'status' => 'pending',
            'payment_method' => 'konbini',
        ]);
    }
}