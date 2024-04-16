@extends('layouts.app')

@section('page-title', __('My Orders'))
@section('page-heading', __('All Orders'))

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">
        @lang('Orders')
    </li>
@stop

@section('styles')
    <link media="all" type="text/css" rel="stylesheet" href="{{ url('assets/css/custom.css') }}">
{{--    <link media="all" type="text/css" rel="stylesheet" href="{{ url('assets/plugins/x-editable/bootstrap-editable.css') }}">--}}
{{--    <link media="all" type="text/css" rel="stylesheet" href="{{ url('assets/plugins/daterangepicker/daterangepicker.css') }}">--}}
{{--    <link media="all" type="text/css" rel="stylesheet" href="{{ url('assets/plugins/select2/select2.min.css') }}">--}}

<style>
    .loader {
        height: 70px !important;
    }
    .orders-list-table th, .orders-list-table td {
        padding: 0.3rem !important;
    }
    .orders-list-table {
        font-weight: 400 !important;
        font-size: 12px !important;
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
@endsection

@section('content')
    @include('partials.messages')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body mt-0 p-3">

                    <div class="notes-success" style="">
                        <p>Total Orders are in the system are.
                            <b>
                                {{$count}}
                            </b>
                        </p>
                    </div>

                    <form action="" method="GET" id="orders-form" class="pb-2 mb-3 border-bottom-light">
                        <div class="row my-2 flex-md-row flex-column-reverse">
                            <div class="col-md-6 mt-md-0 mt-2">
                                <div class="input-group custom-search-form">
                                    <input type="text"
                                           class="form-control input-solid"
                                           name="search"
                                           value="{{ Request::get('search') }}"
                                           placeholder="Search by order, name, company, address">

                                    <span class="input-group-append">
                                    @if (Request::has('search') && Request::get('search') != '')
                                            <a href="{{ route('orders.index') }}"
                                               class="btn btn-light d-flex align-items-center text-muted"
                                               role="button">
                                                <i class="fas fa-times"></i>
                                        </a>
                                   @endif
                                    <button class="btn btn-light" type="submit">
                                              <i class="fas fa-search text-muted"></i>
                                    </button>
                                </span>
                                </div>
                            </div>

                        </div>
                    </form>

                    <div class="table-responsive mt-2 orders-list-table" id="users-table-wrapper">
                        <table class="table table-borderless table-striped products-list-table">
                            <thead>
                            <tr>
                                <th class="min-width-80">@lang('Id')</th>
                                <th class="min-width-80">@lang('User')</th>
                                <th class="min-width-80">@lang('Ship Date')</th>
                                <th class="min-width-80">@lang('Name')</th>
                                <th class="min-width-80">@lang('Company')</th>
                                <th class="min-width-80">@lang('Phone')</th>
                                <th class="min-width-80">@lang('Address')</th>
                                <th class="min-width-80">@lang('SubTotal')</th>
                                <th class="min-width-80">@lang('Shipping Cost')</th>
                                <th class="min-width-80">@lang('Total')</th>
{{--                                <th class="min-width-80">@lang('Size')</th>--}}
                                <th class="min-width-80">@lang('Status')</th>
                                <th class="min-width-80">@lang('Created')</th>
                                <th class="min-width-80">@lang('Action')</th>

                            </tr>
                            </thead>
                            <tbody>
                            @if ($count)
                                @foreach ($orders as $index => $order)
                                    @include('orders.row')
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

    {!! $orders->render() !!}

@stop

@section('scripts')
    @include('partials.toaster-js')
    <script src="{{ url('assets/plugins/daterangepicker/daterangepicker.min.js') }}"></script>
    <script src="{{ url('assets/plugins/select2/select2.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/x-editable/bootstrap-editable.min.js') }}" ></script>

    <script>

    </script>
@endsection
