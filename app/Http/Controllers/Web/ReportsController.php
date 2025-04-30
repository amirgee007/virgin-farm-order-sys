<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Vanguard\Exports\ProductReportExport;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\ProductQuantity;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function generateReport(Request $request)
    {
        $validated = $request->validate([
            'columns' => 'required|array',
            'date_in' => 'required|date',
            'date_out' => 'required|date|after_or_equal:date_in',
            'report_type' => 'required|in:pdf,excel',
            'supplier_id' => 'required',
        ]);

        $dateIn = $validated['date_in'];
        $dateOut = $validated['date_out'];
        $columns = $validated['columns'];

        $supplier = in_array($validated['supplier_id'], [1, 2]) ? $validated['supplier_id'] : null;

        $columnCustomNames = getReportColumns();

        // Add table names to the columns
        $columnsWithTableNames = array_map(function ($column) {
            if (in_array($column, ['product_text', 'item_no'])) {
                return "products.$column";
            } else {
                return "product_quantities.$column";
            }
        }, $columns);

        // Fetch data
        $query = ProductQuantity::where('quantity', '>', 0)
            ->where('date_in', '>=', $dateIn)
            ->where('date_out', '<=', $dateOut)
            ->join('products', 'products.id', '=', 'product_quantities.product_id')
            ->join('categories', 'categories.category_id', '=', 'products.category_id') // Join with categories table
            ->orderBy('products.category_id'); // Sort by category_id

        if ($supplier)
            $query->where('products.supplier_id', $supplier);

//            ->orderBy('products.product_text') // Then sort by product_text
        $data = $query->get(array_merge($columnsWithTableNames, ['categories.description as category_name', 'product_quantities.is_special'])); // Include category_name in the result

        $groupedData = $data->groupBy('category_name');

        $name = 'Inventory-Report-' . $dateIn;
        if ($validated['report_type'] === 'excel') {
            return \Excel::download(new ProductReportExport($columns, $groupedData, $columnCustomNames), "$name.xlsx");
        } else {
            #return view('products.report', compact('data', 'columns' , 'dateIn' , 'columnCustomNames'));
            $pdf = \Pdf::loadView('products.reports.report', compact('columns', 'dateIn', 'columnCustomNames', 'groupedData'));
            return $pdf->download("$name.pdf");
        }
    }

}
