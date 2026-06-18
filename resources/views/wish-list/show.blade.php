@extends('layouts.app')

@php
    $canManage = in_array(myRoleName(), ['Admin', 'SalesRep']);
    $items = $wishList->items;
    $approvedCount = $items->where('approval_status', 'approved')->count();
    $rejectedCount = $items->where('approval_status', 'rejected')->count();
    $pendingCount  = $items->where('approval_status', 'pending')->count();

    $availableItems = $items->where('approval_status', 'approved');
    $customerRespondedCount = $availableItems->whereIn('customer_decision', ['accepted', 'rejected'])->count();
    $customerCanRespond = !$canManage
        && $wishList->user_id === auth()->id()
        && $availableItems->isNotEmpty()
        && in_array($wishList->status, ['quoted', 'confirmed'], true);
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
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body p-3">
                    <h6 class="text-muted mb-2">Customer</h6>
                    <p class="mb-1">
                        <strong>{{ optional($wishList->user)->first_name }} {{ optional($wishList->user)->last_name }}</strong>
                    </p>
                    <p class="mb-1 text-muted small">{{ optional($wishList->user)->email }}</p>
                    <p class="mb-0 text-muted small">Sales Rep: {{ (string) $wishList->sales_rep ?: '-' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body p-3">
                    <h6 class="text-muted mb-2">Request</h6>
                    <p class="mb-1 small">
                        <strong>Date:</strong>
                        {{ $wishList->request_date ? $wishList->request_date->format('Y-m-d') : '-' }}
                    </p>
                    <p class="mb-1 small">
                        <strong>Submitted:</strong>
                        {{ $wishList->submitted_at ? $wishList->submitted_at->format('Y-m-d H:i') : '-' }}
                    </p>
                    <p class="mb-0 small">
                        <strong>Status:</strong>
                        @php
                            $statusBadge = [
                                'submitted' => 'info',
                                'quoted'    => 'warning',
                                'confirmed' => 'primary',
                                'closed'    => 'success',
                            ][$wishList->status] ?? 'secondary';
                        @endphp
                        <span class="badge badge-{{ $statusBadge }}">{{ ucfirst($wishList->status) }}</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body p-3">
                    <h6 class="text-muted mb-2">Notes from Customer</h6>
                    <p class="mb-0 small">
                        {{ $wishList->notes ? (string) $wishList->notes : '—' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Items ({{ $items->count() }})</h5>
                <div>
                    <span class="badge badge-success">{{ $approvedCount }} available</span>
                    @if($rejectedCount > 0)
                        <span class="badge badge-danger">{{ $rejectedCount }} not available</span>
                    @endif
                    <span class="badge badge-secondary">{{ $pendingCount }} in progress</span>
                </div>
            </div>

            <form action="{{ $canManage ? route('wishlist.decisions', $wishList->id) : route('wishlist.customer.decisions', $wishList->id) }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered products-list-table align-middle {{ $canManage ? '' : 'table-sm wishlist-compact' }}">
                        <thead>
                            <tr>
                                @if($canManage)
                                    <th>Item #</th>
                                @endif
                                <th style="width:28%">Product</th>
                                <th>Qty</th>
                                <th style="width:12%">Quoted Price</th>
                                <th style="width:20%">Sales Note</th>
                                <th style="width:14%">Sales Decision</th>
                                <th style="width:14%">Customer Response</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($items as $item)
                            @php
                                $prod = $item->product;
                                $stems = $item->stems ?: optional($prod)->stems;
                                $unit  = optional(optional($prod)->stemsCount)->total;
                                if ($canManage) {
                                    $attrs = array_filter([
                                        ($item->size ?: optional($prod)->size) ? 'Size: ' . ($item->size ?: $prod->size) : null,
                                        $stems ? 'Stems: ' . $stems : null,
                                        optional($prod)->color ? 'Color: ' . $prod->color : null,
                                        $unit ? 'Unit: ' . $unit : null,
                                    ]);
                                } else {
                                    $attrs = array_filter([
                                        $stems ? 'Stems: ' . $stems : null,
                                        $unit ? 'Unit: ' . $unit : null,
                                    ]);
                                }
                            @endphp
                            <tr>
                                @if($canManage)
                                    <td class="align-middle">{{ (string) $item->item_no }}</td>
                                @endif
                                <td class="align-middle">
                                    <img src="{{ $item->image ?: asset('assets/img/no-image.png') }}"
                                         data-largeimg="{{ $item->image }}"
                                         class="img-thumbnail" width="35"
                                         onerror="this.src='{{ asset('assets/img/no-image.png') }}'">
                                    <strong>{{ (string) $item->name }}</strong>
                                    @if(!empty($attrs))
                                        <small class="text-muted ml-2">({{ implode(' | ', $attrs) }})</small>
                                    @endif
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
                                            @foreach(['pending' => 'In Progress', 'approved' => 'Available', 'rejected' => 'Not Available'] as $val => $label)
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
                                            $decisionLabel = [
                                                'approved' => 'Available',
                                                'rejected' => 'Not Available',
                                                'pending'  => 'In Progress',
                                            ][$item->approval_status] ?? ucfirst($item->approval_status);
                                        @endphp
                                        <span class="badge badge-{{ $decisionBadge }}">
                                            {{ $decisionLabel }}
                                        </span>
                                    @endif
                                </td>

                                <td class="align-middle">
                                    @php
                                        $cd = $item->customer_decision ?? 'pending';
                                        $cdBadge = [
                                            'accepted' => 'success',
                                            'rejected' => 'danger',
                                            'pending'  => 'secondary',
                                        ][$cd] ?? 'secondary';
                                        $cdLabel = [
                                            'accepted' => 'Accepted',
                                            'rejected' => 'Rejected',
                                            'pending'  => 'Waiting',
                                        ][$cd] ?? ucfirst($cd);
                                    @endphp

                                    @if($customerCanRespond && $item->approval_status === 'approved')
                                        <select name="customer_decisions[{{ $item->id }}]"
                                                class="form-control form-control-sm">
                                            <option value="pending" {{ $cd === 'pending' ? 'selected' : '' }}>— Decide —</option>
                                            <option value="accepted" {{ $cd === 'accepted' ? 'selected' : '' }}>Accept</option>
                                            <option value="rejected" {{ $cd === 'rejected' ? 'selected' : '' }}>Reject</option>
                                        </select>
                                    @elseif($item->approval_status === 'approved')
                                        <span class="badge badge-{{ $cdBadge }}">{{ $cdLabel }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canManage ? 7 : 6 }}" class="text-center text-muted">No items.</td>
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
                @elseif($customerCanRespond)
                    <div class="alert alert-info mt-3 mb-0">
                        Sales has marked {{ $availableItems->count() }} item(s) as <strong>Available</strong>.
                        Please <strong>Accept</strong> or <strong>Reject</strong> each one — sales will then confirm your order.
                    </div>
                    <div class="text-right mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i> Send My Response
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
                        @foreach(['submitted', 'quoted', 'confirmed', 'closed'] as $st)
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
                    <strong>Quoted</strong> — sales has marked items Available.
                    <strong>Confirmed</strong> — customer has accepted items; sales locks in the order.
                    <strong>Closed</strong> — fully resolved.
                </small>

                @if($availableItems->isNotEmpty())
                    <div class="mt-3">
                        <small class="text-muted">
                            Customer response progress:
                            <strong>{{ $customerRespondedCount }} / {{ $availableItems->count() }}</strong> Available items responded to.
                        </small>
                    </div>
                @endif
            </div>
        </div>
    @endif

@endsection

@section('styles')
    <style>
        .wishlist-compact th,
        .wishlist-compact td {
            padding: 0.35rem 0.5rem !important;
            font-size: 13px;
            vertical-align: middle;
        }
        .wishlist-compact .img-thumbnail {
            width: 28px;
            padding: 1px;
        }
    </style>
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
