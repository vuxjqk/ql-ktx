<?php

namespace App\Exports;

use App\Http\Controllers\DashboardController;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BookingsByBranch implements FromCollection, WithHeadings, WithStyles
{
    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $controller = new DashboardController();
        $bookingsByBranch = $controller->getBookingsByBranch($this->year);

        return collect($bookingsByBranch)->map(function ($total_bookings, $branch_name) {
            return [
                $branch_name,
                $total_bookings
            ];
        });
    }

    public function headings(): array
    {
        return [
            __('Chi nhánh'),
            __('Số lượng đặt phòng'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
