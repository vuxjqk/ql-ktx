<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Room;
use App\Models\Student;
use App\Models\User;
use DateTime;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'date_of_birth' => new DateTime('2004-10-10'),
            'gender' => 'male',
            'avatar' => null,
            'role' => 'super_admin',
        ]);

        Student::factory(10)->create();
        Branch::factory(3)->create();
        Room::factory(15)->create();
    }
}
