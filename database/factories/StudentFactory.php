<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
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
            'student_code' => strtoupper(fake()->bothify('SV########')),
            'class' => fake()->optional()->regexify('1[1-5]DHTH0[1-9]'),
            'cohort' => fake()->optional()->numberBetween(11, 15),
            'major' => fake()->optional()->randomElement([
                'Công nghệ thông tin',
                'Quản trị kinh doanh',
                'Công nghiệp thực phẩm',
            ]),
            'graduated' => fake()->boolean(),
        ];
    }
}
