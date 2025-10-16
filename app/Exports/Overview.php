<?php

namespace App\Exports;

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Overview implements FromCollection, WithHeadings, WithStyles
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
        $year = $this->year;
        $totalRevenue = $controller->getTotalRevenue($year);
        $totalBookings = $controller->getTotalBookings($year);
        $newStudents = $controller->getNewStudents($year);
        $revenueGrowth = $controller->getRevenueGrowthRate($year, $totalRevenue);
        $creator = Auth::user()->name;

        return new Collection([
            ['Người tạo', $creator],
            ['Năm', $year],
            ['Tổng doanh thu (triệu)', $totalRevenue],
            ['Tổng lượt đặt phòng', $totalBookings],
            ['Số học viên mới', $newStudents],
            ['Tăng trưởng doanh thu (%)', $revenueGrowth !== null ? $revenueGrowth . '%' : 'Không xác định'],
        ]);
    }

    public function headings(): array
    {
        return [
            __('Thông tin'),
            __('Giá trị'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
