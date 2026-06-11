<?php

namespace Vanguard\Http\Controllers\Web;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Vanguard\Exports\ProductReportExport;
use Vanguard\Exports\VFReportingsExport;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\ProductQuantity;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function vfReportings(Request $request)
    {
        $search = trim((string)$request->get('search', ''));
        $period = $request->get('period', 'monthly');
        $salesRep = $request->get('sales_rep');
        $sort = $request->get('sort', 'most_sold');
        $export = $request->get('export');

        [$dateIn, $dateOut] = $this->resolveReportingDateRange(
            $period,
            $request->get('date_in'),
            $request->get('date_out')
        );

        $query = $this->soldItemsReportQuery($search, $salesRep, $dateIn, $dateOut, $sort);

        $totalOrders = DB::query()
            ->fromSub(clone $query, 'report_items')
            ->sum('order_count');

        $totalSales = DB::query()
            ->fromSub(clone $query, 'report_items')
            ->sum('total_sales');

        $filters = [
            'search' => $search,
            'period' => $period,
            'salesRep' => $salesRep,
            'sort' => $sort,
            'dateIn' => $dateIn,
            'dateOut' => $dateOut,
        ];

        $suppliers = config('vfsuppliers');
        $salesReps = getSalesReps();
        unset($salesReps[0]);

        if (in_array($export, ['pdf', 'excel'], true)) {

            $reportItems = $query->get();
            $fileName = 'VF-Reportings-' . $dateIn . '-to-' . $dateOut;

            if ($export === 'excel') {
                return \Excel::download(
                    new VFReportingsExport($reportItems, $filters, $suppliers),
                    "{$fileName}.xlsx"
                );
            }

            $pdf = \Pdf::loadView('products.reports.vf-reportings-pdf', compact(
                'reportItems',
                'filters',
                'suppliers',
                'salesReps',
                'totalOrders',
                'totalSales'
            ))->setPaper('a4', 'landscape');

            return $pdf->download("{$fileName}.pdf");
        }

        $reportItems = $query->paginate(100);

        $reportItems->appends([
            'search' => $search,
            'period' => $period,
            'sales_rep' => $salesRep,
            'sort' => $sort,
            'date_in' => $dateIn,
            'date_out' => $dateOut,
        ]);

        $periods = [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
            'custom' => 'Custom Range',
        ];

        $sortOptions = [
            'most_sold' => 'Most Sold',
            'least_sold' => 'Least Sold',
        ];

        return view('products.reports.vf-reportings', compact(
            'reportItems',
            'filters',
            'suppliers',
            'salesReps',
            'periods',
            'sortOptions',
            'totalOrders',
            'totalSales'
        ));
    }

    private function soldItemsReportQuery($search, $salesRep, $dateIn, $dateOut, $sort)
    {
        $query = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('categories', 'categories.category_id', '=', 'products.category_id')
            ->whereDate('orders.date_shipped', '>=', $dateIn)
            ->whereDate('orders.date_shipped', '<=', $dateOut)
            ->select([
                'order_items.item_no',
                DB::raw('COALESCE(products.product_text, order_items.name) as product_text'),
                'products.supplier_id',
                'categories.description as category_name',
                'products.unit_of_measure',
                'order_items.stems',
                'products.size',
                'products.weight',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * COALESCE(order_items.stems, 1)) as total_stems'),
                DB::raw('SUM(COALESCE(order_items.sub_total, order_items.price * order_items.quantity * COALESCE(order_items.stems, 1), 0)) as total_sales'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('AVG(order_items.price) as average_price'),
                DB::raw("GROUP_CONCAT(DISTINCT orders.sales_rep ORDER BY orders.sales_rep SEPARATOR ', ') as sales_reps"),
            ])
            ->groupBy(
                'order_items.item_no',
                'products.product_text',
                'order_items.name',
                'products.supplier_id',
                'categories.description',
                'products.unit_of_measure',
                'order_items.stems',
                'products.size',
                'products.weight'
            );

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_items.item_no', 'like', "%{$search}%")
                    ->orWhere('order_items.name', 'like', "%{$search}%")
                    ->orWhere('products.product_text', 'like', "%{$search}%")
                    ->orWhere('categories.description', 'like', "%{$search}%");
            });
        }

        if ($salesRep && $salesRep !== '0') {
            $query->where('orders.sales_rep', $salesRep);
        }

        if ($sort === 'least_sold') {
            return $query->orderBy('total_quantity')->orderBy('product_text');
        }

        return $query->orderByDesc('total_quantity')->orderBy('product_text');
    }

    private function resolveReportingDateRange($period, $dateIn = null, $dateOut = null)
    {
        $now = Carbon::now();

        if ($period === 'custom') {
            return [
                $dateIn ? Carbon::parse($dateIn)->toDateString() : $now->copy()->startOfMonth()->toDateString(),
                $dateOut ? Carbon::parse($dateOut)->toDateString() : $now->copy()->endOfMonth()->toDateString(),
            ];
        }

        return match ($period) {
            'daily' => [$now->toDateString(), $now->toDateString()],
            'weekly' => [$now->copy()->startOfWeek()->toDateString(), $now->copy()->endOfWeek()->toDateString()],
            'quarterly' => [$now->copy()->startOfQuarter()->toDateString(), $now->copy()->endOfQuarter()->toDateString()],
            'yearly' => [$now->copy()->startOfYear()->toDateString(), $now->copy()->endOfYear()->toDateString()],
            default => [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()],
        };
    }

    public function generateProductsReport(Request $request)
    {
        #This is from products page Reports.
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

        $totalOrders = (clone $query)
            ->distinct()
            ->count('product_quantities.id');

//        $totalOrders = DB::query()
//            ->fromSub(clone $query, 'report_items')
//            ->count();
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
