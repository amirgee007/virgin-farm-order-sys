@extends('layouts.app')

@section('page-title', __('Product Groups'))
@section('page-heading', __('Products Groups'))

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">@lang('Manage Groups')</li>
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

                            @php
                                $selectedProduct = $products->firstWhere('id', old('parent_product_id', $productGroup->parent_product_id));
                            @endphp

                            <div class="mb-4">
                                <label class="form-label">Parent Product</label>
                                @if($selectedProduct)
                                    <select class="form-control" disabled>
                                        <option>{{ $selectedProduct->item_no }} - {{ $selectedProduct->product_text }}</option>
                                    </select>
                                    <input type="hidden" name="parent_product_id" value="{{ $selectedProduct->id }}">
                                @else
                                    <div class="text-danger">Invalid parent product selected.</div>
                                @endif
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Group Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $productGroup->name) }}" required>
                            </div>

                            {{-- SEARCH --}}
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
                                <tbody>
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
                                </tbody>
                            </table>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">💾 Save Group</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script src="{{ url('assets/js/product-group-form.js') }}"></script>
    @include('partials.toaster-js')
@stop
