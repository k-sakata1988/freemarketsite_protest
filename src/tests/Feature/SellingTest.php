<?php

// 15_SellingTest.php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Category;
use App\Models\Condition;

class SellingTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function 商品出品画面に必要な情報が保存されること()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $condition = Condition::factory()->create();

        $response = $this->actingAs($user)->post(route('items.store'), [
            'name' => 'Test Item',
            'category_id' => [$category->id],
            'condition_id' => $condition->id,
            'price' => 1000,
            'description' => 'desc'
        ]);

        $response->assertRedirect();
    }
}
