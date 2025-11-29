<?php

// 7_商品詳細情報取得
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;
use App\Models\User;

class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 必要な情報が表示される()
    {
        $user = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Item',
            'brand' => 'Test Brand',
            'price' => 1000,
            'description' => 'Test Description',
            'condition' => '目立った汚れなし',
            'status' => 'selling',
        ]);

        $response = $this->get(route('items.show', $item));

        $response->assertStatus(200);
        $response->assertSee($item->name);
        $response->assertSee($item->brand);
        $response->assertSee(number_format($item->price));
        $response->assertSee($item->description);
        $response->assertSee($item->condition);
    }
}
