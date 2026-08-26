<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 取引評価の認可。
 *
 * 評価は「購入者」と「出品者」の当事者2人だけが書ける。
 * 両方が埋まると取引が完了扱いになるため、第三者が書き込めると
 * 他人の取引を勝手に完了させられてしまう。
 */
class ChatReviewTest extends TestCase
{
    use RefreshDatabase;

    /** 購入者・出品者・無関係な第三者と、その取引を用意する */
    private function 取引を用意(): array
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $stranger = User::factory()->create();

        $item = Item::factory()->create(['user_id' => $seller->id]);
        $purchase = Purchase::factory()->create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
        ]);

        return [$seller, $buyer, $stranger, $purchase];
    }

    /** @test */
    public function 無関係な第三者は取引を評価できない()
    {
        [, , $stranger, $purchase] = $this->取引を用意();

        $this->actingAs($stranger)
            ->post("/chat/review/{$purchase->id}", ['rating' => 5])
            ->assertForbidden();

        $purchase->refresh();
        $this->assertNull($purchase->seller_reviewed);
        $this->assertNull($purchase->buyer_reviewed);
        $this->assertFalse((bool) $purchase->is_completed);
    }

    /** @test */
    public function 未ログインでは取引を評価できない()
    {
        [, , , $purchase] = $this->取引を用意();

        $this->post("/chat/review/{$purchase->id}", ['rating' => 5])
            ->assertRedirect('/login');

        $purchase->refresh();
        $this->assertNull($purchase->seller_reviewed);
    }

    /** @test */
    public function 購入者は購入者側の評価を書ける()
    {
        [, $buyer, , $purchase] = $this->取引を用意();

        $this->actingAs($buyer)
            ->post("/chat/review/{$purchase->id}", ['rating' => 4])
            ->assertRedirect();

        $purchase->refresh();
        $this->assertSame(4, $purchase->buyer_reviewed);
        $this->assertNull($purchase->seller_reviewed);
    }

    /** @test */
    public function 出品者は出品者側の評価を書ける()
    {
        [$seller, , , $purchase] = $this->取引を用意();

        $this->actingAs($seller)
            ->post("/chat/review/{$purchase->id}", ['rating' => 3])
            ->assertRedirect();

        $purchase->refresh();
        $this->assertSame(3, $purchase->seller_reviewed);
        $this->assertNull($purchase->buyer_reviewed);
    }

    /** @test */
    public function 双方が評価すると取引が完了になる()
    {
        [$seller, $buyer, , $purchase] = $this->取引を用意();

        $this->actingAs($buyer)->post("/chat/review/{$purchase->id}", ['rating' => 5]);
        $this->actingAs($seller)->post("/chat/review/{$purchase->id}", ['rating' => 5]);

        $purchase->refresh();
        $this->assertTrue((bool) $purchase->is_completed);
    }

    /** @test */
    public function 評価は1から5の範囲外を受け付けない()
    {
        [, $buyer, , $purchase] = $this->取引を用意();

        // 境界のOK側
        $this->actingAs($buyer)
            ->post("/chat/review/{$purchase->id}", ['rating' => 1])
            ->assertSessionHasNoErrors();

        // 境界のNG側
        $this->actingAs($buyer)
            ->post("/chat/review/{$purchase->id}", ['rating' => 0])
            ->assertSessionHasErrors('rating');

        $this->actingAs($buyer)
            ->post("/chat/review/{$purchase->id}", ['rating' => 6])
            ->assertSessionHasErrors('rating');
    }
}
