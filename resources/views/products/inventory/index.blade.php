@extends('layouts.app')

@section('page-title', __('Client Inventory'))
@section('page-heading', __('Client Inventory'))

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">
        @lang('Products')
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
                             <label class="form-check-label float-right mt-2 ml-2 text-danger">Ship-To Address
                                 <a target="_blank" class="btn btn-icon" href="{{route('shipping.address.index')}}"
                                    title="@lang('Add New Address')" data-toggle="tooltip" data-placement="top"><i
                                         class="fas fa-plus-circle "></i>
                                 </a>

                                 <select class="form-control form-control-md" id="changeAddress"
                                         title="When do you want your product to be shipped?"
                                         data-trigger="hover"
                                         data-toggle="tooltip">
                                     <option selected value="0">My Default Address</option>
                                     @foreach($address as $add)
                                         <option
                                             {{auth()->user()->address_id == $add->id ? 'selected' : '' }} value="{{$add->id}}">{{$add->address}}</option>
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
                                         <option value="{{$key}}" {{ auth()->user()->carrier_id == $key ? 'selected' : '' }}> {{$name}} </option>
                                     @endforeach
                                 </select>

                             </label>
                        </b>
                    </span>

                    <div class="row my-2 flex-md-row flex-column-reverse">
                        <div class="col-md-12 col-sm-12 mt-md-0 mt-1">
                            <form action="" method="GET" id="filters-form" class="border-bottom-light">
                                <div class="input-group custom-search-form">

                                    <input type="date"
                                           class="form-control rounded"
                                           id="date_shipped"
                                           name="date_shipped"
                                           title="When do you want your product to be shipped?"
                                           data-trigger="hover"
                                           data-toggle="tooltip"
                                           value="{{ \Request::get('date_shipped') }}">

                                    <select class="form-control rounded ml-3" name="category" id="category_id">
                                        <option selected value="">All Categories</option>
                                        @foreach($categories AS $key => $val)
                                            <option value="{{$key}}"
                                                {{ (\Request::get('category') == $key) ? 'selected': ''  }}
                                            >{{$val}}</option>
                                        @endforeach
                                    </select>

                                    <input type="text"
                                           class="form-control rounded ml-2"
                                           placeholder="Search by name, item, variety, grade, color"
                                           name="searching"
                                           value="{{\Request::get('searching')}}">

                                    {{--                                    <input type="text"--}}
                                    {{--                                           class="form-control rounded ml-2"--}}
                                    {{--                                           name="po"--}}
                                    {{--                                           placeholder="What is PO#?"--}}
                                    {{--                                           title="What is your PO#? (optional)"--}}
                                    {{--                                           data-trigger="hover"--}}
                                    {{--                                           data-toggle="tooltip"--}}
                                    {{--                                           value="{{ \Request::get('po') }}">--}}

                                    <span class="input-group-append">
                                        @if (\Request::get('date_shipped') || \Request::get('category') || \Request::get('searching'))
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

                        {{--@permission('orders.filter')--}}
                        @include('products._partial.filter')
                        {{--@endpermission--}}

                    </div>

                    <hr>
                    <div class="table-responsive mt-2" id="users-table-wrapper">
                        <table class="table table-borderless table-striped products-list-table">
                            <thead>
                                <tr>
                                <th class="min-width-200">@lang('Product Description')</th>
                                <th class="min-width-80">@lang('Unit Price')</th>
                                <th class="min-width-80" title="How many stems in a bunch UOM">@lang('Stem/Bunch')</th>
                                <th class="min-width-80">@lang('Quantity')</th>
                                <th class="min-width-80" title="Weight of the item">@lang('Weight')</th>
                                <th class="min-width-80" title="Size of the item">@lang('Size')</th>
                                <th class="min-width-80">@lang('Order Qty(Boxes)')</th>
                                <th class="min-width-80">@lang('Actions')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (count($products))
                                @foreach ($products as $index => $product)
                                    <tr>
                                        <td class="align-middle">
                                            <img style="max-width: 35px; cursor: pointer;"
                                                 title="Click to show Larger image"
                                                 data-info="{{$product->product_text}}"
                                                 data-toggle="tooltip" data-placement="bottom"
                                                 data-largeimg="{{$product->image_url}}"
                                                 src="{{ $product->image_url ? $product->image_url : asset('assets\img\no-image.png') }}"
                                                 class="img-thumbnail" alt="Virgin Farm">
                                            {{ $product->product_text }}

                                            {!!  $product->is_deal ? '<i class="fas fa-bolt text-danger" title="Deal"></i>' :'' !!}
                                        </td>

                                        @php $priceNow = $product->$priceCol; @endphp
                                        <td class="align-middle" title="Per STEM flowers">${{ $priceNow }}/ST</td>
                                            {{--ST stad for per STEM flowers --}}
                                        <td class="align-middle" title="How many stems in a bunch UOM">{{ $product->unit_of_measure }}</td>
                                        <td class="align-middle" title="Bunch">{{ $product->quantity }} BU</td>
                                        <td class="align-middle" title="Weight">{{ $product->weight }}</td>
                                        <td class="align-middle" title="Size">{{ $product->size }}</td>

                                        <form action="{{route('add.to.cart')}}" method="POST"
                                              enctype="multipart/form-data">
                                            {{csrf_field()}}

                                            <input type="hidden" name="product_id" value="{{$product->id}}">
                                            <td class="align-middle">
                                                <input required class="form-control form-control-sm width50" max="{{$product->quantity}}" name="quantity" type="number" min="0">
                                            </td>

                                            <td class="align-middle">
                                                @if($priceNow)
                                                    <button type="submit" class="btn btn-icon"
                                                            title="@lang('Add product to cart')" data-toggle="tooltip"
                                                            data-placement="top"><i class="fas fa-plus-circle "></i>
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
                                        <b class="text-danger">@lang('Choose a Ship date to shop available inventory.')</b>
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
@stop

@section('scripts')
    @include('partials.toaster-js')
    <script>
        $('.img-thumbnail').click(function () {
            $('#imagePreviewId').attr('src', $(this).data('largeimg'));
            $('#imagePreviewTitle').text($(this).data('info'));
            $('#largeImgModal').modal('show');
        });

        $('#date_shipped, #category_id').change(function () {
            var date = $(this).val();
            $("#filters-form").submit();
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


        $('#changeCarrier').on('change', function () {
            var carrier_id = this.value;

            $.ajax({
                url: '{{route('carriers.create.update')}}',
                data: {carrier_id},
                type: 'POST',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function (response) {
                    toastr.success("Your career has been updated successfully.", "Success");
                }
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
