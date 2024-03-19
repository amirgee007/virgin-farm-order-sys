@extends('layouts.app')

@section('page-title', __('Cart'))
@section('page-heading', __('Cart'))

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">
        @lang('Order Summary') Virgin Farm.
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

        .thumbnail {
            position: relative;
            padding: 0px;
            margin-bottom: 20px;
        }
        .thumbnail img {
            width: 80%;
        }
        .thumbnail .caption{
            margin: 7px;
        }
        .main-section{
            background-color: #F8F8F8;
        }
        .dropdown{
            float:right;
            padding-right: 30px;
        }
        .btn{
            border:0px;
            margin:10px 0px;
            box-shadow:none !important;
        }
        .dropdown .dropdown-menu{
            padding:20px;
            top:30px !important;
            width:350px !important;
            left:-110px !important;
            box-shadow:0px 5px 30px black;
        }
        .total-header-section{
            border-bottom:1px solid #d2d2d2;
        }
        .total-section p{
            margin-bottom:20px;
        }
        .cart-detail{
            padding:15px 0px;
        }
        .cart-detail-img img{
            width:100%;
            height:100%;
            padding-left:15px;
        }
        .cart-detail-product p{
            margin:0px;
            color:#000;
            font-weight:500;
        }
        .cart-detail .price{
            font-size:12px;
            margin-right:10px;
            font-weight:500;
        }
        .cart-detail .count{
            color:#C2C2DC;
        }
        .checkout{
            border-top:1px solid #d2d2d2;
            padding-top: 10px;
        }
        .checkout .btn-primary{
            border-radius:35px;
            height:36px;
        }
        .checkout .btn-danger{
            border-radius:35px;
            height:36px;
        }


    </style>
@endsection

@section('content')

    @include('partials.messages')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body mt-0 p-3">
                    <table id="cart" class="table table-hover table-condensed">
                        <thead>
                        <tr>
                            <th style="width:50%">Product</th>
                            <th style="width:10%">Price</th>
{{--                            <th style="width:4%">Size</th>--}}
                            <th style="width:8%">Quantity</th>
                            <th style="width:22%" class="text-center">Subtotal</th>
                            <th style="width:10%">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php $total = 0; $size = 0 @endphp
                        @if($carts)
                            @foreach($carts as $cartItem)
                                @php
                                    $total += ($cartItem->price * $cartItem->quantity * $cartItem->stems);
                                    $size += $cartItem->size * $cartItem->quantity;
                                @endphp

                                <tr data-id="{{ $cartItem->id }}">
                                    <td data-th="Product">
                                        <div class="row">
                                            <div class="col-sm-3 hidden-xs mt-2"><img src="{{ $cartItem->image }}" style="max-width: 40px;" class="img-responsive"/></div>
                                            <div class="col-sm-9 mt-2">
                                                <h4 class="nomargin">{{ $cartItem->name }} Pack {{ $cartItem->stems }}</h4>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-th="Price">${{ $cartItem->price }} * {{$cartItem->stems}}</td>
{{--                                    <td data-th="Price">{{ $cartItem->size }}</td>--}}
                                    <td data-th="Quantity">
                                        <input type="number" max="{{$cartItem->max_qty}}" onkeydown="return false" value="{{ $cartItem->quantity }}" class="form-control quantity change-cart-qty" />
                                    </td>

                                    <td data-th="Subtotal" class="text-center">${{ round2Digit($cartItem->price * $cartItem->quantity * $cartItem->stems) }}</td>
                                    <td class="actions" data-th="" title="Remove from cat">
                                        <button class="btn btn-danger btn-sm remove-from-cart"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                        <tfoot>

                        @php
                            $cubeSizes = getCubeRanges($size);
                            $total = round2Digit($total);
                        @endphp

{{--                        Box Size(s): Medium   Weight: 30 cu.--}}

                        @if($cubeSizes)
                            @if(is_array($cubeSizes))
                                <tr>
                                    <td colspan="5" class="text-center text-primary"><h4>Box Size(s): {{implode(', ' ,$cubeSizes)}} &nbsp; Weight:{{$size}} cu.</h4></td>
                                </tr>
                            @endif

                            <tr>
                                <td colspan="5" class="text-right"><h4><strong>Order Subtotal: ${{ $total }}</strong></h4></td>
                            </tr>
                            @php
                                 $totalCubeTax = getCubeSizeTax($size);
                                  $orderTotal =  round2Digit($total + $totalCubeTax);
                            @endphp
                            <tr>
                                <td colspan="5" class="text-right"><h4><strong>Service/Transportation: ${{$totalCubeTax}}</strong></h4></td>
                            </tr>

                            <tr>
                                <td colspan="5" class="text-right"><h4><strong>Tax $0</strong></h4></td>
                            </tr>

                            <tr>
                                <td colspan="5" class="text-right"><h3><strong>Order Total: ${{ $orderTotal }}</strong></h3></td>
                            </tr>

                            @if(isDeliveryChargesApply())
                                <tr>
                                    <td colspan="5" class="text-right text-danger"><h5>**Delivery charges may apply.</h5></td>
                                </tr>
                            @endif

                            <tr>
                                <td colspan="5" class="text-right">
                                <a href="{{ route('inventory.index') }}" class="btn btn-danger"><i class="fa fa-angle-left"></i> Continue Shopping</a>

                                <a href="{{ route('checkout.cart') }}"
                                   class="btn btn-primary"
                                   title="@lang('Checkout and Confirm the Order')"
                                   data-toggle="tooltip"
                                   data-placement="top"
                                   data-method="GET"
                                   data-confirm-title="@lang('Please Confirm To Proceed?')"
                                   data-confirm-text="@lang('Once you checkout you can no longer change this order?')"
                                   data-confirm-delete="@lang('Yes, Proceed!')">
                                    Checkout &nbsp;<i class="fa fa-angle-right"></i>
                                </a>

                            </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="5" class="text-center text-danger ">
                                    <h3><strong>You are not matching with cube size please select few more flowers i.e size now {{$size}}</strong></h3>
                                    <a href="{{ route('inventory.index') }}" class="btn btn-danger"><i class="fa fa-angle-left"></i> Continue Shopping</a>
                                </td>
                            </tr>

                        @endif
                        </tfoot>
                    </table>
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

    <script type="text/javascript">

        $(".change-cart-qty").change(function (e) {
            e.preventDefault();

            var ele = $(this);

            $.ajax({
                url: '{{ route('change.cart.qty') }}',
                method: "patch",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: ele.parents("tr").attr("data-id"),
                    quantity: ele.parents("tr").find(".quantity").val()
                },
                success: function (response) {
                    window.location.reload();
                }
            });
        });

        $(".remove-from-cart").click(function (e) {
            e.preventDefault();

            var ele = $(this);

            if(confirm("Are you sure want to remove?")) {
                $.ajax({
                    url: '{{ route('remove.from.cart') }}',
                    method: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: ele.parents("tr").attr("data-id")
                    },
                    success: function (response) {
                        window.location.reload();
                    }
                });
            }
        });

    </script>
@endsection
