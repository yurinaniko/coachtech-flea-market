<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class MypageTest extends TestCase
{
    use RefreshDatabase;
    public function test_guest_cannot_access_mypage()
    {
        $response = $this->get('/mypage');
        $response->assertRedirect('/login');
    }

    public function test_logged_in_user_can_access_mypage()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/mypage');

        $response->assertStatus(200);
    }

    public function test_default_page_is_recommend()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/mypage');

        $response->assertStatus(200);
        $response->assertViewHas('page', 'recommend');
    }

    public function test_can_access_favorite_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/mypage?page=favorite');

        $response->assertStatus(200);
    }

    public function test_can_search_items_by_keyword()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/mypage?keyword=腕');

        $response->assertStatus(200);
    }

    public function test_keyword_search_displays_matching_items_only()
    {
        $user = User::factory()->create();

        // 検索にヒットする商品
        $matchedItem = Item::factory()->create([
            'name' => '腕時計 ロレックス',
            'user_id' => User::factory()->create()->id, // 他人の商品
        ]);

        // 検索にヒットしない商品
        $unmatchedItem = Item::factory()->create([
            'name' => 'スニーカー ナイキ',
            'user_id' => User::factory()->create()->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/mypage?keyword=腕');

        $response->assertStatus(200);

        // ヒットする商品は表示される
        $response->assertSee('腕時計 ロレックス');

        // ヒットしない商品は表示されない
        $response->assertDontSee('スニーカー ナイキ');
    }

    public function test_favorite_keyword_search_filters_items_correctly()
    {
        $user = User::factory()->create();

        // お気に入り + keyword一致
        $favoriteMatched = Item::factory()->create([
            'name' => '腕時計 ロレックス',
        ]);

        // お気に入り + keyword不一致
        $favoriteUnmatched = Item::factory()->create([
            'name' => 'スニーカー ナイキ',
        ]);

        // keyword一致だが「お気に入りじゃない」
        $notFavoriteMatched = Item::factory()->create([
            'name' => '腕時計 セイコー',
        ]);

        // お気に入り登録（Aユーザー）
        $user->favorites()->attach([
            $favoriteMatched->id,
            $favoriteUnmatched->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/mypage?page=favorite&keyword=腕');

        $response->assertStatus(200);

        // 表示される
        $response->assertSee('腕時計 ロレックス');

        // 表示されない
        $response->assertDontSee('スニーカー ナイキ');
        $response->assertDontSee('腕時計 セイコー');
    }

    public function test_profile_sell_shows_only_user_items()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // 自分の出品商品
        $myItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '自分の商品',
        ]);

        // 他人の商品
        $otherItem = Item::factory()->create([
            'user_id' => $otherUser->id,
            'name' => '他人の商品',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/mypage/profile?page=sell');

        $response->assertStatus(200);

        $response->assertSee('自分の商品');
        $response->assertDontSee('他人の商品');
    }

    public function test_profile_buy_shows_only_purchased_items()
    {
        $user = User::factory()->create();

        $item1 = Item::factory()->create([
            'name' => '購入した商品',
            'price' => 10000,
        ]);

        $item2 = Item::factory()->create([
            'name' => '購入していない商品',
            'price' => 20000,
        ]);

        // 購入済みとして紐付け（price 必須）
        $user->purchases()->attach($item1->id, [
            'price' => $item1->price,
            'payment_method' => 'card', // or 'konbini'
            'sending_postcode' => '123-4567',
            'sending_address' => '東京都テスト区1-2-3',
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/mypage/profile?page=buy');

        $response->assertStatus(200);
        $response->assertSee('購入した商品');
        $response->assertDontSee('購入していない商品');
    }
}

