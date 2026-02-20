<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\Item;
use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'address_id' => Address::factory(),
            'payment_method' => $this->faker->randomElement(['convenience_store','credit_card']),
            'status' => 'purchased',
            // 'total' => $this->faker->numberBetween(100, 10000),
        ];
    }


    public function purchased()
    {
        return $this->state(fn () => [
            'status' => 'purchased',
        ]);
    }

    public function completed()
    {
        return $this->state(fn () => [
            'status' => 'completed',
        ]);
    }
}