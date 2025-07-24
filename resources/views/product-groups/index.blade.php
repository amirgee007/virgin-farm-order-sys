@extends('layouts.app')

@section('page-title', __('Product Groups Combos'))
@section('page-heading', __('Products Groups Combos'))

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">
        @lang('Manage Groups')
    </li>
@stop


@section('styles')

@endsection

@section('content')
    @include('partials.messages')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body mt-0 p-3">
                    <div class="notes-success p-2 d-flex justify-content-between align-items-center" style="background-color: #d4f8d4; border-radius: 5px;">
                        <div>
                            <span>Total Products Groups Combos in the system are: <strong>{{@$count}}</strong></span>
                        </div>
                        <div>
                            <a href="{{ route('product-groups.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus-circle"></i>
                                Create New Group
                            </a>
                        </div>
                    </div>

{{--                    <form action="" method="GET" id="product-form" class="pb-2 mb-1 border-bottom-light">--}}
{{--                        <div class="row my-2 flex-md-row flex-column-reverse">--}}
{{--                            <div class="col-md-6 mt-md-0 mt-2">--}}
{{--                                <div class="input-group custom-search-form">--}}
{{--                                    <input type="text"--}}
{{--                                           class="form-control input-solid"--}}
{{--                                           name="search"--}}
{{--                                           value="{{ Request::get('search') }}"--}}
{{--                                           placeholder="Search by Item, Description">--}}

{{--                                    <span class="input-group-append">--}}
{{--                                        @if (Request::has('search') && Request::get('search') != '')--}}
{{--                                            <a href="{{ route('inventory.index') }}"--}}
{{--                                               class="btn btn-light d-flex align-items-center text-muted"--}}
{{--                                               role="button">--}}
{{--                                                    <i class="fas fa-times"></i>--}}
{{--                                            </a>--}}
{{--                                        @endif--}}
{{--                                        <button class="btn btn-light" type="submit"> <i class="fas fa-search text-muted"></i></button>--}}
{{--                                    </span>--}}
{{--                                </div>--}}
{{--                            </div>--}}


{{--                        </div>--}}
{{--                    </form>--}}

                    <div class="table-responsive mt-2" id="users-table-wrapper">
                        <table class="table table- table-bordered products-list-table">
                            <thead>
                            <tr>
                                <th>Parent Product</th>
                                <th>Group Name</th>
                                <th>Products (with Stems)</th>
                                <th>Last Update</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (count($groups))
                                @foreach($groups as $group)
                                    @php
                                        $totalStems = $group->products->sum(fn($p) => $p->pivot->stems);
                                    @endphp

                                        <!-- Main Row -->
                                    <tr>
                                        <td style="background-color: #f4f8fb;">
                                            @if($group->parentProduct)
                                                <a target="_blank"
                                                   href="{{ route('products.index.manage', ['search' => $group->parentProduct->item_no]) }}"
                                                   class="badge badge-lg badge-primary text-decoration-none"
                                                   data-toggle="tooltip" data-placement="left"
                                                   title="Click to see detail on manage products page.">
                                                    {{ $group->parentProduct->item_no }}
                                                </a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ $group->name }}</td>

                                        <td>
                                            <strong>Total:</strong> {{ $totalStems }} stems
                                            <br>
                                            <a href="javascript:void(0);" onclick="toggleDetails({{ $group->id }})" class="text-primary"
                                               title="Click  to show more detail about stems" data-toggle="tooltip" data-placement="left">
                                                Show Details
                                                <i class="fa fa-arrow-down"></i>
                                            </a>
                                        </td>

                                        <td>{{ diff4Human($group->created_at) }}</td>

                                        <td class="d-flex gap-1">
                                            <a href="{{ route('product-groups.edit', $group->id) }}" class="btn btn-sm btn-info">Edit</a>

                                            <form action="{{ route('product-groups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Delete group?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger ml-1">Delete</button>
                                            </form>

                                            <button type="button"
                                                    class="btn btn-sm btn-secondary view-breakdown ml-1"
                                                    data-url="{{ route('product-groups.breakdown', $group->parent_product_id) }}"
                                                    title="View Combo Breakdown"  data-toggle="tooltip" data-placement="left">
                                                👁️
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Hidden Detail Row -->
                                    <tr id="details-{{ $group->id }}" style="display: none;">
                                        <td colspan="4">
                                            <div class="border p-2 rounded bg-light">
                                                <strong>Product Breakdown:</strong>
                                                <ul class="mb-0">
                                                    @foreach($group->products as $product)
                                                        <li>
                                                            {{ $product->item_no }} – {{ $product->pivot->stems }} stems
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4">No products found</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {!! $groups->render() !!}

    <div class="modal fade" id="breakdownModal" tabindex="-1" aria-labelledby="breakdownModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="breakdownModalLabel">Product Breakdown</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="breakdown-modal-body">
                    <!-- AJAX content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        function toggleDetails(groupId) {
            const row = document.getElementById('details-' + groupId);
            row.style.display = row.style.display === 'none' ? '' : 'none';
        }
        $(document).on('click', '.view-breakdown', function () {
            const url = $(this).data('url');

            // Clear old content
            $('#breakdown-modal-body').html('Loading...');

            // Fetch breakdown data via AJAX using named route
            $.get(url, function (response) {
                $('#breakdown-modal-body').html(response.html);
                $('#breakdownModal').modal('show');
            }).fail(() => {
                $('#breakdown-modal-body').html('<p class="text-danger">Failed to load breakdown.</p>');
                $('#breakdownModal').modal('show');
            });
        });
    </script>


@endsection
