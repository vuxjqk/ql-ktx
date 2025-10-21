<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class UpdateExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-expired-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now()->startOfDay();

        $bookings = Booking::where('status', 'active')
            ->whereDate('expected_check_out_date', '<=', $now)
            ->get();

        $count = 0;

        foreach ($bookings as $booking) {
            if ($booking->expire()) {
                $count++;
            }
        }

        $this->info(__("{$count} hợp đồng đã hết hạn trong hôm nay"));

        return 0;
    }
}
