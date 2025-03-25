@component('mail::message')
# Web Order Summary
## Hey "{{strtoupper($order->name)}}", thanks for your order! Here are the details.

### Company Name: {{$user->company_name}}
##### Customer Account: {{$user->customer_number}}
##### Created On: {{now()->format('m/d/Y h:i:s A')}}
##### Reference: WO{{$order->id}}
--------------------------------------
##### Ship Date: {{$order->date_shipped}}
##### Carrier: {{@$user->carrier->carrier_name}}
##### Ship To: {{$order->ship_to}}

# Prebook {{getAddOnDetail($order)}}
@component('mail::table')
    |      Product     |  Quantity    | Unit Price  |   Unit Pack     |   Total     |
    | :--------------- | :----------  | :---------- | :---------- | :---------- |
    @if(count($order->items))
    @foreach($order->items AS $item)
    | {{$item->name}}  |<small>{{$item->quantity}}</small> &nbsp;| <small>${{round2Digit($item->price)}}</small>|<small>{{$item->stems}}</small>|<small>${{round2Digit($item->sub_total)}}</small>|
    @endforeach
    | <strong>Summary</strong>   |    |  |  |
    |    |    |  <small>Subtotal:</small>| <small>${{round2Digit($order->sub_total) }}</small> |
    |    |    |  <small>Service/Transportation:</small>| <small>${{round2Digit($order->shipping_cost)}}</small> |
    @if($order->discount_applied > 0)
    |    |    |  <small>Promo Code Applied:</small> | <small class="text-success">- ${{ round2Digit($order->discount_applied) }}</small> |
    @endif
    |    |    |  <small>Taxes:</small>| <small>$0</small> |
    |    |    |  <strong>Total</strong> | <strong>${{ round2Digit($order->total - $order->discount_applied) }}</strong> |
    @if(isDeliveryChargesApply())
    |    |    |      <small>**Delivery charges may apply.</small> |     |
    @endif
    @endif
@endcomponent

# Total Units: {{$order->countQty()}}
##### Order Notes: {{$order->notes}}
Your sales representative will contact you to confirm your online order.

If you have any questions, feel free to contact us.
##### sales@virginfarms.com
1-888-548-7673

Thanks,
Virgin Farms Order System
@endcomponent
