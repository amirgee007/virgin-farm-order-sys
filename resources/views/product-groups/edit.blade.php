@extends('layouts.app')

@section('page-title', __('Edit Product Group'))
@section('page-heading', __('Edit Product Group'))

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">@lang('Manage Groups')</li>
@stop

@section('styles')
    <style>
        #product-suggestions .list-group-item {
            background-color: #f9f9f9;
            border: none;
            padding: 0.5rem 0.75rem;
            cursor: pointer;
        }

        #product-suggestions .list-group-item:hover {
            background-color: #e9ecef;
        }

        #product-suggestions {
            max-height: 250px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-top: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }

        .product-row-added {
            animation: fadeIn 0.5s ease-in-out;
            background-color: #f8f9fa;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
@stop

@section('content')
    @include('partials.messages')

    <form action="{{ route('product-groups.update', $productGroup->id) }}" method="POST" id="group-form">
        @csrf
        @method('PUT')

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <h5 class="card-title">@lang('Edit Product Group')</h5>
                        <p class="text-muted small">
                            @lang('Modify the combo group and its associated products.')
                        </p>
                    </div>

                    <div class="col-md-9">
                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Parent Product</label>
                                @php
                                    $selectedProduct = $products->firstWhere('id', old('parent_product_id', $productGroup->parent_product_id));
                                @endphp

                                @if($selectedProduct)
                                    <select class="form-control" disabled>
                                        <option>{{ $selectedProduct->item_no }} - {{ $selectedProduct->product_text }}</option>
                                    </select>
                                    <input type="hidden" name="parent_product_id" value="{{ $selectedProduct->id }}">
                                @else
                                    <div class="text-danger">Invalid parent product selected.</div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Group Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $productGroup->name) }}" required>
                            </div>
                        </div>

                        {{-- Product Search Section --}}
                        <div class="card mb-4 border shadow-sm">
                            <div class="card-header bg-light py-2 px-3 border-bottom small fw-semibold">
                                <i class="fa fa-magic"></i> Search/Add Products to Group
                            </div>

                            <div class="card-body">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-4 position-relative">
                                        <label class="form-label">Search Product</label>
                                        <input type="text" id="search_product" class="form-control" placeholder="Item No or Name" autocomplete="off">
                                        <div id="product-suggestions" class="list-group position-absolute shadow-sm bg-white border rounded mt-1" style="z-index: 1000; max-height: 250px; overflow-y: auto;"></div>
                                        <input type="hidden" id="product_id">
                                    </div>

                                    <div class="col-md-5">
                                        <label class="form-label">Product Name</label>
                                        <input type="text" id="product_name" class="form-control" readonly>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label">Stems</label>
                                        <input type="number" id="stems" class="form-control" min="1" placeholder="e.g. 5">
                                    </div>

                                    <div class="col-md-1">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-primary" id="add-product">
                                            <i class="fa fa-plus-circle"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mt-2 text-center">
                                    <b id="search-status" class="text-muted d-block fst-italic">Waiting for input...</b>
                                </div>
                            </div>
                        </div>

                        {{-- Product List --}}
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
                                    @foreach($productGroup->products as $index => $product)
                                        <tr class="product-row">
                                            <td>
                                                <input type="hidden" name="products[{{ $index }}][id]" value="{{ $product->id }}">
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
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary float-right">
                                <i class="fa fa-save"></i> Save Group
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('scripts')
    <script src="{{ url('assets/js/product-group-form.js') }}"></script>
    @include('partials.toaster-js')
@stop
