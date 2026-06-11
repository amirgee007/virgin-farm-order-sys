<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VF Reportings</title>
    <style>
        @page {
            margin: 12px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #111;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 0.5px solid #222;
            padding: 3px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .header-table td {
            border: 0;
        }

        .logo {
            width: 220px;
        }

        h3 {
            margin: 0 0 6px 0;
            font-size: 15px;
        }

        .meta {
            margin: 0 0 10px 0;
            font-size: 10px;
        }
        .summary-box {
            margin: 10px 0;
            padding: 6px 8px;
            border: 1px solid #222;
            font-size: 10px;
            background: #f8f8f8;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 35%;">
                <img class="logo" src="{{ public_path('VFLogo.png') }}" alt="Virgin Farms Logo">
            </td>
            <td style="width: 65%; text-align: right;">
                <h3>VF Sold Items Reporting</h3>
                <p class="meta">
                    {{ dateFormatMy($filters['dateIn']) }} - {{ dateFormatMy($filters['dateOut']) }}<br>
                    Sales Rep: {{ $filters['salesRep'] ?: 'All' }}<br>
                    Sort: {{ $filters['sort'] === 'least_sold' ? 'Least Sold' : 'Most Sold' }}
                </p>
            </td>
        </tr>
    </table>

    {{-- Summary --}}
    <div class="summary-box">
        Total Orders: <strong>{{ $totalOrders ?? $reportItems->sum('order_count') }}</strong>
        &nbsp; | &nbsp;
        Total Sales:
        <strong>${{ number_format($totalSales ?? $reportItems->sum('total_sales'), 2) }}</strong>
    </div>

    @include('products.reports._vf-reportings-table', [
        'reportItems' => $reportItems,
        'filters' => $filters,
        'suppliers' => $suppliers,
        'isExport' => true,
    ])
</body>
</html>
