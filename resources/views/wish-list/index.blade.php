@extends('layouts.app')

@section('page-title', 'Wish List History')
@section('page-heading', 'Wish List History')

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">All your wish lists</li>
@stop

@section('content')

    @include('partials.messages')

    <div class="row mb-3">
        <div class="col-md-12 text-right">
            <a href="{{ route('wishlist.view') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-clipboard-list"></i> Current Wish List
            </a>
            <a href="{{ route('wishlist.browse') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Browse Products
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-bordered products-list-table">
                    <thead>
                        <tr>
                            <th>WL #</th>
                            <th>Status</th>
                            <th>Items</th>
                            <th>Ship Date</th>
                            <th>Submitted</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($wishLists as $wl)
                        <tr>
                            <td class="align-middle">WL-{{ $wl->id }}</td>
                            <td class="align-middle">
                                @php
                                    $statusBadge = [
                                        'draft'     => 'secondary',
                                        'submitted' => 'info',
                                        'quoted'    => 'warning',
                                        'closed'    => 'success',
                                    ][$wl->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $statusBadge }}">{{ ucfirst($wl->status) }}</span>
                            </td>
                            <td class="align-middle">
                                {{ $wl->items->count() }} ({{ $wl->items->sum('quantity') }} qty)
                            </td>
                            <td class="align-middle">
                                {{ $wl->ship_date ? $wl->ship_date->format('Y-m-d') : '-' }}
                            </td>
                            <td class="align-middle">
                                {{ $wl->submitted_at ? $wl->submitted_at->format('Y-m-d H:i') : '-' }}
                            </td>
                            <td class="align-middle">{{ \Illuminate\Support\Str::limit((string) $wl->notes, 80) }}</td>
                            <td class="align-middle">
                                @if($wl->status !== 'draft')
                                    <a href="{{ route('wishlist.show', $wl->id) }}" class="btn btn-icon" title="View">
                                        <i class="fas fa-eye text-primary"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No wish lists yet.
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
