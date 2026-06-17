@extends('layouts.app')

@section('page-title', 'Wish List WL-' . $wishList->id)
@section('page-heading', 'Wish List WL-' . $wishList->id)

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('wishlist.manage') }}">All Wish Lists</a>
    </li>
    <li class="breadcrumb-item text-muted">WL-{{ $wishList->id }}</li>
@stop

@section('content')

    @include('partials.messages')

    <div class="row mb-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body p-3">
                    <h5>Customer</h5>
                    <p class="mb-1">
                        <strong>{{ optional($wishList->user)->first_name }} {{ optional($wishList->user)->last_name }}</strong>
                    </p>
                    <p class="mb-1 text-muted">{{ optional($wishList->user)->email }}</p>
                    <p class="mb-0 text-muted">Sales Rep: {{ (string) $wishList->sales_rep ?: '-' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body p-3">
                    <h5>Request</h5>
                    <p class="mb-1">
                        <strong>Date:</strong>
                        {{ $wishList->request_date ? $wishList->request_date->format('Y-m-d') : '-' }}
                    </p>
                    <p class="mb-1">
                        <strong>Submitted:</strong>
                        {{ $wishList->submitted_at ? $wishList->submitted_at->format('Y-m-d H:i') : '-' }}
                    </p>
                    <p class="mb-0">
                        <strong>Status:</strong>
                        @php
                            $statusBadge = [
                                'submitted' => 'info',
                                'quoted'    => 'warning',
                                'closed'    => 'success',
                            ][$wishList->status] ?? 'secondary';
                        @endphp
                        <span class="badge badge-{{ $statusBadge }}">{{ ucfirst($wishList->status) }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if($wishList->notes)
        <div class="card mb-3">
            <div class="card-body p-3">
                <h6>Notes from Customer</h6>
                <p class="mb-0">{{ (string) $wishList->notes }}</p>
            </div>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body p-3">
            <h5 class="mb-3">Items ({{ $wishList->items->count() }})</h5>
            <div class="table-responsive">
                <table class="table table-bordered products-list-table">
                    <thead>
                        <tr>
                            <th>Item #</th>
                            <th style="width:60%">Product</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($wishList->items as $item)
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
                            <td class="align-middle">{{ (int) $item->quantity }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No items.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-3">
            <h6>Update Status</h6>
            <form action="{{ route('wishlist.status', $wishList->id) }}" method="POST" class="form-inline">
                @csrf
                <select name="status" class="form-control form-control-sm mr-2">
                    @foreach(['submitted', 'quoted', 'closed'] as $st)
                        <option value="{{ $st }}" {{ $wishList->status === $st ? 'selected' : '' }}>
                            {{ ucfirst($st) }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-save"></i> Save
                </button>
            </form>
            <small class="text-muted d-block mt-2">
                <strong>Quoted</strong> = sales sent a quote. <strong>Closed</strong> = wish list resolved or cancelled.
            </small>
        </div>
    </div>

@endsection
