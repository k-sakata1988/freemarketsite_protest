<?php

// 6_ItemSearchTest.php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function 商品名で部分一致検索ができる()
    {
        Item::factory()->create(['name' => 'Apple Watch']);
        $response = $this->get('/items/recommended/search?keyword=Apple');
        $response->assertSee('Apple Watch');
    }
}