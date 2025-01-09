<?php

namespace Vanguard\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductReportExport implements FromView
{
    protected $groupedData;
    protected $columns;
    protected $columnCustomNames;

    public function __construct($columns, $groupedData , $columnCustomNames)
    {
        $this->columns = $columns;
        $this->groupedData = $groupedData;
        $this->columnCustomNames = $columnCustomNames;
    }

    public function view(): View
    {
        return view('products.reports.__report-table', [
            'columns' => $this->columns,
            'groupedData' => $this->groupedData,
            'columnCustomNames' => $this->columnCustomNames,
        ]);
    }
}
