<?php

// 12_AddressTest.php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;

class AddressTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function 送付先住所変更画面にて登録した住所が商品購入画面に反映される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('purchase.create', $item->id));
        $response->assertSee($address->postal_code);
    }
}