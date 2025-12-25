<?php

namespace Database\Factories;

use App\Enums\Symbol;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' =>  User::query()->inRandomOrder()->value('id'), 
            'symbol' => $this->faker->randomElement(Symbol::values()),
            'side' => $this->faker->randomElement(['buy', 'sell']),
            'price' => $this->faker->randomFloat(2, 100, 60000), 
            'amount' => $this->faker->randomFloat(2, 0.01, 5),
            'status' => $this->faker->randomElement([1, 2, 3]),
        ];
    }
}
