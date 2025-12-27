<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function logged_in_user_can_favorite_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('favorite.toggle', $item));

        $response->assertStatus(302);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function logged_in_user_can_unfavorite_item()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 先にいいね
        $this->actingAs($user)
            ->post(route('favorite.toggle', $item));

        // いいね解除
        $this->actingAs($user)
            ->post(route('favorite.toggle', $item));

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}