<?php

// 8_FavoriteTest.php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;
use App\Models\User;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function アイテムにいいねができる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post(route('favorite.toggle', $item));
        $response->assertJson(['liked' => true]);
    }
}