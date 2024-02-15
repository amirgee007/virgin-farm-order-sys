@component('mail::message')
# Web Order Summary
## Hey "{{strtoupper($order->name)}}", thanks for your order! Here are the details.

### Customer Name: {{$user->name}}
##### Customer Account: {{$user->customer_number}}
##### Created On: {{now()->format('m/d/Y h:i:s A')}}
--------------------------------------
##### Ship Date: {{$order->date_shipped}}
##### Carrier: {{@$user->carrier->carrier_name}}
##### Ship To: {{$order->ship_to}}

# Prebook
@component('mail::table')
    |      Product     |  Quantity    | Unit Price/Stem  |   Total     |
    | :--------------- | :----------  | :---------- | :---------- |
    @if(count($order->items))
    @foreach($order->items AS $item)
    | {{$item->name}}  |<small>{{$item->quantity}}</small> &nbsp;| <small>${{$item->price}} * {{$item->size}}</small>|<small>${{$item->sub_total}}</small>|
    @endforeach
    | <strong>Summary</strong>   |    |  |  |
    |    |    |  <small>Subtotal:</small>| <small>${{$order->total }}</small> |
    |    |    |  <small>Service/Transportation:</small>| <small>${{$order->shipping_cost}}</small> |
    |    |    |  <small>Taxes:</small>| <small>$0</small> |
    |    |    |     Total             | <small>${{$order->total }}</small> |
    @endif
@endcomponent

# Total Boxes: {{count($order->items)}}

Your sales reperentative will contact you to confirm your online order.

If you have any questions, feel free to contact us.
##### sales@virginfarms.net
1-888-548-7673

Thanks,
{{ config('app.name') }}
@endcomponent
