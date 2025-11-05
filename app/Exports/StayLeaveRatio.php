<?php

namespace App\Exports;

use App\Http\Controllers\DashboardController;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StayLeaveRatio implements FromCollection, WithHeadings, WithStyles
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
        $stayLeaveRatio = $controller->getStayLeaveRatio($this->year);

        return collect($stayLeaveRatio)->map(function ($quantity, $label) {
            return [
                $label,
                $quantity
            ];
        });
    }

    public function headings(): array
    {
        return [
            __('Loại'),
            __('Số lượng'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
