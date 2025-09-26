<?php

namespace Database\Factories;

use App\Models\RoomAssignment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bill>
 */
class BillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify(),
            'user_id' => fake()->numberBetween(12, 21),
            'room_assignment_id' => RoomAssignment::factory(),
            'amount' => fake()->numberBetween(1_000_000, 3_000_000),
            'status' => fake()->randomElement(['pending', 'paid', 'failed']),
            'due_date' => fake()->dateTimeBetween('-6 month'),
        ];
    }
}
