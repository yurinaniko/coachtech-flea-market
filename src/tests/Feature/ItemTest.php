<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Comment;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function item_list_displays_items_except_own_items()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '自分の商品',
        ]);
        $otherItem = Item::factory()->create([
            'user_id' => $otherUser->id,
            'name' => '他人の商品',
        ]);
        $response = $this->actingAs($user)
            ->get(route('items.index'));
        $response->assertSee('他人の商品');
        $response->assertDontSee('自分の商品');
    }

    /** @test */
    public function guest_can_view_item_list()
    {
        Item::factory()->create(['name' => '商品A']);
        Item::factory()->create(['name' => '商品B']);

        $response = $this->get(route('items.index'));

        $response->assertSee('商品A');
        $response->assertSee('商品B');
    }

    /** @test */
    public function items_can_be_searched_by_partial_name()
    {
        Item::factory()->create(['name' => 'テスト商品A']);
        Item::factory()->create(['name' => 'サンプル']);

        $response = $this->get(route('items.index', ['keyword' => 'テスト']));

        $response->assertSee('テスト商品A');
        $response->assertDontSee('サンプル');
    }

    /** @test */
    public function item_detail_displays_multiple_categories()
    {
        $item = Item::factory()->create();
        $categories = Category::factory()->count(2)->create();
        $item->categories()->attach($categories->pluck('id'));
        $response = $this->get(route('items.show', $item));
        $response->assertStatus(200);
        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }

    /** @test */
    public function item_detail_displays_all_required_information()
    {
        $user = User::factory()->create();
        $condition = Condition::factory()->create([
            'condition' => '新品',
        ]);
        $categories = Category::factory()->count(2)->create();
        $item = Item::factory()->create([
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 12,345,
            'description' => 'テスト説明文',
            'condition_id' => $condition->id,
            'img_url' => 'images/test.jpg',
        ]);
        $item->categories()->attach($categories->pluck('id'));
        $user->favorites()->attach($item->id);
        $commentUser = User::factory()->create();
        $comment = Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $commentUser->id,
            'comment' => 'とても良い商品です',
        ]);
        $response = $this->get(route('items.show', $item));
        $response->assertStatus(200);
        $response->assertSee('テスト商品');
        $response->assertSee('テストブランド');
        $response->assertSee('12345');
        $response->assertSee('テスト説明文');
        $response->assertSee('新品');
        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
        $response->assertSee('1');
        $response->assertSee('1');
        $response->assertSee('とても良い商品です');
        $response->assertSee($commentUser->name);
        $response->assertSee('images/test.jpg');
    }

    /** @test */
    public function guest_cannot_create_item()
    {
        $condition = Condition::factory()->create();
        $response = $this->post(route('items.store'), [
            'name' => 'テスト商品',
            'price' => 1000,
            'condition_id' => $condition->id,
            'description' => 'テスト説明',
            'img_url' => UploadedFile::fake()->image('test.jpg'),
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('items', [
            'name' => 'テスト商品',
        ]);
    }

    /** @test */
    public function logged_in_user_can_store_item_with_required_information()
    {
        $user = User::factory()->create();
        $condition = Condition::factory()->create();
        $categories = Category::factory()->count(2)->create();

        $response = $this->actingAs($user)->post(route('items.store'), [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト商品の説明です',
            'price' => 5000,
            'condition_id' => $condition->id,
            'categories' => $categories->pluck('id')->toArray(),
            'img_url' => UploadedFile::fake()->image('item.jpg'),
        ]);
        $response->assertRedirect(route('mypage.index'));
        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト商品の説明です',
            'price' => 5000,
            'condition_id' => $condition->id,
            'user_id' => $user->id,
        ]);
        $item = Item::where('name', 'テスト商品')->first();
        foreach ($categories as $category) {
            $this->assertDatabaseHas('category_item', [
                'item_id' => $item->id,
                'category_id' => $category->id,
            ]);
        }
    }
    private function validItemData(): array
    {
        return [
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'description' => 'テスト商品説明',
            'price' => 1000,
            'img_url' => UploadedFile::fake()->image('test.png'),
        ];
    }

    public function test_name_is_required()
    {
        $user = User::factory()->create();

        $data = $this->validItemData();
        $data['name'] = '';

        $response = $this->actingAs($user)->post(route('items.store'), $data);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_name_must_be_less_than_100_characters()
    {
        $user = User::factory()->create();

        $data = $this->validItemData();
        $data['name'] = str_repeat('a', 101);

        $response = $this->actingAs($user)->post(route('items.store'), $data);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_description_must_be_less_than_255_characters()
    {
        $user = User::factory()->create();

        $data = $this->validItemData();
        $data['description'] = str_repeat('a', 256);

        $response = $this->actingAs($user)->post(route('items.store'), $data);

        $response->assertSessionHasErrors(['description']);
    }

    public function test_price_must_be_zero_or_more()
    {
        $user = User::factory()->create();

        $data = $this->validItemData();
        $data['price'] = -1;

        $response = $this->actingAs($user)->post(route('items.store'), $data);

        $response->assertSessionHasErrors(['price']);
    }

    public function test_image_is_required()
    {
        $user = User::factory()->create();

        $data = $this->validItemData();
        unset($data['img_url']);

        $response = $this->actingAs($user)->post(route('items.store'), $data);

        $response->assertSessionHasErrors(['img_url']);
    }

    public function test_categories_are_required()
    {
        $user = User::factory()->create();

        $data = $this->validItemData();
        $data['categories'] = [];

        $response = $this->actingAs($user)->post(route('items.store'), $data);

        $response->assertSessionHasErrors(['categories']);
    }

    public function test_brand_can_be_null()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $condition = Condition::factory()->create();

        $data = $this->validItemData();
        $data['brand'] = null;
        $data['categories'] = [$category->id];
        $data['condition_id'] = $condition->id;

        $response = $this->actingAs($user)
            ->post(route('items.store'), $data);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('items', [
            'name' => $data['name'],
            'brand' => null,
        ]);
    }

    public function test_categories_must_be_array()
    {
        $user = User::factory()->create();

        $data = $this->validItemData();
        $data['categories'] = '1';

        $response = $this->actingAs($user)
            ->post(route('items.store'), $data);

        $response->assertSessionHasErrors(['categories']);
    }

    public function test_categories_must_be_sent_as_array_even_if_only_one()
    {
        $user = User::factory()->create();

        $data = $this->validItemData();
        $data['categories'] = 1;

        $response = $this->actingAs($user)
        ->post(route('items.store'), $data);

        $response->assertSessionHasErrors(['categories']);
    }
}
