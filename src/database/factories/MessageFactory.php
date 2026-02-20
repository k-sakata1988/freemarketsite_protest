<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Purchase;
use App\Models\User;

class MessageFactory extends Factory
{
    public function definition()
    {
        return [
            'purchase_id' => Purchase::factory(),
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
            'message' => $this->faker->sentence(),
            'read_at' => null,
            'image_path' => null,
        ];
    }
}
