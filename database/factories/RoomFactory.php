<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $block = fake()->randomElement(['A', 'B', 'C']);
        $floor = fake()->numberBetween(1, 5);
        $capacity = fake()->numberBetween(1, 5);
        return [
            'room_code' => $block . $floor . str_pad(fake()->numberBetween(1, 99), 2, '0', STR_PAD_LEFT),
            'block' => $block,
            'floor' => $floor,
            'gender_type' => fake()->randomElement(['male', 'female', 'mixed']),
            'price_per_month' => fake()->numberBetween(1_000_000, 3_000_000),
            'capacity' => $capacity,
            'current_occupancy' => fake()->numberBetween(0, $capacity),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
