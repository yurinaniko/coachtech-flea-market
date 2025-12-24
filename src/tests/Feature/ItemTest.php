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
        $response = $this->post(route('items.store'), [
            'name' => 'テスト商品',
            'price' => 1000,
            'condition_id' => 1,
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
}
