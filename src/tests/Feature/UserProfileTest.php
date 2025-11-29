<?php

// 13_UserProfileTest.php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function 必要な情報が取得できるできる_プロフィール画像_ユーザー名_出品した商品一覧_購入した商品一覧()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('mypage.index'));
        $response->assertSee($user->name);
    }
}