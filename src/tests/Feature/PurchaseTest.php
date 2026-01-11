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

    /** @test */
    public function user_can_complete_purchase_by_clicking_purchase_button()
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

    /** @test */
    public function purchased_item_is_displayed_as_sold_on_item_list_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'status'  => 'completed',
        ]);
        $response = $this->get(route('items.index'));
        $response->assertStatus(200);
        $response->assertSee('sold');
    }

    /** @test */
    public function purchased_item_is_shown_in_user_purchase_history_on_profile_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'name' => 'テスト商品',
        ]);
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'status'  => 'completed',
        ]);
        $response = $this->actingAs($user)->get(route('mypage.index'));
        $response->assertStatus(200);
        $response->assertSee('テスト商品');
    }

    /** @test */
    public function updated_address_is_reflected_on_purchase_screen()
    {
        $user = User::factory()->create();
        $user->profile()->create([
            'postal_code' => '111-1111',
            'address' => '東京都旧住所',
        ]);
        $user->profile()->update([
            'postal_code' => '222-2222',
            'address' => '東京都新住所',
        ]);
        $item = Item::factory()->create();
        $response = $this->actingAs($user)
            ->get(route('purchase.index', $item->id));
        $response->assertStatus(200);
        $response->assertSee('222-2222');
        $response->assertSee('東京都新住所');
    }

    /** @test */
    public function delivery_address_is_saved_with_purchase()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $data = [
            'payment_method' => 'card',
            'postal_code'    => '333-3333',
            'address'        => '東京都配送先住所',
            'building'       => '配送ビル',
        ];

        $this->actingAs($user)
            ->post(route('purchase.store', $item->id), $data);

        $this->assertDatabaseHas('purchases', [
            'item_id'          => $item->id,
            'sending_postcode' => '333-3333',
            'sending_address'  => '東京都配送先住所',
            'sending_building' => '配送ビル',
        ]);
    }
}