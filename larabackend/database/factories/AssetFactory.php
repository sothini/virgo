<?php

namespace Database\Factories;

use App\Enums\Symbol;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    protected $model = Asset::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->randomUserId(),
            'symbol' => $this->faker->randomElement(Symbol::values()),
            'amount' => fake()->randomFloat(2, 0, 10_000),
            'locked_amount' => fake()->randomFloat(2, 0, 1_000),
        ];
    }

    /**
     * Force the asset to belong to a specific user.
     */
    public function withUserId(int $userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }

    /**
     * Resolve a user id: use the provided state, an existing random user,
     * or create one if none exist.
     */
    protected function randomUserId(): Factory|int
    {
        return User::query()->inRandomOrder()->value('id') ?? User::factory();
    }
}

