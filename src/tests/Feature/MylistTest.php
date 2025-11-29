<?php
// 5_MylistTest.php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;
use App\Models\User;
use App\Models\Purchase;

class MylistTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function いいねした商品だけが表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $user->favorites()->attach($item->id);

        $response = $this->actingAs($user)->get(route('items.tab', ['type' => 'mylist']));
        $response->assertSee($item->name);
    }

    /** @test */
    public function 購入済み商品はSoldと表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $user->favorites()->attach($item->id);
        Purchase::factory()->create(['item_id' => $item->id]);

        $response = $this->actingAs($user)->get(route('items.tab', ['type' => 'mylist']));
        $response->assertSee('Sold');
    }

    /** @test */
    public function 未認証の場合には何も表示されない()
    {
        $response = $this->get(route('items.tab', ['type' => 'mylist']));
        $response->assertSee('商品がありません');
    }
}
