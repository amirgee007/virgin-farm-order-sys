<tr data-toggle="collapse" data-target="#accordion{{$order->id}}" class="clickable">
    <td class="align-middle">{{ $order->id }}</td>

    <td class="align-middle">{{ $order->name }}</td>

    <td class="align-middle">{{ $order->date_shipped }}</td>
    <td class="align-middle">{{ @$order->carrier->carrier_name }}</td>
    <td class="align-middle">{{ $order->company }}</td>
    <td class="align-middle">{{ $order->phone }}</td>
    <td class="align-middle">{{ $order->shipping_address }}</td>
    <td class="align-middle">{{ $order->sub_total }}</td>
    <td class="align-middle">0</td>
    <td class="align-middle">{{ $order->total }}</td>
    <td class="align-middle">{{ $order->size }}</td>
    <td class="align-middle">
       <span class="badge badge-lg badge-primary">
           Active
       </span>
    </td>
    <td class="align-middle">{{ diff4Human($order->created_at) }}</td>
</tr>

<tr>
    <td colspan="7">
        <div id="accordion{{$order->id}}" class="collapse">

            <table class="table">
                <thead>
                <tr>
                    <th class="min-width-80">@lang('#')</th>
                    <th class="min-width-80">@lang('Item')</th>
                    <th class="min-width-100">@lang('Product')</th>
                    <th class="min-width-100">@lang('Quantity')</th>
                    <th class="min-width-100">@lang('Price')</th>
                    <th class="min-width-100">@lang('Size')</th>
                    <th class="min-width-100">@lang('Stem')</th>
                    <th class="min-width-100">@lang('SubTotal')</th>
                </tr>
                </thead>
                <tbody>

                @if ($order->items)
                    @foreach ($order->items as $index => $prod)
                        <tr>
                            <td scope="row">{{++$index}}</td>
                            <td scope="row">{{$prod->item_no}}</td>
                            <td class="align-middle">{{ $prod->quantity }}</td>
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
