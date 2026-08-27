<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 取引チャット（chat.show / chat.store / chat.destroy）の認可。
 *
 * チャットは「購入者」と「出品者」の当事者だけが閲覧・投稿でき、
 * メッセージ削除は「そのメッセージを書いた本人」だけができる。
 * クライアントから来た item_id / comment_id を、サーバー側で
 * 「自分が当事者か・自分のメッセージか」まで確認しているかを担保する。
 * （review 側の認可は ChatReviewTest が担当）
 */
class ChatAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /** 出品者・購入者・無関係な第三者と、その取引を用意する */
    private function 取引を用意(array $purchaseOverrides = []): array
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $stranger = User::factory()->create();

        $item = Item::factory()->create(['user_id' => $seller->id]);
        $purchase = Purchase::factory()->create(array_merge([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
        ], $purchaseOverrides));

        return compact('seller', 'buyer', 'stranger', 'item', 'purchase');
    }

    /** 当事者が書いたチャットメッセージを1件作る */
    private function メッセージ(User $author, array $ctx): Comment
    {
        return Comment::create([
            'user_id' => $author->id,
            'item_id' => $ctx['item']->id,
            'purchase_id' => $ctx['purchase']->id,
            'comment' => 'こんにちは',
            'is_read' => false,
        ]);
    }

    // ---- 閲覧（chat.show） ----

    /** @test */
    public function 第三者は他人の取引チャットを閲覧できない()
    {
        $ctx = $this->取引を用意();

        $this->actingAs($ctx['stranger'])
            ->get("/chat/{$ctx['item']->id}")
            ->assertNotFound();
    }

    /** @test */
    public function 当事者は取引チャットを閲覧できる()
    {
        $ctx = $this->取引を用意();

        $this->actingAs($ctx['buyer'])
            ->get("/chat/{$ctx['item']->id}")
            ->assertOk();
        $this->actingAs($ctx['seller'])
            ->get("/chat/{$ctx['item']->id}")
            ->assertOk();
    }

    // ---- 投稿（chat.store） ----

    /** @test */
    public function 第三者は他人の取引チャットに投稿できない()
    {
        $ctx = $this->取引を用意();

        $this->actingAs($ctx['stranger'])
            ->post("/chat/{$ctx['item']->id}", ['comment' => '横入り'])
            ->assertNotFound();

        $this->assertDatabaseMissing('comments', ['comment' => '横入り']);
    }

    /** @test */
    public function 未ログインでは取引チャットに投稿できない()
    {
        $ctx = $this->取引を用意();

        $this->post("/chat/{$ctx['item']->id}", ['comment' => 'ゲスト'])
            ->assertRedirect(route('login'));

        $this->assertDatabaseMissing('comments', ['comment' => 'ゲスト']);
    }

    /** @test */
    public function 当事者は取引チャットに投稿できる()
    {
        $ctx = $this->取引を用意();

        $this->actingAs($ctx['buyer'])
            ->post("/chat/{$ctx['item']->id}", ['comment' => '購入者メッセージ'])
            ->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'purchase_id' => $ctx['purchase']->id,
            'user_id' => $ctx['buyer']->id,
            'comment' => '購入者メッセージ',
        ]);
    }

    /** @test */
    public function 完了した取引には投稿できない()
    {
        $ctx = $this->取引を用意(['is_completed' => true]);

        $this->actingAs($ctx['buyer'])
            ->post("/chat/{$ctx['item']->id}", ['comment' => '完了後メッセージ'])
            ->assertForbidden();

        $this->assertDatabaseMissing('comments', ['comment' => '完了後メッセージ']);
    }

    // ---- 境界値（投稿は最大400文字） ----

    /** @test */
    public function チャットは400文字ちょうどは投稿でき401文字は弾かれる()
    {
        $ctx = $this->取引を用意();

        // OK 側の境界：400文字ちょうど
        $this->actingAs($ctx['buyer'])
            ->post("/chat/{$ctx['item']->id}", ['comment' => str_repeat('あ', 400)])
            ->assertRedirect();
        $this->assertDatabaseHas('comments', ['comment' => str_repeat('あ', 400)]);

        // NG 側の境界：401文字
        $this->actingAs($ctx['buyer'])
            ->post("/chat/{$ctx['item']->id}", ['comment' => str_repeat('い', 401)])
            ->assertSessionHasErrors('comment');
        $this->assertDatabaseMissing('comments', ['comment' => str_repeat('い', 401)]);
    }

    // ---- 削除（chat.destroy） ----

    /** @test */
    public function 他人のメッセージは削除できない()
    {
        $ctx = $this->取引を用意();
        $message = $this->メッセージ($ctx['buyer'], $ctx);

        // 無関係な第三者
        $this->actingAs($ctx['stranger'])
            ->delete("/chat/{$message->id}")
            ->assertForbidden();

        // 取引の相手（出品者）でも、本人でなければ削除不可
        $this->actingAs($ctx['seller'])
            ->delete("/chat/{$message->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('comments', ['id' => $message->id]);
    }

    /** @test */
    public function 自分のメッセージは削除できる()
    {
        $ctx = $this->取引を用意();
        $message = $this->メッセージ($ctx['buyer'], $ctx);

        $this->actingAs($ctx['buyer'])
            ->delete("/chat/{$message->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('comments', ['id' => $message->id]);
    }
}
