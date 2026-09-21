<?php

namespace Database\Factories;

use App\Models\Delivery;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Delivery>
 */
class DeliveryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference_no' => 'DEL-'.date('Ymd').'-'.fake()->unique()->numerify('####'),
            'supplier' => fake()->company(),
            'delivery_date' => fake()->date(),
            'status' => 'pending',
            'notes' => fake()->sentence(),
            'created_by' => User::factory(),
            'received_by' => null,
            'confirmed_at' => null,
        ];
    }

    /**
     * Indicate that the delivery has been received/delivered into stock.
     */
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'received_by' => User::factory(),
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Indicate that the delivery has been cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'confirmed_at' => now(),
        ]);
    }
}
