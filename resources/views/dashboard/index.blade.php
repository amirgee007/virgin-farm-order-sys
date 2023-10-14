@extends('layouts.app')

@section('page-title', __('Dashboard'))
@section('page-heading', __('Dashboard'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('Dashboard')
    </li>
@stop

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
                    <table class="tblEdition" align="center" style="width: 100%" cellpadding="2" cellspacing="2" border="0">
                        <tbody><tr>
                            <td width="35%" class="right vmiddle no-break"><b>Name:</b></td>
                            <td>
                                <div id="account_name">Virgin Farms Inc.</div>
                            </td>
                        </tr>
                        <tr>
                            <td class="right vmiddle no-break"><b>Salesperson:</b></td>
                            <td>
                                <div id="account_sales_person">JP Ponce</div>
                            </td>
                        </tr>
                        <tr>
                            <td class="right vmiddle no-break"><b>Buyer:</b></td>
                            <td>
                                <div id="account_buyers"></div>
                            </td>
                        </tr>
                        <tr>
                            <td class="right vmiddle no-break"><b>Address:</b></td>
                            <td>
                                <div id="account_address">8475 NW 66 Street</div>
                            </td>
                        </tr>
                        <tr>
                            <td class="right vmiddle no-break"><b>City:</b></td>
                            <td>
                                <div id="account_city">Miami</div>
                            </td>
                        </tr>
                        <tr>
                            <td class="right vmiddle no-break"><b>State:</b></td>
                            <td>
                                <div id="account_state">FL</div>
                            </td>
                        </tr>
                        <tr>
                            <td class="right vmiddle no-break"><b>Zip/Postal Code:</b></td>
                            <td>
                                <div id="account_zipcode">33166</div>
                            </td>
                        </tr>
                        <tr>
                            <td class="right vmiddle no-break"><b>Email:</b></td>
                            <td>
                                <div id="account_email">compras@virginfarms.com;olif@virginfarms.com</div>
                            </td>
                        </tr>
                        <tr>
                            <td class="right vmiddle no-break"><b>Phone:</b></td>
                            <td>
                                <div id="account_phone">305-436-8703</div>
                            </td>
                        </tr>
                        <tr>
                            <td class="right vmiddle no-break"><b>Fax:</b></td>
                            <td>
                                <div id="account_fax">305-436-8711</div>
                            </td>
                        </tr>
                        </tbody></table>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <table class="tblEdition" align="center" style="width: 100%" cellpadding="2" cellspacing="2" border="0">
                        <tbody><tr>
                            <td width="50%" style="text-align: right;"><b>Balance:</b></td>
                            <td width="50%">
                                <div id="account_balance">$2,288.12</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right;"><b>Credit Limit:</b></td>
                            <td>
                                <div id="account_credit_limit">$10,000.00</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right;"><b>Available Credit:</b></td>
                            <td>
                                <div id="account_credit_available">$7,711.88</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right;"><b>Terms:</b></td>
                            <td>
                                <div id="account_terms">Net 30</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right;"><b>Customer Since:</b></td>
                            <td>
                                <div id="account_customer_since">07/20/2021</div>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right;"><b>Last Sale Date:</b></td>
                            <td>
                                <div id="account_lastsale_date">10/11/2023</div>
                            </td>
                        </tr>

                        <tr>
                            <td style="text-align: right;"><b>Last Payment:</b></td>
                            <td>
                                <div id="account_last_payment">$85.79</div>
                            </td>
                        </tr>
                        </tbody></table>
                </div>
            </div>
        </div>


        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table>
                        <tr>
                            <td>Month</td>
                            <td>2023</td>
                            <td>2022</td>
                            <td>2021</td>
                        </tr>
                        <tr>
                            <td>Jan</td>
                            <td>$998 </td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>Feb</td>
                            <td>$2,225 </td>
                            <td>$6,580 </td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>Mar</td>
                            <td>$545 </td>
                            <td>$64 </td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>Apr</td>
                            <td>$673 </td>
                            <td>$4,139 </td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>May</td>
                            <td>$1,217 </td>
                            <td>$215 </td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>Jun</td>
                            <td>$262 </td>
                            <td>$71 </td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>Jul</td>
                            <td>$261 </td>
                            <td>-</td>
                            <td>$188 </td>
                        </tr>
                        <tr>
                            <td>Aug</td>
                            <td>$137 </td>
                            <td>$244 </td>
                            <td>$671 </td>
                        </tr>
                        <tr>
                            <td>Sep</td>
                            <td>$1,165 </td>
                            <td>$58 </td>
                            <td>$44 </td>
                        </tr>
                        <tr>
                            <td>Oct</td>
                            <td>$1,179 </td>
                            <td>-</td>
                            <td>$179 </td>
                        </tr>
                        <tr>
                            <td>Nov</td>
                            <td>-</td>
                            <td>-</td>
                            <td>$375 </td>
                        </tr>
                        <tr>
                            <td>Dec</td>
                            <td>-</td>
                            <td>-</td>
                            <td>$1,738 </td>
                        </tr>
                    </table>
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
