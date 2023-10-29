@component('mail::message')

    # @lang('Hello!')

    @lang('New user was just registered on :app website.', ['app' => setting('app_name')])


    @lang('To view the user details just visit the link below.')


    @component('mail::table')
        |           |                |                  |           |
        | :-------- |:-------------  | :--------------- | :-------- |
        {{--@if(count($orderedItems))--}}
            {{--@foreach($orderedItems AS $item)--}}
                {{--| {{$item['quantity']}} st.  |<small>{{$item['product_id']}}</small> &nbsp;&nbsp;| <small>{{$item['title']}}</small>|<small>{{$item['price']}} {{$order['currency']}}</small>|--}}
            {{--@endforeach--}}
            {{--|    |    |  <small>Total:</small>| <small>{{@$order['total'].' '.$order['currency'] }}</small> |--}}

        {{--@endif--}}
    @endcomponent


    @lang('Regards'),<br>
    {{ setting('app_name') }}

@endcomponent


{{--<p>--}}
    {{--{{$order['customer_first_name'] .' '. $order['customer_last_name']}}<br />--}}
    {{--{{$order['customer_address']}} {{$order['customer_address_2']}}<br />--}}
    {{--{{$order['customer_zip'].', '.$order['customer_city']}} <br>--}}
    {{--@lang('order.'.$order['customer_country_code'])--}}
{{--</p>--}}
