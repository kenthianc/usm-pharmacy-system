<?php

namespace Database\Factories;

use App\Models\Medicine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Medicine>
 */
class MedicineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('???-###-???')),
            'barcode' => fake()->ean13(),
            'name' => fake()->word(),
            'generic_name' => fake()->word(),
            'category' => 'Antibiotic',
            'unit' => 'Tablet',
            'unit_price' => fake()->randomFloat(2, 1, 100),
            'reorder_level' => 10,
            'is_active' => true,
        ];
    }

    /**
     * Mark the medicine as inactive.
     */
    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
