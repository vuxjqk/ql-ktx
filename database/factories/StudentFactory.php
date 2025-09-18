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
            'major' => fake()->optional()->randomElement([
                'Công nghệ thông tin',
                'Quản trị kinh doanh',
                'Công nghiệp thực phẩm',
            ]),
            'class' => fake()->optional()->regexify('1[1-5]DH0[1-9]'),
        ];
    }
}
