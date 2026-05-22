<table class="table table-borderless table-striped vf-reportings-table">
    <thead>
        <tr>
            <th>#</th>
            <th class="min-width-120">@lang('Item No')</th>
            <th class="min-width-180">Product</th>
            <th>@lang('Supplier')</th>
            <th>@lang('Category')</th>
            <th>@lang('Sales Rep')</th>
            <th>@lang('UOM')</th>
            <th>@lang('Stems')</th>
            <th>@lang('Size')</th>
            <th>@lang('Weight')</th>
            <th>@lang('Orders')</th>
            <th>@lang('Units Sold')</th>
            <th>@lang('Stems Sold')</th>
            <th>@lang('Avg Price')</th>
            <th>@lang('Total Sales')</th>
        </tr>
    </thead>
    <tbody>
        @forelse($reportItems as $index => $item)
            <tr>
                <td>
                    @if(method_exists($reportItems, 'firstItem'))
                        {{ $reportItems->firstItem() + $index }}
                    @else
                        {{ $index + 1 }}
                    @endif
                </td>
                <td>{{ $item->item_no }}</td>
                <td>{{ $item->product_text }}</td>
                <td>{{ $suppliers[$item->supplier_id] ?? 'Unknown' }}</td>
                <td>{{ $item->category_name ?? 'N/A' }}</td>
                <td>{{ $filters['salesRep'] ?: ($item->sales_reps ?: 'N/A') }}</td>
                <td>{{ $item->unit_of_measure ?: 'N/A' }}</td>
                <td>{{ $item->stems ?: 0 }}</td>
                <td>{{ $item->size ?: 0 }}</td>
                <td>{{ $item->weight ?: 0 }}</td>
                <td>{{ $item->order_count }}</td>
                <td>{{ $item->total_quantity }}</td>
                <td>{{ $item->total_stems }}</td>
                <td>${{ round2Digit($item->average_price) }}</td>
                <td>${{ round2Digit($item->total_sales) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="15">
                    @lang('No sold items found for the selected filters.')
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
