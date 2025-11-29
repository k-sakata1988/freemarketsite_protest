<?php

// 11_PaymentMethodTest.php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;
use App\Models\User;
use App\Models\Address;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Mockery;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function 小計画面で変更が反映される()
    {
        Stripe::setApiKey('sk_test_mock');

        $mock = Mockery::mock('overload:' . PaymentIntent::class);
        $mock->shouldReceive('create')->andReturn((object)[
            'id' => 'pi_mock'
        ]);

        $user = User::factory()->create();
        $address = Address::factory()->create([
            'user_id' => $user->id,
        ]);
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post(route('purchase.store', $item), [
            'payment_method_type' => 'credit',
            'payment_method_id' => 'pm_mock',
        ]);

        $response->assertRedirect(route('items.index'));
        $response->assertSessionHas('success', '商品を購入しました！（テスト動作）');
    }
}