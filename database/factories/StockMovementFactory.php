<?php

namespace Database\Factories;

use App\Models\Medicine;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockMovement>
 */
class StockMovementFactory extends Factory
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
            'batch_id' => StockBatch::factory(),
            'type' => 'in',
            'quantity' => fake()->numberBetween(10, 200),
            'reference_type' => 'delivery',
            'reference_id' => null,
            'notes' => null,
            'created_by' => User::factory(),
        ];
    }
}
