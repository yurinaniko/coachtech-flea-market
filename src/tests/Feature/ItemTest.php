<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function item_factory_creates_item_correctly()
    {
        $item = Item::factory()->create();

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => $item->name,
        ]);
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
    public function logged_in_user_can_create_item()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $condition = Condition::factory()->create();
        $response = $this->actingAs($user)->post(route('items.store'), [
            'name' => 'テスト商品',
            'price' => 1000,
            'condition_id' => $condition->id,
            'description' => 'テスト説明',
            'img_url' => UploadedFile::fake()->image('test.jpg'),
            'categories' => [$category->id],
        ]);

        $response->assertRedirect(route('mypage.index'));

        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'user_id' => $user->id,
        ]);
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

    public function test_categories_must_not_be_integer()
    {
        $user = User::factory()->create();

        $data = $this->validItemData();
        $data['categories'] = 1;

        $response = $this->actingAs($user)
        ->post(route('items.store'), $data);

        $response->assertSessionHasErrors(['categories']);
    }
}
