@extends('layouts.app')

@section('page-title', __('Client Inventory'))
@section('page-heading', __('Client Inventory'))

@section ('styles')
    <style>
        .radius{
            border-radius: 2.5px;
            border-style: outset;
            cursor: pointer;
            font-size: 9px;
        }
        .highlighted-date {
            background-color: rgb(219, 31, 45) !important;
            color: #ffffff !important;
            border-radius: 60% !important;
        }
    </style>
@stop

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted " style="cursor: pointer;" >
        @lang('See Boxes detail') <i class="fas fa-box text-danger" data-toggle="modal" data-target="#boxesModal"></i>
        &nbsp; &nbsp; &nbsp;
        <div class="form-check form-check-inline"
             title="Change Inventory to Virgin farms"
             data-trigger="hover"
             data-toggle="tooltip">
            <input class="form-check-input" type="radio" name="radioGroup" id="radioVirgin" value="1" {{auth()->user()->supplier_id == 1 ? 'checked' :''}}>
            <label class="form-check-label bg-success text-white p-2 radius" for="radioVirgin">Virgin Farm.</label>
        </div>
        <div class="form-check form-check-inline"
             title="Switch Inventory to Dutch Flowers"
             data-trigger="hover"
             data-toggle="tooltip">
            <input class="form-check-input" type="radio" name="radioGroup" id="radioDutch" value="2" {{auth()->user()->supplier_id == 2 ? 'checked' :''}}>
            <label class="form-check-label bg-danger text-white p-2 radius" for="radioDutch">Dutch</label>
        </div>
        <div class="form-check form-check-inline"
             title="Change Inventory to Special Offers"
             data-trigger="hover"
             data-toggle="tooltip">
            <input class="form-check-input" type="radio" name="radioGroup" id="radioSpecial" value="3" {{auth()->user()->supplier_id == 3 ? 'checked' :''}}>
            <label class="form-check-label bg-warning text-white p-2 radius" for="radioSpecial">Seasonal</label>
        </div>

    </li>
@stop

@section('styles')
    <style>

        [role=main] {
            padding-top: 75px;
        }

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

        .width50 {
            width: 60px !important;
        }
        .blink {
            animation: blink-animation 0.75s steps(5, start) infinite;
            -webkit-animation: blink-animation 0.75s steps(5, start) infinite;
        }

        @keyframes blink-animation {
            to {
                visibility: hidden;
            }
        }

        @-webkit-keyframes blink-animation {
            to {
                visibility: hidden;
            }
        }

        .date-input-container {
            position: relative;
            display: inline-block;
        }

        #date-input {
            width: 100%;
            padding: 8px 35px 8px 10px; /* Add padding to the right for the icon */
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .calendar-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none; /* Prevent the icon from being clickable */
            color: #888;
            font-size: 18px;
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

@section('content')
    @include('partials.messages')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body mt-0 p-3">
                    <span>
                        <b>
                            1. Enter your shipping information
                            <small class="text-primary">(** You are browsing <b>{{ $user->supplier_id == 1 ? 'Virgin Farms'  : ($user->supplier_id == 2 ? 'Dutch' : 'Special and Seasonal ') }}</b> flowers) </small>
                             <label class="form-check-label float-right mt-2 ml-2 text-danger">Ship-To Address
                                 <a target="_blank" class="btn btn-icon" href="{{route('shipping.address.index')}}"
                                    title="@lang('Add New Address')" data-toggle="tooltip" data-placement="top"><i
                                         class="fas fa-plus-circle "></i>
                                 </a>

                                 <select class="form-control form-control-md" id="changeAddress"
                                         title="Where do you want your product to be shipped?"
                                         data-trigger="hover"
                                         data-toggle="tooltip">
                                     <option selected value="0">Default Address</option>
                                     @foreach($address as $add)
                                         <option
                                             {{$user->address_id == $add->id ? 'selected' : '' }} value="{{$add->id}}">{{$add->address}}</option>
                                     @endforeach
                                 </select>
                             </label>

                            <label class="form-check-label float-right mt-2 text-danger">Select Carrier
                                 <select class="form-control form-control-md" id="changeCarrier"
                                         title="Click to change carrier how you want to ship the items?"
                                         data-trigger="hover"
                                         data-toggle="tooltip">
                                     <option hidden value="">Select Shipping Carrier</option>
                                    @foreach($carriers AS $key => $name)
                                         <option value="{{$key}}" {{ $user->carrier_id == $key ? 'selected' : '' }}> {{$name}} </option>
                                     @endforeach
                                 </select>
                             </label>

                        </b>
                    </span>

                    <div class="row my-2 flex-md-row flex-column-reverse">
                        <div class="col-md-12 col-sm-12 mt-md-0 mt-1">
                            <form action="" method="GET" id="filters-form" class="border-bottom-light">
                                <div class="input-group custom-search-form">
{{--                                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>--}}
                                    <select class="form-control form-control-md mr-2" id="add-on-order" style="border:3px solid #cccccc; border-style:dashed"
                                            title="Choose an order to edit, or select 'Add New Order' to proceed?"
                                            data-trigger="hover"
                                            data-toggle="tooltip">
                                        @foreach($myOrders AS $key => $value)
                                            <option value="{{$key}}" {{ $user->edit_order_id == $key ? 'selected' : '' }}>{{$key > 1 ? '#W - ' : '' }} {{$value}} </option>
                                        @endforeach
                                    </select>

                                    <style>
                                        .date-input-container {
                                            position: relative;
                                            display: inline-block;
                                            width: 250px; /* Adjust as needed */
                                        }

                                        #date_shipped {
                                            width: 100%;
                                            padding: 8px 35px 8px 10px; /* Add padding to the right for the icon */
                                            font-size: 16px;
                                            border: 1px solid #ccc;
                                            border-radius: 4px;
                                            box-sizing: border-box;
                                        }

                                        .calendar-icon {
                                            position: absolute;
                                            right: 10px;
                                            top: 50%;
                                            transform: translateY(-50%);
                                            cursor: pointer;
                                            color: #888;
                                            font-size: 18px;
                                        }
                                    </style>

                                    <div class="date-input-container">
                                        <input type="text"
                                               readonly
                                              id="date_shipped"
                                               placeholder="When to Ship?"
                                               name="date_shipped"
                                               title="When do you want your product to be shipped?"
                                               data-trigger="hover"
                                               data-toggle="tooltip"
                                               value="{{ $date_shipped }}">
                                        <span class="calendar-icon">&#x1F4C5;</span> <!-- Unicode calendar icon -->
                                    </div>

                                    <select class="form-control rounded ml-3" name="category" id="category">
                                        <option selected value="">All Categories</option>
                                        @foreach($categories AS $key => $val)
                                            <option value="{{$key}}"
                                                {{ (\Request::get('category') == $key) ? 'selected': ''  }}
                                            >{{$val}}</option>
                                        @endforeach
                                    </select>


                                    <input type="text"
                                           id="searching"
                                           class="form-control rounded ml-2"
                                           placeholder="Search by name, item, category, text etc"
                                           name="searching"
                                           value="{{\Request::get('searching')}}">

                                    <span class="input-group-append">
                                        @if (\Request::get('category') || \Request::get('searching'))
                                            <a href="{{ route('inventory.index') }}"
                                               title="Reset Filters"
                                               data-trigger="hover"
                                               data-toggle="tooltip"
                                               class="btn btn-light d-flex align-items-center text-muted ml-2"
                                               role="button">
                                                <i class="fas fa-times"></i>
                                        </a>
                                        @endif
                                        <button class="btn btn-secondary ml-2" type="submit" id="search-products-btn">
                                        <i class="fas fa-search "></i>
                                    </button>
                                </span>
                                </div>
                            </form>
                        </div>

                    </div>
                    <hr>
                    @if($user->last_ship_date && $user->carrier_id)
                        <div class="table-responsive mt-2" id="users-table-wrapper">
                            <table class="table table-borderless table-striped products-list-table">
                                <thead>
                                    <tr>
                                    <th class="min-width-200">@lang('Product Description')</th>
                                    <th class="min-width-80">@lang('Stem/Unit Price')</th>
                                    <th class="min-width-80" title="How many stems in a bunch UOM">@lang('Unit Pack')</th>
                                    <th class="min-width-80">@lang('Available Qty')</th>
{{--                                    <th class="min-width-80" title="Weight of the item">@lang('Weight')</th>--}}
{{--                                    <th class="min-width-80" title="Size of the item">@lang('Size')</th>--}}
                                    <th class="min-width-80">@lang('Order Qty')</th>
                                    <th class="min-width-100">@lang('Actions')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if (count($products))
                                    @foreach ($products as $index => $product)

                                        <tr>
                                            <td class="align-middle">
                                                <img style="max-width: 35px; cursor: pointer;"
                                                     id="{{$product->id}}imgTD"
                                                     data-id="{{$product->id}}"
                                                     title="Click to show Larger image"
                                                     data-info="{{$product->product_text}}"
                                                     data-toggle="tooltip" data-placement="bottom"
                                                     data-largeimg="{{$product->image_url}}"
                                                     src="{{ asset('assets\img\no-image.png') }}"
                                                     class="img-thumbnail" alt="Virgin Farm">
                                                {{ $product->product_text }}

                                                {!!  $product->is_special ? '<i class="fas fa-bolt text-danger blink" data-toggle="tooltip" data-placement="bottom" title="Special and Seasonal offers"></i>' :'' !!}
                                            </td>

                                            @php $priceNow = round2Digit($product->$priceCol); @endphp
                                            <td class="align-middle" title="Per STEM flowers & Price Column: {{$priceCol}}">${{ $priceNow }}</td>
                                                {{--ST stad for per STEM flowers --}}
                                            <td class="align-middle" title="How many stems in a bunch UOM">{{ $product->stems }}</td>
                                            <td class="align-middle" title="Bunch">{{ $product->quantity }}</td>
{{--                                            <td class="align-middle" title="Weight">{{ $product->weight }}</td>--}}
{{--                                            <td class="align-middle" title="Size">{{ $product->size }}</td>--}}

                                            <form action="{{route('add.to.cart')}}" method="POST" enctype="multipart/form-data">
                                                {{csrf_field()}}

                                                <input type="hidden" name="id" value="{{$product->id}}">
                                                <td class="align-middle">
                                                    <input required class="form-control form-control-sm width50" max="{{$product->quantity}}" name="quantity" type="number" min="0">
                                                </td>

                                                <td class="align-middle">
                                                    @if($priceNow && $product->quantity)
                                                        <button type="submit" class="btn btn-icon"><i
                                                                title="@lang('Add product to cart')" data-toggle="tooltip"
                                                                data-placement="left"
                                                                class="fas fa-plus-circle "></i>
                                                        </button>
                                                    @endif
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
                                        <td colspan="12" style="text-align: center">
                                            <b class="text-danger noRecordText">Choose a Ship date to shop available inventory.</b>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                        {!! $products->render() !!}
                    @else
                        <tr>
                            <div colspan="12" style="text-align: center">
                                <b class="text-danger">Choose a Ship date and carrier to shop available inventory.</b>
                            </div>
                        </tr>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="largeImgModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagePreviewTitle">Large Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img src="" id="imagePreviewId" style="width: 450px; height: 450px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="boxesModal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Boxes Detail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm table-borderless">
                        <thead>
                        <tr>
                            <th scope="col">Box</th>
                            <th scope="col">Approximate Capacity</th>
                        </tr>
                        </thead>
                        <tbody>

                            <tr>
                                <td>Medium</td>
                                <td>6-8 bunches</td>
                            </tr>
                            <tr>
                                <td>Medium Large</td>
                                <td>8-9 bunches</td>
                            </tr>
                            <tr>
                                <td>Large Box</td>
                                <td>10-12 bunches</td>
                            </tr>
                            <tr>
                                <td>Super</td>
                                <td>15-17 bunches</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-gray-500 float-right"> ***Roses used to calculate approximate box capacity.</div>
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

        // Array of dates to be highlighted
        var highlightedDates = @json($highlightedDates);
        if (!Array.isArray(highlightedDates)) {
            highlightedDates = Object.values(highlightedDates);
        }

        $('#date_shipped').datepicker({
            format: 'yyyy-mm-dd',
            startDate: new Date(), // Disable past dates
            beforeShowDay: function(date) {
                var dateString = moment(date).format('YYYY-MM-DD');
                if (highlightedDates.indexOf(dateString) !== -1) {
                    return {
                        classes: 'highlighted-date',
                        tooltip: 'Our inventory is available for this date! Feel free to shop for some beautiful flowers.'
                    };
                }
                return;
            }
        });

        // Initialize Bootstrap tooltips for this one.
        $('body').tooltip({
            selector: '.highlighted-date',
            placement: 'top',
            trigger: 'hover'
        });

        $('.img-thumbnail').click(function () {
            $('#imagePreviewId').attr('src', $(this).data('largeimg'));
            $('#imagePreviewTitle').text($(this).data('info'));
            $('#largeImgModal').modal('show');
        });

        $(".form-check-input").change(function() {

            var selectedSupplier = $(this).val();
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
        });

        previousCarrier = $('#changeCarrier').val();
        $('#changeCarrier').on('change', function () {

            var carrier_id = $(this).val();
            if (!confirm("Are you sure you wish to change carrier?")) {
                $(this).val(previousCarrier);
                return;
            }

            if(carrier_id != 23 && carrier_id != 32){
                swal("Reminder", " Refer to your trucking line's ship date schedule. Please place orders 1 DAY PRIOR before 4 p.m. EST.", "info");
            }

            var date_shipped = $("#date_shipped").val();
            $.ajax({
                url: '{{ route("date-carrier-validation") }}',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    date_shipped,carrier_id
                },
                success: function(response) {
                    if (response.error) {
                        swal("Unavailable for Ship Date & Carrier.", "Please select a later date or change the carrier or contact your sales representative for assistance.", "error");
                        $('#changeCarrier').val(previousCarrier);
                        return '';
                    }
                    else{
                        $.ajax({
                            url: '{{route('carriers.create.update')}}',
                            data: {carrier_id},
                            type: 'POST',
                            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                            success: function (response) {
                                toastr.success("Your carrier has been updated successfully.", "Success");
                                setTimeout(function() {
                                    location.reload();
                                }, 3000); // 3000 milliseconds = 5 seconds
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                }
            });
        });

        $('#date_shipped, #category').change(function (event) {

            if ($(this).attr('id') === 'date_shipped'){

                // event.preventDefault(); // Prevent form submission by default
                //
                // var dateValue = $(this).val();
                // var datePattern = /^\d{4}-\d{2}-\d{2}$/; // Simple regex for YYYY-MM-DD format
                //
                // if (dateValue.match(datePattern)) {
                //     $(this).closest('form').submit(); // Submit the form
                // }

                var dateShipped = $(this).val();
                $.ajax({
                    url: '{{ route("date-carrier-validation") }}',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        date_shipped: dateShipped
                    },
                    success: function(response) {
                        if (response.error) {
                            swal("Unavailable for Ship Date & Carrier.", "Please select a later date or change the carrier or contact your sales representative for assistance.", "error");
                            $('#date_shipped').val(response.old_ship_date);
                            return '';
                        }
                        else
                            $("#filters-form").submit();
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                });
            }
            else{
                $("#filters-form").submit();
            }
        });

        $('#changeAddress').on('change', function () {
            var address_id = this.value;

            $.ajax({
                url: '{{route('ship.address.create.update')}}',
                data: {address_id},
                type: 'POST',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function (response) {
                    toastr.success("Your shiping address has been changed.", "Success");
                }
            });

        });

        $('#add-on-order').on('change', function () {
            var order_id = this.value;
            var _dateShip = $("#date_shipped");

            $.ajax({
                url: '{{route('edit.order.user')}}',
                data: {order_id},
                type: 'POST',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function (response) {
                    let currentDate = new Date().toJSON().slice(0, 10);
                    if(order_id > 1){
                        toastr.success("Your add-on order has been chosen. Please add more items and proceed to checkout to confirm.", "Success");
                    }
                    else{
                        toastr.success("Your add-on setting has been update. Now you can add new order.", "Success");
                    }

                    reloadPageWithParameter('date_shipped', response.date);
                }
            });
        });

        function reloadPageWithParameter(key, value) {
            var baseUrl = window.location.href.split('?')[0]; // Get the base URL without query parameters
            var newParam = key + "=" + encodeURIComponent(value); // Create the parameter string
            var newUrl = baseUrl + "?" + newParam; // Construct the new URL

            // If there are already query parameters, append the new one
            if (window.location.search) {
                newUrl = baseUrl + window.location.search + '&' + newParam;
            }

            window.location.href = newUrl; // Redirect to the new URL
        }

        $( document ).ready(function(){

            const searchParams = new URLSearchParams(window.location.search);

            ['date_shipped' , 'category' , 'searching'].forEach(function(item) {

                var exist = searchParams.has(item);
                if(exist){
                    let param = searchParams.get(item);
                    let id = '#'+item;

                    if(param){
                        var value = $(id).val();
                        if(item == 'category')
                            var value = $(id).find(":selected").text();

                        if(item == 'date_shipped')
                            item = 'date';

                        var valueHere = 'Not available for '+ item +' '+ value;

                        $('.noRecordText').text(valueHere);
                    }
                }
            });
        });

        $(window).on('load', function () {
            $(function(){
                setTimeout(function(){

                    var selectedProductsIds = [];

                    $(".img-thumbnail").each( function() {
                        selectedProductsIds.push($(this).data('id'));
                    });

                    $.ajax({
                        url: "{{ route('get.image.data.ajax') }}",
                        method: 'post',
                        data: {
                            ids:selectedProductsIds.join(",")
                        },
                        success: function (response) {
                            $.each(response,function(id, image) {
                                var idImg = `#${id}imgTD`;
                                if(image)
                                    $(idImg).attr("src",image);
                            });
                        },
                        error: function () {
                            toastr.error('Something went wrong during loading images data.');
                        }
                    });

                }, 250);
            });
        });

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

        //#later when need to make it more accurate VIA AJAX
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
