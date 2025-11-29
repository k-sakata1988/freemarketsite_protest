<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->word(),
            'brand' => $this->faker->company(),
            'price' => $this->faker->numberBetween(100, 10000),
            'description' => $this->faker->sentence(),
            'image_path' => 'dummy.jpg',
            'condition' => $this->faker->randomElement(['良好', '目立った汚れなし','やや傷や汚れあり','状態が悪い']),
            'is_recommended' => true,
        ];
    }
}