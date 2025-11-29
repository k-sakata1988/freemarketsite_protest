<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;
use App\Models\User;
use App\Models\Address;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Mockery;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \Stripe\Stripe::setApiKey('sk_test_mock');

        $mock = Mockery::mock('overload:' . \Stripe\PaymentIntent::class);
        $mock->shouldReceive('create')->andReturn((object)[
            'id' => 'pi_mock',
            'client_secret' => 'cs_test_mock',
            'next_action' => (object)[
                'konbini_display_details' => (object)['hosted_voucher_url' => 'https://example.com/voucher']
            ]
        ]);
    }
    /** @test */
    public function 購入ボタンを押すと購入が完了する()
    {
        Stripe::setApiKey('sk_test_mock');

        $mock = Mockery::mock('overload:' . PaymentIntent::class);
        $mock->shouldReceive('create')->andReturn((object)[
            'id' => 'pi_mock'
        ]);

        $user = User::factory()->create();
        $item = Item::factory()->create();
        Address::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('purchase.store', $item), [
            'payment_method_type' => 'credit',
            'payment_method_id' => 'pm_mock',
        ]);

        $this->assertDatabaseHas('purchases', ['item_id' => $item->id]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
