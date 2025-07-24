{{-- resources/views/product-groups/partials/product-list.blade.php --}}

<div class="card mb-4 border shadow-sm">
    <div class="card-header bg-light py-2 px-3 border-bottom small fw-semibold">
        <i class="fa fa-rocket"></i> Group Products
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle text-center" id="product-list">
            <thead class="table-light">
            <tr>
                <th>Item No</th>
                <th>Product Name</th>
                <th>Stems</th>
                <th>Remove</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($productGroup))
                @foreach($productGroup->products as $index => $product)
                    <tr class="product-row">
                        <td>
                            <input type="hidden" name="products[{{ $index }}][item_no]" value="{{ $product->item_no }}">
                            {{ $product->item_no }}
                        </td>
                        <td>
                            <input type="text" name="products[{{ $index }}][product_text_temp]" value="{{ $product->pivot->product_text_temp }}" class="form-control" required>
                        </td>
                        <td>
                            <input type="number" name="products[{{ $index }}][stems]" value="{{ $product->pivot->stems }}" class="form-control" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-row">✖</button>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
</div>
