<?php

// 4_商品一覧取得
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;

class ItembrowseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 全商品を取得できる()
    {
        $user = User::factory()->create();

        $items = Item::factory()->count(3)->create([
            'name' => 'テスト商品A',
            'status' => 'selling',
            'user_id' => $user->id,
            'condition' => '目立った汚れなし',
            'is_recommended' => true,
        ]);

        $response = $this->get(route('items.tab', ['type' => 'recommended']));

        foreach ($items as $item) {
            $response->assertSee('テスト商品A');
        }
    }

    /** @test */
    public function 購入済み商品は「Sold」と表示される()
    {
        $user = User::factory()->create();

        // 出品者とは別ユーザーで購入
        $seller = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'status' => 'selling',
            'condition' => '目立った汚れなし',
        ]);

        // Purchaseを作成し、item_idを指定
        Purchase::factory()->create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'status' => 'purchased',
        ]);

    $response = $this->actingAs($user)->get(route('items.tab', ['type' => 'recommended']));

    $response->assertSee('Sold');
}

    /** @test */
    public function 自分が出品した商品は表示されない()
    {
        $user = User::factory()->create();
        $myItem = Item::factory()->create([
            'user_id' => $user->id,
            'condition' => '目立った汚れなし',
        ]);
        $otherItem = Item::factory()->create([
            'user_id' => User::factory()->create()->id,
            'condition' => '目立った汚れなし',
        ]);

        $response = $this->actingAs($user)->get(route('items.tab', ['type' => 'recommended']));
        $response->assertDontSeeText($myItem->name);
        $response->assertSeeText($otherItem->name);
    }
}
