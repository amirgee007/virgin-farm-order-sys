@extends('layouts.app')

@section('page-title', __('Manage Products'))
@section('page-heading', __('Manage Products'))

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">
        @lang('Products')
    </li>
@stop

@section('styles')
    <style>
        .loader {
            height: 70px !important;
        }
        .products-list-table th, .products-list-table td {
            padding: 0.3rem !important;
        }
        .products-list-table {
            font-weight: 400 !important;
            font-size: 13px !important;
            line-height: 1.6 !important;
        }
        .width50{
            width: 60px !important;
        }
        /*button {*/
        /*font-size: 11px !important;*/
        /*font-weight: 300 !important;*/
        /*}*/
        /*caption {*/
        /*caption-side:top;*/
        /*}*/
        /*tr:hover > td {*/
        /*cursor: pointer !important;*/
        /*}*/
    </style>
@endsection

<div class="dropdown">
    <button type="button" class="btn btn-info" data-toggle="dropdown">

    </button>
    <div class="dropdown-menu">
        <div class="row total-header-section">
            <div class="col-lg-6 col-sm-6 col-6">
                <i class="fa fa-shopping-cart" aria-hidden="true"></i> <span class="badge badge-pill badge-danger">{{ count((array) session('cart')) }}</span>
            </div>
            @php $total = 0 @endphp
            @foreach((array) session('cart') as $id => $details)
                @php $total += $details['price'] * $details['quantity'] @endphp
            @endforeach
            <div class="col-lg-6 col-sm-6 col-6 total-section text-right">
                <p>Total: <span class="text-info">$ {{ $total }}</span></p>
            </div>
        </div>
        @if(session('cart'))
            @foreach(session('cart') as $id => $details)
                <div class="row cart-detail">
                    <div class="col-lg-4 col-sm-4 col-4 cart-detail-img">
                        <img src="{{ $details['image'] }}" />
                    </div>
                    <div class="col-lg-8 col-sm-8 col-8 cart-detail-product">
                        <p>{{ $details['name'] }}</p>
                        <span class="price text-info"> ${{ $details['price'] }}</span> <span class="count"> Quantity:{{ $details['quantity'] }}</span>
                    </div>
                </div>
            @endforeach
        @endif
        <div class="row">
            <div class="col-lg-12 col-sm-12 col-12 text-center checkout">
                <a href="" class="btn btn-primary btn-block">View all</a>
            </div>
        </div>
    </div>
</div>

@section('content')
    @include('partials.messages')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body mt-0 p-3">

                    <span>
                        <b>
                            1. Enter your shipping information
                        </b>
                    </span>
                    <label class="form-check-label float-right"
                           data-trigger="hover"
                           data-toggle="popover" data-html='true'
                           data-content="
                                    Virgin Farms Inc. <br/>
                                    8495 NW 66 Street <br/>
                                    Miami, FL 33166<br/>
                                    ">
                        Where should it be shipped?
                    </label>
                    <div class="row my-2 flex-md-row flex-column-reverse">
                        <div class="col-md-10 col-sm-12 mt-md-0 mt-1">
                            <form action="" method="GET" id="filters-form" class="border-bottom-light">
                                <div class="input-group custom-search-form">

                                    <input type="date"
                                           class="form-control rounded"
                                           name="date_shipped"
                                           title="When do you want your product to be shipped?"
                                           data-trigger="hover"
                                           data-toggle="tooltip"
                                           value="{{ \Request::get('date_shipped') }}">

                                    <select name="carrier_id" class="form-control ml-2 rounded"
                                            title="What is your carrier choice?"
                                            data-trigger="hover"
                                            data-toggle="tooltip"
                                    >
                                        <option hidden value="">Select Carrier</option>
                                        @foreach($carriers AS $key => $name)
                                            <option value="{{$key}}"
                                                {{ Request::get('carrier_id') == $key ? 'selected' : '' }}>
                                                {{$name}}
                                            </option>
                                        @endforeach
                                        <option value="" >Clear Option</option>
                                    </select>

                                    <input type="text"
                                           class="form-control rounded ml-2"
                                           name="po"
                                           placeholder="What is PO#?"
                                           title="What is your PO#? (optional)"
                                           data-trigger="hover"
                                           data-toggle="tooltip"
                                           value="{{ \Request::get('po') }}">

                                    <span class="input-group-append">
                                        @if (\Request::has('carrier_id') && \Request::get('carrier_id') != '')
                                            <a href="{{ route('products.index') }}"
                                               title="Reset Filters"
                                               data-trigger="hover"
                                               data-toggle="tooltip"
                                               class="btn btn-light d-flex align-items-center text-muted"
                                               role="button">
                                                <i class="fas fa-times"></i>
                                        </a>
                                        @endif
                                        <button class="btn btn-secondary ml-1" type="submit" id="search-products-btn">
                                        <i class="fas fa-search "></i>
                                    </button>
                                </span>
                                </div>
                            </form>
                        </div>

                        {{--@permission('orders.filter')--}}
                        @include('products._partial.filter')
                        {{--@endpermission--}}

                    </div>

                    <div class="table-responsive mt-2" id="users-table-wrapper">
                        <table class="table table-borderless table-striped products-list-table">
                            <thead>
                            <tr>
                                <th class="min-width-80">@lang('Vendor')</th>
                                <th class="min-width-200">@lang('Product Description')</th>
                                <th class="min-width-80">@lang('Unit Price')</th>
                                <th class="min-width-80">@lang('Stem/Bunch')</th>
                                <th class="min-width-80">@lang('Quantity')</th>
                                <th class="min-width-80">@lang('Box Type')</th>
                                <th class="min-width-80">@lang('Unit/Box')</th>
                                <th class="min-width-80">@lang('Mark Code')</th>
                                <th class="min-width-80">@lang('Order Qty(Boxes)')</th>
                                <th class="min-width-80">@lang('Actions')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (count($products))
                                @foreach ($products as $index => $product)
                                    <tr>

                                        <td class="align-middle">{{ $product->vendor }}</td>
                                        <td class="align-middle">
                                            <img style="max-width: 35px; cursor: pointer;"
                                                 title="Click to show Larger image"
                                                 data-toggle="tooltip" data-placement="bottom"
                                                 data-largeimg="{{$product->image_url}}"
                                                 src="{{ $product->image_url ? $product->image_url : asset('assets\img\no-image.png') }}" class="img-thumbnail" alt="Virgin Farm">
                                            {{ $product->product_text }}

                                            {!!  $product->is_deal ? '<i class="fas fa-bolt text-danger" title="Deal"></i>' :'' !!}
                                        </td>

                                        <td class="align-middle">${{ $product->unit_price }}/ST</td>
                                        <td class="align-middle">{{ $product->stems }}</td>
                                        <td class="align-middle">{{ $product->quantity }} BX</td>
                                        <td class="align-middle">{{ $product->box_type }}</td>
                                        <td class="align-middle">{{ $product->units_box }}</td>

                                        <form action="{{route('add.to.cart')}}" method="POST" enctype="multipart/form-data">
                                            {{csrf_field()}}
                                            <input type="hidden" name="product_id" value="{{$product->id}}">
                                        <td class="align-middle">
                                            <input class="form-control form-control-sm width50" name="mark_code" type="text">
                                        </td>
                                        <td class="align-middle">
                                            <input required class="form-control form-control-sm width50" name="quantity" type="number">
                                        </td>

                                        <td class="align-middle">

                                            <button type="submit" class="btn btn-icon" title="@lang('Add product to cart')" data-toggle="tooltip"
                                                    data-placement="top"><i class="fas fa-plus-circle "></i>
                                            </button>
                                            {{--<a href="" class="btn btn-icon add-to-cart"--}}
                                               {{--data-id="{{$product->id}}"--}}
                                               {{--title="@lang('Add product to cart')"--}}
                                               {{--data-toggle="tooltip"--}}
                                               {{--data-placement="top">--}}
                                                {{--<i class="fas fa-plus-circle "></i>--}}
                                            {{--</a>--}}
                                        </td>
                                        </form>

                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8"><em>@lang('No address found.')</em></td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="largeImgModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Large Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img src="" id="imagePreviewId" style="width: 650px; height: 400px;" >
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    @include('partials.toaster-js')
    <script>
        $('.img-thumbnail').click(function () {
            $('#imagePreviewId').attr('src', $(this).data('largeimg'));
            $('#largeImgModal').modal('show');
        });

//        $("#doubleCheckMode1, #search-products-btn").change(function () {
//            $("#filters-form").submit();
//        });


        $(".add-to-cart").click(function (event) {

            {{--var _this = $(this);--}}
            {{--var product_id = _this.data("id");--}}

            {{--$.ajax({--}}
                {{--url: "{{ route('product.add.to.cart') }}",--}}
                {{--method: 'post',--}}
                {{--data: {--}}
                    {{--product_id: product_id--}}
                {{--},--}}
                {{--success: function (data) {--}}
                    {{--if (data.result) {--}}
                        {{--toastr.success("Product added to cart successfully.", "Success");--}}
                        {{--//reload if needed--}}
                        {{--//window.location.href = window.location.href + "#myItems";--}}
                        {{--//location.reload();--}}
                    {{--}--}}
                    {{--else {--}}
                        {{--toastr.error("Something went wrong please contact admin.", "Error");--}}
                    {{--}--}}
                {{--}--}}
            {{--});--}}

        });


//        #later when need to make it more accurate VIA AJAX
        {{--$(".add-to-cart").click(function (event) {--}}

            {{--var _this = $(this);--}}
            {{--var product_id = _this.data("id");--}}

            {{--$.ajax({--}}
                {{--url: "{{ route('product.add.to.cart') }}",--}}
                {{--method: 'post',--}}
                {{--data: {--}}
                    {{--product_id: product_id--}}
                {{--},--}}
                {{--success: function (data) {--}}
                    {{--if (data.result) {--}}
                        {{--toastr.success("Product added to cart successfully.", "Success");--}}
                        {{--//reload if needed--}}
                        {{--//window.location.href = window.location.href + "#myItems";--}}
                        {{--//location.reload();--}}
                    {{--}--}}
                    {{--else {--}}
                        {{--toastr.error("Something went wrong please contact admin.", "Error");--}}
                    {{--}--}}
                {{--}--}}
            {{--});--}}

        {{--});--}}
    </script>
@endsection
