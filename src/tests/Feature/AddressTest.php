<?php

// 12_AddressTest.php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use Stripe\PaymentIntent;
use Mockery;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Stripe PaymentIntent モック
        $mock = Mockery::mock('overload:' . PaymentIntent::class);
        $mock->shouldReceive('create')->andReturn((object)[
            'id' => 'pi_mock',
            'client_secret' => 'cs_test_mock',
            'next_action' => (object)[
                'konbini_display_details' => (object)['hosted_voucher_url' => 'https://example.com/voucher']
            ]
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

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