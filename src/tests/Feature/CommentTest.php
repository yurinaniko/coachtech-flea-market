<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function logged_in_user_can_post_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post(
            route('comment.store', $item->id),
            ['comment' => 'テストコメント']
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);
    }

    /** @test */
    public function guest_cannot_post_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post(
            route('comment.store', $item->id),
            ['comment' => 'ゲストコメント']
        );

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function comment_is_required()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post(
            route('comment.store', $item->id),
            ['comment' => '']
        );

        $response->assertSessionHasErrors('comment');
    }
}