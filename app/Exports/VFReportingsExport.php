<?php

namespace Vanguard\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VFReportingsExport implements FromView
{
    public function __construct(
        protected $reportItems,
        protected array $filters,
        protected array $suppliers
    ) {
    }

    public function view(): View
    {
        return view('products.reports._vf-reportings-table', [
            'reportItems' => $this->reportItems,
            'filters' => $this->filters,
            'suppliers' => $this->suppliers,
            'isExport' => true,
        ]);
    }
}
