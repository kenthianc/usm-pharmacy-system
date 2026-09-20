<?php

namespace Database\Factories;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Medicine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryItem>
 */
class DeliveryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'delivery_id' => Delivery::factory(),
            'medicine_id' => Medicine::factory(),
            'quantity' => fake()->numberBetween(10, 200),
            'batch_no' => fake()->bothify('BATCH-####'),
            'expiry_date' => fake()->dateTimeBetween('+6 months', '+2 years')->format('Y-m-d'),
            'stock_batch_id' => null,
        ];
    }
}
