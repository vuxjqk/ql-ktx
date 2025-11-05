<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StatisticsExport implements WithMultipleSheets
{
    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function sheets(): array
    {
        return [
            new Overview($this->year),
            new MonthlyRevenue($this->year),
            new BookingsByBranch($this->year),
            new StayLeaveRatio($this->year),
        ];
    }
}
