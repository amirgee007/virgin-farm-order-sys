@extends('layouts.app')

@section('page-title', 'Wish List - Browse Products')
@section('page-heading', 'Wish List - Browse Products')

@section('styles')
    <style>
        .color-circle {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 5px;
            border: 1px solid #ccc;
            vertical-align: middle;
        }
        .MIX {
            background: conic-gradient(orange 0% 16.66%, pink 16.66% 33.33%, brown 33.33% 50%, green 50% 66.66%, blue 66.66% 83.33%, yellow 83.33% 100%);
        }
        .ASSORTED {
            background: conic-gradient(red 0% 25%, yellow 25% 50%, green 50% 75%, blue 75% 100%);
        }
    </style>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">
        Add items to your Wish List. Sales will follow up with a quote.
    </li>
@stop

@section('content')

    @include('partials.messages')

    <div class="row mb-3">
        <div class="col-md-8">
            <form method="GET" action="{{ route('wishlist.browse') }}" class="form-inline">
                <input type="text"
                       name="q"
                       value="{{ (string)($search ?? '') }}"
                       placeholder="Search by item # or product"
                       class="form-control form-control-sm mr-2"
                       style="min-width: 280px;">
                <button class="btn btn-secondary btn-sm" type="submit">
                    <i class="fas fa-search"></i> Search
                </button>
                @if(!empty($search))
                    <a href="{{ route('wishlist.browse') }}" class="btn btn-light btn-sm ml-2">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('wishlist.view') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-clipboard-list"></i>
                View Wish List ({{ (int) $wishList->countQty() }})
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-bordered products-list-table">
                    <thead>
                        <tr>
                            <th style="width:50%">Product</th>
                            <th>Color</th>
                            <th title="How many stems in a bunch UOM">Unit Pack</th>
                            <th style="width:12%">Quantity</th>
                            <th style="width:8%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="align-middle">
                                <img src="{{ $product->image_url ?: asset('assets/img/no-image.png') }}"
                                     data-largeimg="{{ $product->image_url }}"
                                     class="img-thumbnail" width="35"
                                     onerror="this.src='{{ asset('assets/img/no-image.png') }}'">
                                {{ (string) $product->product_text }}
                            </td>
                            <td class="align-middle">
                                @php
                                    $colorName = (string) ($product->color_name ?? '');
                                    $isSpecial = in_array($colorName, ['MIX', 'ASSORTED']);
                                @endphp
                                @if($colorName !== '')
                                    <span
                                        title="{{ $product->color_description ?? $colorName }}"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        class="color-circle {{ $isSpecial ? $colorName : '' }}"
                                        style="cursor: pointer; {{ !$isSpecial ? 'background-color: ' . strtolower($colorName) . ';' : '' }}">
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="align-middle">{{ is_scalar($product->stems) ? $product->stems : '-' }}</td>
                            <form action="{{ route('wishlist.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <td class="align-middle">
                                    <input required name="quantity" type="number" min="1"
                                           value="1"
                                           class="form-control form-control-sm"
                                           style="width: 80px;">
                                </td>
                                <td class="align-middle">
                                    <button type="submit" class="btn btn-icon" title="Add to Wish List"
                                            data-toggle="tooltip" data-placement="left">
                                        <i class="fas fa-plus-circle text-success"></i>
                                    </button>
                                </td>
                            </form>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No products found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {!! $products->render() !!}
        </div>
    </div>

@endsection
