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

    @if(myRoleName() == 'Admin')
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
                    <div class="card-body" style="padding: 5px">
                        <h5 class="text-center">Recent Future Inventory</h5>
                        <h6>Date Ranges</h6>
                        <hr>
                        <div class="table-responsive orders-list-table" id="users-table-wrapper">
                            <table class="table table-borderless table-striped">
                                <thead></thead>
                                <tbody>
                                @if ($futureInventory)
                                    @foreach ($futureInventory as $order)
                                        <tr>
                                            <th class="align-middle">{{ dateFormatRecent($order->date_in) }}  -  {{dateFormatRecent($order->date_out)}}</th>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="12">
                                            No date range found
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-body" style="padding: 5px">
                        <h5 class="text-center">Show Low Inventory</h5>
                        <hr>
                        <div class="table-responsive orders-list-table">
                            <table class="table table-borderless table-striped products-list-table">
                                <thead>
                                <tr>
                                    <th >@lang('Item')</th>
                                    <th >@lang('Quantity')</th>
                                    <th >@lang('Date In')</th>
                                    <th >@lang('Date Out')</th>
                                    <th >@lang('Last Updated')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if ($lowInventory)
                                    @foreach ($lowInventory as  $product)
                                        <tr>
                                            <td class="align-middle">{{ $product->item_no }}</td>
                                            <td><span class="badge badge-lg badge-danger"> {{ $product->quantity }} </span></td>
                                            <td class="align-middle">{{ $product->date_in }}</td>
                                            <td class="align-middle">{{ $product->date_out }}</td>
                                            <td class="align-middle">{{ diff4Human($product->updated_at) }}</td>
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
        </div>
    @else

    <div class="container mt-3">
            <div class="row">
                <div class="col-sm-6">
                    <div class="card text-white mb-3 supplier-card {{auth()->user()->supplier_id == 1 ? 'selected' :''}}" data-supplier="1" >
                        <div class="card-body">
                            <div class="text-center">
                                <img src="{{ url('assets/img/dashboard/vf.png') }}" alt="{{ setting('app_name') }}" height="300">
                            </div>
                            <div class="text-center mt-3 text-primary">
                                Virgin Farms Inventory
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card text-white mb-3 supplier-card {{auth()->user()->supplier_id == 2 ? 'selected' :''}}" data-supplier="2">
                        <div class="card-body">
                            <div class="text-center">
                                <img src="{{ url('assets/img/dashboard/dutch.png') }}" alt="{{ setting('app_name') }}" height="300">
                            </div>
                            <div class="text-center mt-3 text-danger">
                                Dutch Flowers
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
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
