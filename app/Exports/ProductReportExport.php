<?php

namespace Vanguard\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductReportExport implements FromCollection, WithHeadings
{
    protected $data;
    protected $columns;
    protected $columnCustomNames;

    public function __construct($data, $columns , $columnCustomNames)
    {
        $this->data = $data;
        $this->columns = $columns;
        $this->columnCustomNames = $columnCustomNames;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return array_map(function ($column) {
            return $this->columnCustomNames[$column] ?? strtoupper(str_replace('_', ' ', $column));
        }, $this->columns);
    }

}
