@component('mail::message')
    # Order Confirmation

    Thank you for your order! Here are the details:

    **Order ID:** {{ $order->id }}
    **Total Amount:** ${{ $order->total }}

    **Items:**
    @foreach ($order->items as $item)
        - {{ $item->name }} ({{ $item->quantity }}) - ${{ $item->price }}
    @endforeach

    If you have any questions, feel free to contact us.

    Thanks,
    {{ config('app.name') }}
@endcomponent
