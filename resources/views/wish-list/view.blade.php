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
                            <th style="width:55%">Product</th>
                            <th style="width:18%">Quantity</th>
                            <th style="width:10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($items as $item)
                        @php
                            $prod = $item->product;
                            $stems = $item->stems ?: optional($prod)->stems;
                            $unit  = optional(optional($prod)->stemsCount)->total;
                            $inline = array_filter([
                                $stems ? 'Stems: ' . $stems : null,
                                $unit ? 'Unit: ' . $unit : null,
                            ]);
                        @endphp
                        <tr>
                            <td class="align-middle">
                                @if($item->image && is_string($item->image))
                                    <img src="{{ asset('assets/img/no-image.png') }}"
                                         data-largeimg="{{ $item->image }}"
                                         class="img-thumbnail" width="35">
                                @endif
                                <strong>{{ (string) $item->name }}</strong>
                                @if(!empty($inline))
                                    <small class="text-muted ml-2">({{ implode(' | ', $inline) }})</small>
                                @endif
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
                                <form action="{{ route('wishlist.remove') }}" method="POST"
                                      onsubmit="return confirm('Remove this item from your wish list?');">
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
                            <td colspan="3" class="text-center text-muted py-4">
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

    @if(isset($pastWishLists) && $pastWishLists->count())
        <div class="card mt-3">
            <div class="card-body p-3">
                <h5 class="mb-3">Past Wish Lists</h5>
                <div class="table-responsive">
                    <table class="table table-bordered products-list-table">
                        <thead>
                            <tr>
                                <th>WL #</th>
                                <th>Status</th>
                                <th>Items</th>
                                <th>Request Date</th>
                                <th>Submitted</th>
                                <th>Notes</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($pastWishLists as $wl)
                            <tr>
                                <td class="align-middle">WL-{{ $wl->id }}</td>
                                <td class="align-middle">
                                    @php
                                        $statusBadge = [
                                            'draft'     => 'secondary',
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
                                <td class="align-middle">{{ \Illuminate\Support\Str::limit((string) $wl->notes, 80) }}</td>
                                <td class="align-middle">
                                    @if($wl->status !== 'draft')
                                        <a href="{{ route('wishlist.show', $wl->id) }}" class="btn btn-icon" title="View">
                                            <i class="fas fa-eye text-primary"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {!! $pastWishLists->render() !!}
            </div>
        </div>
    @endif

@endsection
