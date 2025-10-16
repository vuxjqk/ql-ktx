<?php

namespace App\Exports;

use App\Http\Controllers\DashboardController;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyRevenue implements FromCollection, WithHeadings, WithStyles
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
        $monthlyRevenue = $controller->getMonthlyRevenue($this->year);

        return collect($monthlyRevenue)->map(function ($amount, $month) {
            return [
                $month,
                $amount
            ];
        });
    }

    public function headings(): array
    {
        return [
            __('Tháng'),
            __('Doanh thu (triệu)'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
