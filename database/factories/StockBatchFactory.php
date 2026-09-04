<?php

namespace Database\Factories;

use App\Models\Medicine;
use App\Models\StockBatch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockBatch>
 */
class StockBatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'medicine_id' => Medicine::factory(),
            'batch_no' => fake()->bothify('BATCH-####'),
            'quantity_received' => 100,
            'quantity_remaining' => 100,
            'expiry_date' => fake()->dateTimeBetween('+1 month', '+2 years')->format('Y-m-d'),
            'received_date' => fake()->date(),
            'supplier' => fake()->company(),
        ];
    }
}
