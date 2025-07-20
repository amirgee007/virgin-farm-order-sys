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
                        <h2>Edit Product Group</h2>

                        <form method="POST" action="{{ route('product-groups.update', $productGroup->id) }}" id="group-form">
                            @csrf
                            @method('PUT')

                            {{-- Group Name --}}
                            <div class="mb-4">
                                <label class="form-label">Group Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $productGroup->name) }}" required>
                            </div>

                            {{-- Search Section --}}
                            <div class="card mb-4 p-3">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label">Item No</label>
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

                            {{-- Product Table --}}
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
                                <tbody>
                                @foreach($productGroup->products as $index => $product)
                                    <tr class="product-row">
                                        <td>
                                            <input type="hidden" name="products[{{ $index }}][item_no]" value="{{ $product->item_no }}">
                                            {{ $product->item_no }}
                                        </td>
                                        <td>{{ $product->product_text }}</td>
                                        <td>
                                            <input type="number" name="products[{{ $index }}][stems]" value="{{ $product->pivot->stems }}" class="form-control" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger remove-row">✖</button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-success">💾 Save Group</button>
                            </div>
                        </form>

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
        let rowIndex = {{ $productGroup->products->count() }};

        $('#add-row').click(function () {
            $('#product-rows').append(`
        <tr>
            <td>
                <input type="text" name="products[${rowIndex}][item_no]" class="form-control item-input" required>
            </td>
            <td><span class="product-name text-muted">Not loaded</span></td>
            <td>
                <input type="number" name="products[${rowIndex}][stems]" class="form-control" required>
            </td>
            <td>
                <button type="button" class="btn btn-danger remove-row">✖</button>
            </td>
        </tr>
    `);
            rowIndex++;
        });

        $(document).on('click', '.remove-row', function () {
            $(this).closest('tr').remove();
        });

        $(document).on('change', '.item-input', function () {
            const row = $(this).closest('tr');
            const itemNo = $(this).val();

            if (!itemNo.trim()) return;

            $.ajax({
                url: '/api/products/by-item-no/' + itemNo,
                method: 'GET',
                success: function (res) {
                    if (res && res.product_text) {
                        row.find('.product-name').text(res.product_text).removeClass('text-muted');
                    } else {
                        row.find('.product-name').text('Not found').addClass('text-muted');
                    }
                },
                error: function () {
                    row.find('.product-name').text('Error').addClass('text-muted');
                }
            });
        });
    </script>
@stop

