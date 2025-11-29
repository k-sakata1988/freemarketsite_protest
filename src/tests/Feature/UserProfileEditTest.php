<?php

// 14_UserProfileEditTest.php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Address;

class UserProfileEditTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function 変更項目が初期値として過去設定されていること()
    {
        $user = User::factory()->create();
        Address::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('mypage.profile.edit'));
        $response->assertSee($user->name);
    }
}