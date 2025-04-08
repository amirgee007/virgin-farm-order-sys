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

        .width50 {
            width: 60px !important;
        }
        .thumbnail {
            position: relative;
            padding: 0px;
            margin-bottom: 20px;
        }

        .thumbnail img {
            width: 80%;
        }

        .thumbnail .caption {
            margin: 7px;
        }

        .main-section {
            background-color: #F8F8F8;
        }

        .dropdown {
            float: right;
            padding-right: 30px;
        }

        .btn {
            border: 0px;
            margin: 10px 0px;
            box-shadow: none !important;
        }

        .dropdown .dropdown-menu {
            padding: 20px;
            top: 30px !important;
            width: 350px !important;
            left: -110px !important;
            box-shadow: 0px 5px 30px black;
        }

        .total-header-section {
            border-bottom: 1px solid #d2d2d2;
        }

        .total-section p {
            margin-bottom: 20px;
        }

        .cart-detail {
            padding: 15px 0px;
        }

        .cart-detail-img img {
            width: 100%;
            height: 100%;
            padding-left: 15px;
        }

        .cart-detail-product p {
            margin: 0px;
            color: #000;
            font-weight: 500;
        }

        .cart-detail .price {
            font-size: 12px;
            margin-right: 10px;
            font-weight: 500;
        }

        .cart-detail .count {
            color: #C2C2DC;
        }

        .checkout {
            border-top: 1px solid #d2d2d2;
            padding-top: 10px;
        }

        .checkout .btn-primary {
            border-radius: 35px;
            height: 36px;
        }

        .checkout .btn-danger {
            border-radius: 35px;
            height: 36px;
        }


        /* Mobile responsiveness */
        @media (max-width: 768px) {
            /* Table responsiveness */
            .table-responsive {
                overflow-x: auto;
            }

            .products-list-table {
                font-size: 12px !important;
            }

            .products-list-table th, .products-list-table td {
                white-space: nowrap;
            }

            .pagination {
                justify-content: center;
            }

            /* Hide unnecessary elements on smaller screens */
            .breadcrumb-item, .page-header {
                display: none !important;
            }

            /* Adjust product name font size */
            .products-list-table td h4.nomargin {
                font-size: 14px !important;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 150px;
            }

            .dropdown .dropdown-menu {
                width: 310px !important;
                max-height: 450px!important;
            }
        }

        /* Further improvements for very small screens */
        @media (max-width: 480px) {
            /* Full-width buttons */
            .btn {
                width: 100%;
                margin-top: 10px;
            }

            /* Adjust product image size */
            .products-list-table td img {
                width: 100% !important;
                max-width: 40px;
            }

            /* Hide breadcrumb and page headers */
            .breadcrumb-item, .page-header {
                display: none;
            }

            /* Further adjustment for very small screen for product titles */
            .products-list-table td h4.nomargin {
                font-size: 12px !important;
                max-width: 100px;
            }

            .dropdown .dropdown-menu {
                width: 310px !important;
                max-height: 400px!important;
            }
        }

        /*!* Force the number input spinner to show on mobile *!*/
        /*input[type="number"] {*/
        /*    -moz-appearance: textfield; !* Firefox *!*/
        /*    -webkit-appearance: none;   !* Safari and Chrome *!*/
        /*    appearance: none;           !* Standard syntax *!*/
        /*    margin: 0;*/
        /*}*/

        /*!* Add increment/decrement buttons for webkit-based browsers like Chrome *!*/
        /*input[type="number"]::-webkit-inner-spin-button,*/
        /*input[type="number"]::-webkit-outer-spin-button {*/
        /*    -webkit-appearance: inner-spin-button;*/
        /*    margin: 0;*/
        /*}*/

        @media (max-width: 576px) {
            .promo-code-section .input-group {
                flex-direction: column;
                align-items: stretch;
            }

            .promo-code-section .input-group .form-control,
            .promo-code-section .input-group .input-group-append {
                width: 100%;
            }

            .promo-code-section .input-group .input-group-append button {
                width: 100%;
                margin-top: 5px;
            }
        }



    </style>
@endsection

@section('content')

    @php $orderId = auth()->user()->edit_order_id; @endphp

    @include('partials.messages')
    @if($orderId)
        <div class="text-center">
            <h3>✅ Add-On Order (<small>{{$orderId > 1 ? '#W-'.$orderId : 'General'}}</small>)</h3>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body mt-0 p-3">
                    <div class="table-responsive">
                        <table id="cart" class="table table-hover table-bordered border-success products-list-table">
                            <thead>
                            <tr>
                                <th style="width:50%">Product</th>
                                <th style="width:10%">Stem/Unit Price</th>
                                <th style="width:4%">Unit Pack</th>
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
                                                <div class="col-sm-3 hidden-xs mt-2">
                                                    <img src="{{ $cartItem->image }}" class="img-responsive" style="max-width: 40px;" />
                                                </div>
                                                <div class="col-sm-9 mt-2">
                                                    <h4 class="nomargin">{{ $cartItem->name }} Pack {{ $cartItem->stems }}</h4>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-th="Price">${{ round2Digit($cartItem->price) }}</td>
                                        <td data-th="Stems">{{$cartItem->stems}}</td>
                                        <td data-th="Quantity">
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-secondary btn-sm decrement-qty"><span class="text-danger">-</span></button>
                                                <input type="number" min="1" max="{{$cartItem->max_qty}}"  onkeydown="return false" value="{{ $cartItem->quantity }}" class="form-control quantity change-cart-qty"/>
                                                <button type="button" class="btn btn-outline-secondary btn-sm increment-qty"><span class="text-primary">+</span></button>
                                            </div>
                                        </td>
                                        <td data-th="Subtotal" class="text-center">
                                            ${{ round2Digit($cartItem->price * $cartItem->quantity * $cartItem->stems) }}
                                        </td>
                                        <td class="actions" data-th="" title="Remove from cart">
                                            <button class="btn btn-danger btn-sm remove-from-cart"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>

                    @php
                        $boxeInfoDetail = getCubeRangesV2($size);
                        $total = round2Digit($total);
                    @endphp

                    @if(@$boxeInfoDetail['boxMatched'])
                        <div class="row">
                            <div class="col-md-6">
                                <label>Order Notes(Optional)</label>
                                <textarea class="form-control" id="order-notes" rows="4" onblur="saveOrderNote()"
                                          placeholder="leave comments or notes for your sales representative."></textarea>
                                <br>
                                <div class="notes-danger">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>

                                    Please note that online orders are subject to availability and final confirmation.
                                    <br/>
                                    <br/>
                                    While we strive to fulfill every request, there are instances in which an order may
                                    need to be adjusted. We appreciate your understanding.
                                </div>

                            </div>

                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <td colspan="5" class="text-center text-primary">
                                            <h4>Box Size(s): {{ @$boxeInfoDetail['boxMatched'] }} &nbsp; Weight: {{$size}} cu.</h4>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right">
                                            <h5><strong>Order Subtotal: ${{ $total }}</strong></h5>
                                        </td>
                                    </tr>
                                    @php
                                        $promoData = getApplicablePromoDiscount(auth()->user(), $total , $size);

                                        $discount_applied = 0;
                                        $promo_code_name = null;
                                        if ($promoData['discountAmount']) {
                                            $discount_applied = $promoData['discountAmount'];
                                            $total = $total - $discount_applied;
                                            $promo_code_name = $promoData['promoCodeName'];
                                        }

                                        $totalCubeTax = getCubeSizeTax($size);
                                        $orderTotal =  round2Digit($total + $totalCubeTax);
                                    @endphp
                                    <tr>
                                        <td colspan="2">
                                            <h5>Shipping Carrier: <b>{{ auth()->user()->carrier->carrier_name }}</b></h5>
                                        </td>
                                        <td colspan="3" class="text-right">
                                            <h5><strong>Service/Transportation: ${{ $totalCubeTax }}</strong></h5>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="5" class="text-right">
                                            <h5><strong>Tax $0</strong></h5>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="2">
                                            <div class="promo-code-section">
                                                <div class="form-group">
                                                    <label for="promo-code"><i class="fas fa-gift text-danger"></i> Have a promo code?</label>
                                                    <div class="row">
                                                        <div class="col-12 col-md-8 mb-2 mb-md-0 mt-2">
                                                            <input type="text" id="promo-code" class="form-control" placeholder="Enter promo code">
                                                        </div>
                                                        <div class="col-12 col-md-4">
                                                            <button class="btn btn-danger btn-block apply-promo">Apply</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <small class="text-danger promo-error d-none">Invalid promo code. Please try again.</small>
                                                <small class="text-success promo-success d-none">Promo code applied successfully!</small>
                                            </div>
                                        </td>

                                        <td colspan="2" class="text-right">
                                            <h4><strong>Order Total: <span class="text-danger" id="order-total">${{ $orderTotal }}</span></strong></h4>
                                            <p class="text-success {{$discount_applied >0 ? '' : 'd-none'}}" id="applied-discount-info">
                                                ✅ Discount Applied: <span id="discount-amount">{{$discount_applied}}</span>
                                            </p>
                                            <small>({{$promo_code_name}})</small>
                                        </td>
                                    </tr>

                                    @if(isDeliveryChargesApply())
                                        <tr>
                                            <td colspan="5" class="text-right text-danger">
                                                <h5>**Delivery charges may apply.</h5>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="5" class="text-right">
                                            <a href="{{ route('inventory.index') }}" class="btn btn-danger"><i
                                                    class="fa fa-angle-left"></i> Continue Shopping</a>
                                            <a href="{{ route('checkout.cart') }}" class="btn btn-primary"
                                               title="@lang('Checkout and Confirm the Order')" data-toggle="tooltip"
                                               data-placement="top" data-method="GET"
                                               data-confirm-title="@lang('Please Confirm To Proceed?')"
                                               data-confirm-text="@lang('Once you check out you can no longer change this order..!')"
                                               data-confirm-delete="@lang('Yes, Proceed!')">
                                                Checkout &nbsp;<i class="fa fa-angle-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-danger">
                            <h3><strong>Please select more products to fill your box. </strong></h3>
                            <a href="{{ route('inventory.index') }}" class="btn btn-danger"><i
                                    class="fa fa-angle-left"></i> Continue Shopping</a>
                        </div>
                    @endif

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
                    <img src="" id="imagePreviewId" style="width: 650px; height: 400px;">
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

            if (confirm("Are you sure want to remove?")) {
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

        function saveOrderNote() {
            var textValue = $('#order-notes').val().trim();
            if (textValue !== '') {
                $.ajax({
                    url: '{{ route('cart.save.notes') }}',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({notes: textValue}),
                    success: function () {
                        console.log('Text sent successfully');
                    },
                    error: function () {
                        console.log('Error sending text');
                    }
                });
            }
        }

        $(document).ready(function () {
            // Handle increment button
            $('.increment-qty').on('click', function () {
                var input = $(this).siblings('.quantity');
                var currentValue = parseInt(input.val(), 10);
                var maxValue = parseInt(input.attr('max'), 10);
                if (currentValue < maxValue) {
                    input.val(currentValue + 1).trigger('change');
                }
            });

            // Handle decrement button
            $('.decrement-qty').on('click', function () {
                var input = $(this).siblings('.quantity');
                var currentValue = parseInt(input.val(), 10);
                var minValue = parseInt(input.attr('min'), 10);
                if (currentValue > minValue) {
                    input.val(currentValue - 1).trigger('change');
                }
            });

            $('.apply-promo').on('click', function () {
                var promoCode = $('#promo-code').val().trim();
                var total_amount = $('#total_amount_promo').val().trim();

                if (promoCode === '') {
                    $('.promo-error').removeClass('d-none').text("Please enter a promo code.");
                    $('.promo-success').addClass('d-none');
                    return;
                }

                $.ajax({
                    url: '{{ route("apply.promo") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        promo_code: promoCode,
                        total_amount: total_amount,
                    },
                    success: function (response) {
                        if (response.success) {
                            // Show success message
                            $('.promo-success').removeClass('d-none').text(response.message);
                            $('.promo-error').addClass('d-none');

                            // Update the Order Total & Show Discount Info
                            var newTotal = response.new_total; // Backend should return updated total
                            var discountAmount = response.discount; // Discount applied

                            $('#order-total').text('$' + newTotal.toFixed(2)); // Update Order Total
                            $('#discount-amount').text('-$' + discountAmount.toFixed(2)); // Show Discount Amount
                            $('#applied-discount-info').removeClass('d-none'); // Show Discount Info
                        } else {
                            $('.promo-error').removeClass('d-none').text(response.message);
                            $('.promo-success').addClass('d-none');
                        }
                    },
                    error: function () {
                        $('.promo-error').removeClass('d-none').text("Invalid or expired promo code.");
                        $('.promo-success').addClass('d-none');
                    }
                });
            });

        });

    </script>


@endsection
