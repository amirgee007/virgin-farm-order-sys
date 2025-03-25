<tr data-toggle="collapse" data-target="#accordion{{$order->id}}" class="clickable">
    <td class="align-middle"
        title="Click to view more details about the order."
        data-toggle="tooltip"
        data-placement="left">
        <i class="fa fa-angle-double-down"></i>
        WO{{ $order->id }}
    </td>

    <td class="align-middle">{{ $order->name }}</td>

    <td class="align-middle">{{ $order->date_shipped }}</td>
    <td class="align-middle">{{ @$order->carrier->carrier_name }}</td>
    <td class="align-middle">{{ $order->company }}</td>
    <td class="align-middle">{{ $order->phone }}</td>
    <td class="align-middle">{{ $order->shipping_address }}</td>
    <td class="align-middle text-danger">${{ round2Digit($order->sub_total) }}</td>
    <td class="align-middle text-primary">
        ${{ round2Digit($order->total - $order->discount_applied) }}
        @if($order->discount_applied > 0)
            <span class="text-danger">(-${{ round2Digit($order->discount_applied) }})</span>
        @endif
    </td>
    <td class="align-middle">
        @if($order->is_active == 1)
           <span class="badge badge-lg badge-danger"> Active </span>
        @elseif($order->is_active == 2)
            <span class="badge badge-lg badge-danger"> NotApproved </span>
        @else
            <span class="badge badge-lg badge-primary"> Completed </span>
        @endif
    </td>
    <td class="align-middle">{{ diff4Human($order->created_at) }}</td>
    <td class="align-middle">

        @if($isAdmin)
            @if($order->is_active == 1 || $order->is_active == 2)
                <a href="{{ route('orders.update', [$order->id , 'markCompeted']) }}"
               class="btn btn-icon"
               title="@lang('Mark order as completed')"
               data-toggle="tooltip"
               data-placement="left"
               data-method="GET"
               data-confirm-title="@lang('Please Confirm')"
               data-confirm-text="@lang('Are you sure that you want to mark this order as completed ?')"
               data-confirm-delete="@lang('Yes, complete it!')">
                <i class="fas fa-check-square text-primary"></i>
            </a>
                <a href="{{ route('orders.update', [$order->id , 'markNotApproved']) }}"
                   class="btn btn-icon"
                   title="@lang('Mark order as not approved')"
                   data-toggle="tooltip"
                   data-placement="left"
                   data-method="GET"
                   data-confirm-title="@lang('Please Confirm')"
                   data-confirm-text="@lang('Are you sure to mark this order as as not approved so sales representative will contact client?')"
                   data-confirm-delete="@lang('Yes, confirmed!')">
                    <i class="fas fa-ban text-danger"></i>
                </a>
            @endif
                <i class="fas fa-envelope text-warning" title="@lang('Resend copy of the web order')"
                   data-toggle="tooltip"
                   data-placement="left"
                   data-orderid="{{$order->id}}"
                   data-email="{{$order->email_address}}" style="cursor:pointer;"></i>

                <a href="{{ route('orders.update', [$order->id , 'delete']) }}"
                   class="btn btn-icon"
                   title="@lang('Delete order')"
                   data-toggle="tooltip"
                   data-placement="left"
                   data-method="GET"
                   data-confirm-title="@lang('Please Confirm')"
                   data-confirm-text="@lang('Are you sure that you want to delete this order?')"
                   data-confirm-delete="@lang('Yes, delete it!')">
                    <i class="fas fa-trash text-danger"></i>
                </a>
         @endif
    </td>
</tr>

<tr>
    <td colspan="13">
        <div id="accordion{{$order->id}}" class="collapse">
            <table class="table">
                <thead>
                <tr >
                    <th class="min-width-80">@lang('#')</th>
                    <th class="min-width-80">@lang('Item')</th>
                    <th class="min-width-100">@lang('Product Name')</th>
                    <th class="min-width-100">@lang('Quantity')</th>
                    <th class="min-width-100">@lang('Price')</th>
{{--                    <th class="min-width-100">@lang('Size')</th>--}}
                    <th class="min-width-100">@lang('Stem')</th>
                    <th class="min-width-100">@lang('SubTotal')</th>
                </tr>
                </thead>
                <tbody>

                @if ($order->items)
                    @foreach ($order->items as $index => $prod)
                        <tr class="{{$prod->is_add_on ? 'addON' : ''}}" title="If its silver background it means its add on.">
                            <td scope="row">{{++$index}}</td>
                            <td scope="row">{{$prod->item_no}}</td>
                            <td class="align-middle">{{ $prod->name }}</td>
                            <td class="align-middle">{{ $prod->quantity }}</td>
                            <td class="align-middle">${{ round2Digit($prod->price) }}</td>
{{--                            <td class="align-middle">{{ $prod->size }}</td>--}}
                            <td class="align-middle">{{ $prod->stems }}</td>
                            <td class="align-middle">${{ round2Digit($prod->sub_total) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8"><em>@lang('No product inventory found.')</em></td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </td>
</tr>
