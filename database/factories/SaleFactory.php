<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_name' => fake()->company(),
            'services' => fake()->name(),
            'paid_amount' => $paid_amount = random_int(500, 100000),
            'due_amount' => $paid_amount - random_int(100, 200),
            'sales_date' => fake()->date(),
            'remarks' => fake()->paragraph(),
            'file' => null,
        ];
    }
}
