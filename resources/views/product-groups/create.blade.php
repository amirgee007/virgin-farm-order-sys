@extends('layouts.app')

@section('page-title', __('Product Groups'))
@section('page-heading', __('Products Groups'))

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">
        @lang('Manage Groups')
    </li>
@stop

@section('content')
    @include('partials.messages')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body mt-0 p-3">
                    <div class="container">
                        <div class="container">
                            <h2>Create Product Group</h2>

                            <form action="{{ route('product-groups.store') }}" method="POST" id="group-form">
                                @csrf

                                <div class="mb-4">
                                    <label class="form-label">Parent Product</label>
                                    <select name="parent_product_id" class="form-control" required>
                                        <option value="">-- None --</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ old('parent_product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->item_no }} - {{ $product->product_text }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- GROUP NAME --}}
                                <div class="mb-4">
                                    <label for="group-name" class="form-label">Group Name</label>
                                    <input type="text" name="name" id="group-name" class="form-control" required>
                                </div>

                                {{-- SEARCH SECTION --}}
                                <div class="card mb-4 p-3">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-3">
                                            <label for="item_no" class="form-label">Item No</label>
                                            <input type="text" id="item_no" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Product Name</label>
                                            <input type="text" id="product_name" class="form-control" readonly>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Stems</label>
                                            <input type="number" id="stems" class="form-control" min="1">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label d-block">&nbsp;</label>
                                            <button type="button" class="btn btn-primary w-100" id="add-product">Add Product</button>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label d-block">&nbsp;</label>
                                            <span id="search-status" class="text-muted small">Waiting for input...</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- PRODUCT LIST --}}
                                <h5>Group Products</h5>
                                <table class="table table-bordered" id="product-list">
                                    <thead>
                                    <tr>
                                        <th>Item No</th>
                                        <th>Name</th>
                                        <th>Stems</th>
                                        <th>Remove</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                                {{-- SUBMIT --}}
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-success">💾 Save Group</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop


@section('scripts')
    <script src="{{ asset('js/product-group-form.js') }}"></script>

    @include('partials.toaster-js')
    <script>
        let productIndex = 0;
        let addedItemNos = new Set();

        // Search when item_no input loses focus or changes
        // Auto search when item_no is typed
        $('#item_no').on('input', function () {
            const itemNo = $(this).val().trim();

            if (!itemNo) {
                $('#product_name').val('');
                $('#search-status').text('Waiting for input...');
                return;
            }

            $.get('/api/products/by-item-no/' + itemNo, function (data) {
                if (data && data.product_text) {
                    $('#product_name').val(data.product_text);
                    $('#search-status').text('Product found');

                    // Automatically move focus to the stems input
                    $('#stems').focus();
                } else {
                    $('#product_name').val('');
                    $('#search-status').text('Not found');
                }
            }).fail(() => {
                $('#product_name').val('');
                $('#search-status').text('Error or not found');
            });
        });


        $('#add-product').click(function () {
            const itemNo = $('#item_no').val().trim();
            const name = $('#product_name').val().trim();
            const stems = $('#stems').val().trim();

            if (!itemNo || !name || !stems) {
                alert('Please enter a valid item number and stem count.');
                return;
            }

            if (addedItemNos.has(itemNo)) {
                alert('Product already added.');
                return;
            }

            const row = `
        <tr>
            <td>
                <input type="hidden" name="products[${productIndex}][item_no]" value="${itemNo}">
                ${itemNo}
            </td>
            <td>${name}</td>
            <td>
                <input type="number" name="products[${productIndex}][stems]" value="${stems}" class="form-control" required>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-row">✖</button>
            </td>
        </tr>
    `;

            $('#product-list tbody').append(row);
            addedItemNos.add(itemNo);
            productIndex++;

            // ✅ Reset form fields
            $('#item_no').val('').focus();
            $('#product_name').val('');
            $('#stems').val('');
            $('#search-status').text('Waiting for input...');
        });

        // Remove row
        $(document).on('click', '.remove-row', function () {
            const itemNo = $(this).closest('tr').find('input[type=hidden]').val();
            addedItemNos.delete(itemNo);
            $(this).closest('tr').remove();
        });

        $('#group-form').on('keydown', function (e) {
            if (e.key === 'Enter' && !$(e.target).is('textarea') && !$(e.target).is('button')) {
                e.preventDefault();
                return false;
            }
        });
    </script>
@stop

