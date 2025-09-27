<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomRegistration>
 */
class RoomRegistrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'student']),
            'room_id' => fake()->numberBetween(1, 15),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'notes' => fake()->optional()->words(3, true),
            'processed_at' => fake()->optional()->dateTimeBetween('-6 month'),
            'processed_by' => fake()->optional()->numberBetween(1, 11),
            'requested_at' => fake()->dateTimeBetween('-6 month'),
        ];
    }
}
