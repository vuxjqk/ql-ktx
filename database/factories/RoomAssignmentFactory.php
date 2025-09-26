<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomAssignment>
 */
class RoomAssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->numberBetween(12, 21),
            'room_id' => fake()->numberBetween(1, 15),
            'checked_in_at' => fake()->dateTimeBetween('-1 years', '-6 month'),
            'checked_out_at' => fake()->dateTimeBetween('-6 month'),
        ];
    }
}
