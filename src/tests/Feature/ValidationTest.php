<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function コメントが送信できる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create();

        $response = $this->post('/items/' . $item->id . '/comments', [
            'comment' => 'テストコメント'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'テストコメント'
        ]);
    }
    /** @test */
    public function 出品時に必須項目が未入力だとバリデーションエラーになる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/sell', [

        ]);

        $response->assertSessionHasErrors([
            'name',
            'category_id',
            'condition_id',
            'price',
            'description',
            'image_path',
        ]);
    }

    /** @test */
    public function コメント内容が必須であること()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post("/items/{$item->id}/comments", [
                'comment' => '',
            ]);

        $response->assertSessionHasErrors(['comment']);
    }

    /** @test */
    public function コメントが最大文字数を超えるとエラーになる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post("/items/{$item->id}/comments", [
                'comment' => str_repeat('あ', 256),
            ]);

        $response->assertSessionHasErrors(['comment']);
    }

    /** @test */
    public function 正しい内容ならコメント投稿できる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
            ->post("/items/{$item->id}/comments", [
                'comment' => 'テストコメント',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'user' => $user->name,
                'body' => 'テストコメント',
            ]);

            $this->assertDatabaseHas('comments', [
                'user_id' => $user->id,
                'item_id' => $item->id,
                'comment' => 'テストコメント',
            ]);
    }

    /** @test */
    public function 未ログインユーザーはコメント投稿できない()
    {
        $item = Item::factory()->create();

        $response = $this->post("/items/{$item->id}/comments", [
            'comment' => 'テストコメント',
        ]);

        $response->assertRedirect('/login');
    }
}