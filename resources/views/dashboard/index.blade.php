@extends('layouts.app')

@section('page-title', __('Dashboard'))
@section('page-heading', __('Dashboard'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('Dashboard')
    </li>
@stop

@section('styles')
    <style>
        .loader {
            height: 70px !important;
        }
        .orders-list-table th, .orders-list-table td {
            padding: 0.3rem !important;
        }
        .orders-list-table {
            font-weight: 400 !important;
            font-size: 10px !important;
            line-height: 1.328571429 !important;
        }
        button {
            font-size: 12px !important;
            font-weight: 400 !important;
        }
        caption {
            caption-side:top;
        }
        tr:hover > td {
            cursor: pointer !important;
        }
    </style>

    <style>
        .card {
            cursor: pointer;
            border: 3px dashed transparent;
            border-width:3px !important;
        }
        .card.selected {
            border-color: #748c41;
        }
    </style>
@endsection

@section('content')
    @include('partials.messages')

    @include('dashboard.warning')
    <div class="row">
        @foreach (\Vanguard\Plugins\Vanguard::availableWidgets(auth()->user()) as $widget)
            @if ($widget->width)
                <div class="col-md-{{ $widget->width }}">
                    @endif
                    {!! app()->call([$widget, 'render']) !!}
                    @if($widget->width)
                </div>
            @endif
        @endforeach
    </div>

    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-auto">
                <a target="_blank" href="https://scribehow.com/page/Web_Shop_Maintenance_Topics__LLhKs5_JSt-ohAt_Y0IMWA?referrer=documents"
                   class="btn btn-danger btn-lg"> System Maintenance Guide (Admin Only)
                </a>
            </div>
        </div>
    </div>


    <div class="container mt-3">
            <div class="row">
                <div class="col-sm-4">
                    <div class="card text-white mb-3 supplier-card {{auth()->user()->supplier_id == 1 ? 'selected' :''}}" data-supplier="1" >
                        <div class="card-body">
                            <div class="text-center">
                                <img src="{{ url('assets/img/dashboard/vf.png') }}" class="img-fluid" alt="{{ setting('app_name') }}" height="300">
                            </div>
                            <div class="text-center mt-3 text-primary">
                                <b>Virgin Farms Inventory</b>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card text-white mb-3 supplier-card {{auth()->user()->supplier_id == 2 ? 'selected' :''}}" data-supplier="2">
                        <div class="card-body">
                            <div class="text-center">
                                <img src="{{ url('assets/img/dashboard/dutch.png') }}" class="img-fluid" alt="{{ setting('app_name') }}" height="300">
                            </div>
                            <div class="text-center mt-3 text-danger">
                                <b>Dutch Flowers</b>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card text-white mb-3 supplier-card {{auth()->user()->supplier_id == 3 ? 'selected' :''}}" data-supplier="3">
                        <div class="card-body">
                            <div class="text-center">
                                <img src="{{ url('assets/img/dashboard/special.png') }}" class="img-fluid" alt="{{ setting('app_name') }}" height="300">
                            </div>
                            <div class="text-center mt-3 text-warning">
                                <b>Special Offers</b>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-body" style="padding: 5px">
                    <h5 class="text-center">Recent Orders</h5>
                    <hr>
                    <div class="table-responsive orders-list-table" id="users-table-wrapper">
                        <table class="table table-borderless table-striped products-list-table">
                            <thead>
                            <tr>
                                <th >@lang('Id')</th>
                                <th >@lang('User')</th>
                                <th >@lang('Ship Date')</th>
                                <th >@lang('Carrier Name')</th>
                                <th >@lang('Status')</th>
                                <th >@lang('Created')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if ($orders)
                                @foreach ($orders as $index => $order)
                                    <tr>
                                        <td class="align-middle" title="@lang('Click to see order detail')"
                                            data-toggle="tooltip"
                                            data-placement="left">
                                            <a target="_blank" href="{{route('orders.index')."?search=WO".$order->id}}">
                                                <span class="badge badge-lg badge-primary">WO{{ $order->id }}</span>
                                            </a>
                                        </td>
                                        <td class="align-middle">{{ $order->name }}</td>
                                        <td class="align-middle">{{ $order->date_shipped }}</td>
                                        <td class="align-middle">{{ @$order->carrier->carrier_name }}</td>
                                        <td><span class="badge badge-lg badge-danger"> Active </span></td>
                                        <td class="align-middle">{{ diff4Human($order->created_at) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="12">
                                        No Orders found
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-header" style="cursor: pointer;" data-toggle="collapse" data-target="#futureInventoryCollapse" aria-expanded="false" aria-controls="futureInventoryCollapse">
                    <h5 class="text-center mb-0">Recent Future Inventory</h5>
                </div>
                <div class="card-body" style="padding: 5px">
                    <!-- Removed the first <hr> to avoid duplicate horizontal rules -->
                    <div class="table-responsive orders-list-table" id="users-table-wrapper">
                        <table class="table table-borderless table-striped">
                            <thead>
                            <tr>
                                <th colspan="3">Date Ranges</th>
                                <th colspan="3">Type</th>
                                <th colspan="3">Last Updated</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if ($futureInventory)
                                @foreach ($futureInventory as $index => $inventory)
                                    @if ($index < 2)
                                        <!-- Always show the first two rows -->
                                        @include('dashboard.future_inventory')
                                    @endif
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    @if (count($futureInventory) > 2)
                        <!-- Collapse button and content for the rest of the rows -->
                        <div id="futureInventoryCollapse" class="collapse">
                            <div class="table-responsive orders-list-table" id="users-table-wrapper">
                                <table class="table table-borderless table-striped">
                                    <tbody>
                                    @foreach ($futureInventory as $index => $inventory)
                                        @if ($index >= 2)
                                            <!-- Rest of the rows hidden by default -->
                                            @include('dashboard.future_inventory')
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="text-center">
                            <a style="cursor: pointer;" data-toggle="collapse" data-target="#futureInventoryCollapse" aria-expanded="false" aria-controls="futureInventoryCollapse">
                                Show More
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{--        <div class="col-12">--}}
        {{--            <div class="card">--}}
        {{--                    <div class="card-body" style="padding: 5px">--}}
        {{--                        <h5 class="text-center">Show Low Inventory</h5>--}}
        {{--                        <hr>--}}
        {{--                        <div class="table-responsive orders-list-table">--}}
        {{--                            <table class="table table-borderless table-striped products-list-table">--}}
        {{--                                <thead>--}}
        {{--                                <tr>--}}
        {{--                                    <th >@lang('Item')</th>--}}
        {{--                                    <th >@lang('Quantity')</th>--}}
        {{--                                    <th >@lang('Date In')</th>--}}
        {{--                                    <th >@lang('Date Out')</th>--}}
        {{--                                    <th >@lang('Last Updated')</th>--}}
        {{--                                </tr>--}}
        {{--                                </thead>--}}
        {{--                                <tbody>--}}
        {{--                                @if ($lowInventory)--}}
        {{--                                    @foreach ($lowInventory as  $product)--}}
        {{--                                        <tr>--}}
        {{--                                            <td class="align-middle">{{ $product->item_no }}</td>--}}
        {{--                                            <td><span class="badge badge-lg badge-danger"> {{ $product->quantity }} </span></td>--}}
        {{--                                            <td class="align-middle">{{ $product->date_in }}</td>--}}
        {{--                                            <td class="align-middle">{{ $product->date_out }}</td>--}}
        {{--                                            <td class="align-middle">{{ diff4Human($product->updated_at) }}</td>--}}
        {{--                                        </tr>--}}
        {{--                                    @endforeach--}}
        {{--                                @else--}}
        {{--                                    <tr>--}}
        {{--                                        <td colspan="12">--}}
        {{--                                            No Orders found--}}
        {{--                                        </td>--}}
        {{--                                    </tr>--}}
        {{--                                @endif--}}
        {{--                                </tbody>--}}
        {{--                            </table>--}}
        {{--                        </div>--}}
        {{--                    </div>--}}
        {{--                </div>--}}
        {{--        </div>--}}
    </div>

@stop

@section('scripts')
    {{--@foreach (\Vanguard\Plugins\Vanguard::availableWidgets(auth()->user()) as $widget)--}}
        {{--@if (method_exists($widget, 'scripts'))--}}
            {{--{!! app()->call([$widget, 'scripts']) !!}--}}
        {{--@endif--}}
    {{--@endforeach--}}
    @include('partials.toaster-js')
    <script>
        $(document).ready(function() {
            $('.supplier-card').click(function() {
                $('.supplier-card').removeClass('selected');
                $(this).addClass('selected');

                var selectedSupplier = $('.supplier-card.selected').data('supplier');

                if (selectedSupplier) {
                    $.ajax({
                        url: '{{ route('update.supplier') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            supplier: selectedSupplier
                        },
                        success: function (response) {
                            toastr.success(response.message);
                            window.location.href = response.href;
                        },
                        error: function () {
                            toastr.error('Something went wrong please check with admin.');
                        },
                    });
                } else {
                    alert('Please select a supplier.');
                }
            });
        });
    </script>
@stop
