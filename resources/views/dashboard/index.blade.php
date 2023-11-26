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
        /*table {*/
            /*border-collapse: separate;*/
            /*border-spacing: 0 15px;*/
        /*}*/

        /*th {*/
            /*background-color: #4287f5;*/
            /*color: white;*/
        /*}*/

        /*th,*/
        /*td {*/
            /*width: 150px;*/
            /*text-align: center;*/
            /*border: 1px solid black;*/
            /*padding: 5px;*/
        /*}*/

        /*h2 {*/
            /*color: #4287f5;*/
        /*}*/
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

    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" id="users-table-wrapper">
                        <table class="table table-borderless table-striped table-sm">
                            <thead>

                            <tr>
                                <td class="min-width-150"><b>Name:</b></td>
                                <td>
                                    <div>Virgin Farms Inc.</div>
                                </td>
                            </tr>

                            <tr>
                                <td><b>Salesperson:</b></td>
                                <td>
                                    <div id="account_sales_person">JP Ponce</div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Buyer:</b></td>
                                <td>
                                    <div id="account_buyers"></div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Address:</b></td>
                                <td>
                                    <div id="account_address">8475 NW 66 Street</div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>City:</b></td>
                                <td>
                                    <div id="account_city">Miami</div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>State:</b></td>
                                <td>
                                    <div id="account_state">FL</div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Zip/Postal Code:</b></td>
                                <td>
                                    <div id="account_zipcode">33166</div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Email:</b></td>
                                <td>
                                    <div id="account_email">compras@virginfarms.com;olif@virginfarms.com</div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Phone:</b></td>
                                <td>
                                    <div id="account_phone">305-436-8703</div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Fax:</b></td>
                                <td>
                                    <div id="account_fax">305-436-8711</div>
                                </td>
                            </tr>


                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" id="users-table-wrapper">
                        <table class="table table-borderless table-striped">
                            <thead>

                            <tr>
                                <td class="min-width-150"><b>Balance:</b></td>
                                <td>
                                    <div>$2,288.12</div>
                                </td>
                            </tr>

                            <tr>
                                <td><b>Credit Limit:</b></td>
                                <td>
                                    <div id="account_sales_person">JP Ponce</div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Available Credit:</b></td>
                                <td>
                                    <div id="account_buyers"></div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Terms:</b></td>
                                <td>
                                    <div id="account_address">8475 NW 66 Street</div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Customer Since:</b></td>
                                <td>
                                    <div id="account_city">Miami</div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Last Sale Date:</b></td>
                                <td>
                                    <div id="account_state">FL</div>
                                </td>
                            </tr>
                            <tr>
                                <td><b>Last Payment:</b></td>
                                <td>
                                    <div id="account_zipcode">33166</div>
                                </td>
                            </tr>


                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive" id="users-table-wrapper">
                        <table class="table table-borderless table-striped">
                            <thead>
                            <tr>
                                <th>Year</th>
                                <th>Jan</th>
                                <th>Feb</th>
                                <th>Mar</th>
                                <th>Apr</th>
                                <th>May</th>
                                <th>Jun</th>
                                <th>Jul</th>
                                <th>Aug</th>
                                <th>Sep</th>
                                <th>Oct</th>
                                <th>Nov</th>
                                <th>Dec</th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                                <td>2021</td>
                                <td>$1500</td>
                                <td>$1700</td>
                                <td>$1600</td>
                                <td>$1800</td>
                                <td>$1900</td>
                                <td>$2000</td>
                                <td>$2100</td>
                                <td>$2200</td>
                                <td>$2300</td>
                                <td>$2400</td>
                                <td>$2500</td>
                                <td>$2600</td>
                            </tr>
                            <tr>
                                <td>2022</td>
                                <td>$2600</td>
                                <td>$2700</td>
                                <td>$2800</td>
                                <td>$2900</td>
                                <td>$3000</td>
                                <td>$3100</td>
                                <td>$3200</td>
                                <td>$3300</td>
                                <td>$3400</td>
                                <td>$3500</td>
                                <td>$3600</td>
                                <td>$3700</td>
                            </tr>
                            <tr>
                                <td>2023</td>
                                <td>$3700</td>
                                <td>$3800</td>
                                <td>$3900</td>
                                <td>$4000</td>
                                <td>$4100</td>
                                <td>$4200</td>
                                <td>$4300</td>
                                <td>$4400</td>
                                <td>$4500</td>
                                <td>$4600</td>
                                <td>$4700</td>
                                <td>$4800</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    {{--@foreach (\Vanguard\Plugins\Vanguard::availableWidgets(auth()->user()) as $widget)--}}
        {{--@if (method_exists($widget, 'scripts'))--}}
            {{--{!! app()->call([$widget, 'scripts']) !!}--}}
        {{--@endif--}}
    {{--@endforeach--}}
@stop
