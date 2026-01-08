<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


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

    public function test_can_access_mylist_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/mypage?tab=mylist');
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

        $matchedItem = Item::factory()->create([
            'name' => '腕時計 ロレックス',
            'user_id' => User::factory()->create()->id,
        ]);

        $unmatchedItem = Item::factory()->create([
            'name' => 'スニーカー ナイキ',
            'user_id' => User::factory()->create()->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/mypage?keyword=腕');

        $response->assertStatus(200);

        $response->assertSee('腕時計 ロレックス');

        $response->assertDontSee('スニーカー ナイキ');
    }

    public function test_favorite_keyword_search_filters_items_correctly()
    {
        $user = User::factory()->create();

        $favoriteMatched = Item::factory()->create([
            'name' => '腕時計 ロレックス',
        ]);

        $favoriteUnmatched = Item::factory()->create([
            'name' => 'スニーカー ナイキ',
        ]);

        $notFavoriteMatched = Item::factory()->create([
            'name' => '腕時計 セイコー',
        ]);

        $user->favorites()->attach([
            $favoriteMatched->id,
            $favoriteUnmatched->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/mypage?tab=mylist&keyword=腕');

        $response->assertStatus(200);

        $response->assertSee('腕時計 ロレックス');

        $response->assertDontSee('スニーカー ナイキ');
        $response->assertDontSee('腕時計 セイコー');
    }

    public function test_profile_sell_shows_only_user_items()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $myItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '自分の商品',
        ]);

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

        $user->purchases()->attach($item1->id, [
            'price' => $item1->price,
            'payment_method' => 'card',
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

    /** @test */
    public function name_cannot_exceed_20_characters()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('profile.store'), [
            'name' => str_repeat('あ', 21),
            'postal_code' => '123-4567',
            'address' => '東京都',
        ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function postal_code_is_required()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('profile.store'), [
            'name' => 'テスト',
            'postal_code' => '',
            'address' => '東京都',
        ]);

        $response->assertSessionHasErrors('postal_code');
    }

    /** @test */
    public function postal_code_with_invalid_characters_is_rejected()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('profile.store'), [
            'name' => 'テスト',
            'postal_code' => '12a-4567',
            'address' => '東京都',
        ]);

        $response->assertSessionHasErrors('postal_code');
    }

    /** @test */
    public function image_must_be_jpeg_or_png()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('profile.store'), [
            'name' => 'テスト',
            'postal_code' => '123-4567',
            'address' => '東京都',
            'image' => UploadedFile::fake()->create('test.pdf', 100),
        ]);

        $response->assertSessionHasErrors('image');
    }

    /** @test */
    public function profile_can_be_updated_without_image()
    {
        $user = User::factory()->create();

        $user->profile()->create([
            'postal_code' => '123-4567',
            'address' => '初期住所',
            'building' => null,
            'img_url' => null,
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => '更新ユーザー',
            'postal_code' => '123-4567',
            'address' => '東京都',
        ]);

        $response->assertRedirect(
            route('mypage.profile', ['page' => 'sell'])
        );

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都',
        ]);
    }
}
