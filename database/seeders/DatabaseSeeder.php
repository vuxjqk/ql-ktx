<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Booking;
use App\Models\Branch;
use App\Models\Floor;
use App\Models\Room;
use App\Models\Service;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
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
        Student::factory(20)->create();

        Service::insert([
            [
                'name' => 'Điện',
                'unit' => 'kWh',
                'unit_price' => 3500,
                'free_quota' => 0,
                'is_mandatory' => true,
            ],
            [
                'name' => 'Nước',
                'unit' => 'm³',
                'unit_price' => 18000,
                'free_quota' => 0,
                'is_mandatory' => true,
            ],
            [
                'name' => 'Internet',
                'unit' => 'Tháng',
                'unit_price' => '250000',
                'free_quota' => 0,
                'is_mandatory' => false,
            ],
        ]);

        Amenity::insert([
            [
                'name' => 'Wi-Fi miễn phí',
                'description' => 'Truy cập internet không giới hạn, tốc độ cao.',
            ],
            [
                'name' => 'Máy lạnh',
                'description' => 'Điều hòa không khí, nhiệt độ có thể điều chỉnh.',
            ],
            [
                'name' => 'Tivi thông minh',
                'description' => 'Hỗ trợ YouTube, Netflix và các ứng dụng giải trí khác.',
            ],
        ]);

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
                    $room = Room::create([
                        'floor_id' => $floor->id,
                        'room_code' => 'R' . $floorNumber . '-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                        'price_per_day' => rand(200000, 500000),
                        'price_per_month' => rand(4000000, 10000000),
                        'capacity' => rand(1, 4),
                        'current_occupancy' => 0,
                        'is_active' => true,
                        'description' => 'Phòng số ' . $i . ' trên tầng ' . $floorNumber,
                    ]);

                    $room->services()->attach([1, 2, 3]);
                }
            }
        }

        for ($i = 22; $i <= 37; $i++) {
            $roomId = rand(1, 27);
            $rentalType = rand(0, 1) ? 'daily' : 'monthly';

            if ($rentalType == 'daily') {
                $days = rand(1, 7);
                $checkInDate = Carbon::now()->addDays(rand(1, 30));
                $expectedCheckOutDate = $checkInDate->copy()->addDays($days);
            } else {
                $months = rand(1, 6);
                $checkInDate = Carbon::now()->addMonths(rand(1, 12));
                $expectedCheckOutDate = $checkInDate->copy()->addMonths($months);
            }

            Booking::create([
                'user_id' => $i,
                'room_id' => $roomId,
                'booking_type' => 'registration',
                'rental_type' => $rentalType,
                'check_in_date' => $checkInDate,
                'expected_check_out_date' => $expectedCheckOutDate,
            ]);
        }
    }
}
