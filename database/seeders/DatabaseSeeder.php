<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Floor;
use App\Models\Room;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'super_admin',
        ]);

        User::factory(20)->create();

        $branches = collect([
            ['name' => 'Chi nhánh Hà Nội', 'address' => '123 Đường ABC, Hà Nội'],
            ['name' => 'Chi nhánh Đà Nẵng', 'address' => '456 Đường XYZ, Đà Nẵng'],
            ['name' => 'Chi nhánh TP.HCM', 'address' => '789 Đường DEF, TP.HCM'],
        ])->map(function ($branchData) {
            return Branch::create($branchData);
        });

        foreach ($branches as $branch) {
            $floorsCount = rand(3, 5);

            for ($floorNumber = 1; $floorNumber <= $floorsCount; $floorNumber++) {
                $floor = Floor::create([
                    'branch_id' => $branch->id,
                    'floor_number' => $floorNumber,
                    'gender_type' => ['male', 'female', 'mixed'][array_rand(['male', 'female', 'mixed'])],
                ]);

                $roomsCount = rand(3, 5);

                for ($i = 1; $i <= $roomsCount; $i++) {
                    Room::create([
                        'floor_id' => $floor->id,
                        'room_code' => 'R' . $floorNumber . '-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                        'price_per_day' => rand(200000, 500000),
                        'price_per_month' => rand(4000000, 10000000),
                        'capacity' => rand(1, 4),
                        'current_occupancy' => 0,
                        'is_active' => true,
                        'description' => 'Phòng số ' . $i . ' trên tầng ' . $floorNumber,
                    ]);
                }
            }
        }
    }
}
