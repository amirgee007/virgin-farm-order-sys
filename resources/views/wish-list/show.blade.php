@extends('layouts.app')

@php
    $canManage = in_array(myRoleName(), ['Admin', 'SalesRep']);
    $items = $wishList->items;
    $approvedCount = $items->where('approval_status', 'approved')->count();
    $rejectedCount = $items->where('approval_status', 'rejected')->count();
    $pendingCount  = $items->where('approval_status', 'pending')->count();
@endphp

@section('page-title', 'Wish List WL-' . $wishList->id)
@section('page-heading', 'Wish List WL-' . $wishList->id)

@section('breadcrumbs')
    @if($canManage)
        <li class="breadcrumb-item">
            <a href="{{ route('wishlist.manage') }}">All Wish Lists</a>
        </li>
    @else
        <li class="breadcrumb-item">
            <a href="{{ route('wishlist.history') }}">My Wish Lists</a>
        </li>
    @endif
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
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Items ({{ $items->count() }})</h5>
                <div>
                    <span class="badge badge-success">{{ $approvedCount }} approved</span>
                    <span class="badge badge-danger">{{ $rejectedCount }} rejected</span>
                    <span class="badge badge-secondary">{{ $pendingCount }} pending</span>
                </div>
            </div>

            <form action="{{ route('wishlist.decisions', $wishList->id) }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered products-list-table align-middle">
                        <thead>
                            <tr>
                                <th>Item #</th>
                                <th style="width:30%">Product</th>
                                <th>Qty</th>
                                <th style="width:12%">Quoted Price</th>
                                <th style="width:25%">Sales Note</th>
                                <th style="width:18%">Decision</th>
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
                                <td class="align-middle">{{ (int) $item->quantity }}</td>

                                <td class="align-middle">
                                    @if($canManage)
                                        @php
                                            $prod = $item->product;
                                            $popover = '';
                                            if ($prod) {
                                                $fmt = fn ($v) => $v !== null ? '$'.number_format((float) $v, 2) : '-';
                                                $popover = '<table class="table table-sm mb-0">'
                                                    . '<tr><td>FedEx</td><td class="text-right">'.$fmt($prod->def_price_fedex).'</td></tr>'
                                                    . '<tr><td>FOB</td><td class="text-right">'.$fmt($prod->def_price_fob).'</td></tr>'
                                                    . '<tr><td>Hawaii</td><td class="text-right">'.$fmt($prod->def_price_hawaii).'</td></tr>'
                                                    . '<tr><td>FedEx+</td><td class="text-right">'.$fmt($prod->def_price_fedex_2).'</td></tr>'
                                                    . '</table>';
                                            }
                                        @endphp
                                        <div class="input-group input-group-sm">
                                            <input type="number"
                                                   name="decisions[{{ $item->id }}][quoted_price]"
                                                   value="{{ $item->quoted_price !== null ? $item->quoted_price : '' }}"
                                                   step="0.01" min="0"
                                                   placeholder="$"
                                                   class="form-control form-control-sm">
                                            @if($popover)
                                                <div class="input-group-append">
                                                    <button type="button"
                                                            class="btn btn-light btn-sm wishlist-price-popover"
                                                            data-toggle="popover"
                                                            data-trigger="focus"
                                                            data-html="true"
                                                            data-placement="left"
                                                            title="Default Prices"
                                                            data-content="{{ $popover }}">
                                                        <i class="fas fa-eye text-primary"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        {{ $item->quoted_price !== null ? '$'.number_format($item->quoted_price, 2) : '-' }}
                                    @endif
                                </td>

                                <td class="align-middle">
                                    @if($canManage)
                                        <input type="text"
                                               name="decisions[{{ $item->id }}][admin_note]"
                                               value="{{ (string) $item->admin_note }}"
                                               maxlength="500"
                                               class="form-control form-control-sm"
                                               placeholder="Optional note...">
                                    @else
                                        {{ (string) $item->admin_note ?: '-' }}
                                    @endif
                                </td>

                                <td class="align-middle">
                                    @if($canManage)
                                        <select name="decisions[{{ $item->id }}][approval_status]"
                                                class="form-control form-control-sm">
                                            @foreach(['pending' => 'Pending', 'approved' => 'Approve', 'rejected' => 'Reject'] as $val => $label)
                                                <option value="{{ $val }}" {{ $item->approval_status === $val ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        @php
                                            $decisionBadge = [
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'pending'  => 'secondary',
                                            ][$item->approval_status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $decisionBadge }}">
                                            {{ ucfirst($item->approval_status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No items.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($canManage && $items->isNotEmpty())
                    <div class="text-right mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Decisions
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>

    @if($canManage)
        <div class="card">
            <div class="card-body p-3">
                <h6>Overall Status</h6>
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
                    Status auto-updates to <strong>Quoted</strong> after any decision is saved. Set to <strong>Closed</strong> when the wish list is fully resolved.
                </small>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    <script>
        $(function () {
            $('[data-toggle="popover"].wishlist-price-popover').popover({
                container: 'body',
                html: true,
            });
        });
    </script>
@endsection
