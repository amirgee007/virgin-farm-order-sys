<?php

namespace Vanguard\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductReportExport implements FromCollection, WithHeadings
{
    protected $data;
    protected $columns;

    public function __construct($data, $columns)
    {
        $this->data = $data;
        $this->columns = $columns;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return array_map(function ($column) {
            return strtoupper(str_replace('_', ' ', $column));
        }, $this->columns);
    }
}
