@extends('layouts.app')

@section('page-title', 'My Wish List')
@section('page-heading', 'My Wish List')

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">
        Review items, set a request date, and submit. Sales will follow up with a quote.
    </li>
@stop

@section('content')

    @include('partials.messages')

    <div class="row mb-3">
        <div class="col-md-6">
            <a href="{{ route('wishlist.browse') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-plus"></i> Add More Items
            </a>
            <a href="{{ route('wishlist.history') }}" class="btn btn-light btn-sm">
                <i class="fas fa-history"></i> History
            </a>
        </div>
        <div class="col-md-6 text-right">
            @if($items->isNotEmpty())
                <a href="{{ route('wishlist.empty') }}"
                   class="btn btn-light btn-sm"
                   data-method="GET"
                   data-confirm-title="Please Confirm"
                   data-confirm-text="Empty your wish list?"
                   data-confirm-delete="Yes, empty">
                    <i class="fas fa-trash"></i> Empty
                </a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-bordered products-list-table">
                    <thead>
                        <tr>
                            <th>Item #</th>
                            <th style="width:50%">Product</th>
                            <th style="width:14%">Quantity</th>
                            <th style="width:10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td class="align-middle">{{ (string) $item->item_no }}</td>
                            <td class="align-middle">
                                @if($item->image && is_string($item->image))
                                    <img src="{{ asset('assets/img/no-image.png') }}"
                                         data-largeimg="{{ $item->image }}"
                                         class="img-thumbnail" width="35">
                                @endif
                                {{ (string) $item->name }}
                            </td>
                            <td class="align-middle">
                                <form action="{{ route('wishlist.qty') }}" method="POST" class="form-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    <input type="number" name="quantity" min="1"
                                           value="{{ (int) $item->quantity }}"
                                           class="form-control form-control-sm mr-2"
                                           style="width: 80px;">
                                    <button type="submit" class="btn btn-icon" title="Update">
                                        <i class="fas fa-sync text-primary"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="align-middle">
                                <form action="{{ route('wishlist.remove') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    <button type="submit" class="btn btn-icon" title="Remove">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Your wish list is empty.
                                <a href="{{ route('wishlist.browse') }}">Browse products</a>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($items->isNotEmpty())
                <hr>
                <form action="{{ route('wishlist.submit') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <label for="request_date">Request Date <span class="text-danger">*</span></label>
                            <input type="date"
                                   id="request_date"
                                   name="request_date"
                                   required
                                   min="{{ now()->toDateString() }}"
                                   value="{{ optional($wishList->request_date)->toDateString() }}"
                                   class="form-control form-control-sm">
                            <small class="text-muted">When would you like these items?</small>
                        </div>
                        <div class="col-md-8">
                            <label for="notes">Notes for Sales</label>
                            <textarea name="notes"
                                      id="notes"
                                      rows="2"
                                      maxlength="2000"
                                      class="form-control form-control-sm"
                                      placeholder="Any additional notes...">{{ (string) $wishList->notes }}</textarea>
                        </div>
                    </div>

                    <div class="text-right mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i> Submit Wish List
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

@endsection
