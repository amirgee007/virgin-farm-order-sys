@extends('layouts.app')

@section('page-title', 'All Wish Lists')
@section('page-heading', 'All Wish Lists')

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">
        Submitted wish lists from customers ({{ (int) $count }} total)
    </li>
@stop

@section('content')

    @include('partials.messages')

    <div class="card mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('wishlist.manage') }}" class="form-row align-items-end">
                <div class="col-md-3 mb-2">
                    <label class="small mb-1">Search (WL-#, name, email)</label>
                    <input type="text" name="search" value="{{ (string) ($search ?? '') }}"
                           class="form-control form-control-sm" placeholder="WL-12 or customer">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="small mb-1">Customer</label>
                    <select name="user_id" class="form-control form-control-sm">
                        @foreach($users as $uid => $uname)
                            <option value="{{ $uid }}" {{ request('user_id') == $uid ? 'selected' : '' }}>
                                {{ $uname }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small mb-1">Sales Rep</label>
                    <select name="sales_rep" class="form-control form-control-sm">
                        <option value="">All</option>
                        @foreach($salesReps as $rep)
                            <option value="{{ $rep }}" {{ request('sales_rep') == $rep ? 'selected' : '' }}>
                                {{ $rep }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small mb-1">Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">All</option>
                        @foreach(['submitted', 'quoted', 'confirmed', 'closed'] as $st)
                            <option value="{{ $st }}" {{ ($status ?? '') === $st ? 'selected' : '' }}>
                                {{ ucfirst($st) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-secondary btn-sm btn-block">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-hover products-list-table">
                    <thead>
                        <tr>
                            <th>WL #</th>
                            <th>Customer</th>
                            <th>Sales Rep</th>
                            <th>Status</th>
                            <th>Items</th>
                            <th>Ship Date</th>
                            <th>Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($wishLists as $wl)
                        <tr>
                            <td class="align-middle">WL-{{ $wl->id }}</td>
                            <td class="align-middle">
                                {{ optional($wl->user)->first_name }} {{ optional($wl->user)->last_name }}<br>
                                <small class="text-muted">{{ optional($wl->user)->email }}</small>
                            </td>
                            <td class="align-middle">{{ (string) $wl->sales_rep ?: '-' }}</td>
                            <td class="align-middle">
                                @php
                                    $statusBadge = [
                                        'submitted' => 'info',
                                        'quoted'    => 'warning',
                                        'confirmed' => 'primary',
                                        'closed'    => 'success',
                                    ][$wl->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $statusBadge }}">{{ ucfirst($wl->status) }}</span>
                            </td>
                            <td class="align-middle">
                                {{ $wl->items->count() }} ({{ $wl->items->sum('quantity') }} qty)
                            </td>
                            <td class="align-middle">
                                {{ $wl->request_date ? $wl->request_date->format('Y-m-d') : '-' }}
                            </td>
                            <td class="align-middle">
                                {{ $wl->submitted_at ? $wl->submitted_at->format('Y-m-d H:i') : '-' }}
                            </td>
                            <td class="align-middle">
                                <a href="{{ route('wishlist.show', $wl->id) }}" class="btn btn-icon" title="View">
                                    <i class="fas fa-eye text-primary"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No wish lists match the filters.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {!! $wishLists->render() !!}
        </div>
    </div>

@endsection
