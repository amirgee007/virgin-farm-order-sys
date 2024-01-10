<tr>
    <td class="align-middle">{{ @$categories[$product->category_id] }}</td>
    <td class="align-middle" title="{{$product->id}}">{{ $product->item_no }}</td>
    <td class="align-middle">
        <img style="max-width: 35px; cursor: pointer;"
             id="{{$product->id}}"
             title="Click to show Larger image OR Copy image from any other ITEM"
             data-info="{{$product->product_text}}"
             data-toggle="tooltip" data-placement="bottom"
             data-largeimg="{{$product->image_url}}"
             src="{{ $product->image_url ? $product->image_url : asset('assets\img\no-image.png') }}" class="img-thumbnail" alt="Virgin Farm">
        {{ $product->product_text }}

        {!!  $product->is_deal ? '<i class="fas fa-bolt text-danger" title="Deal"></i>' :'' !!}
    </td>

    <td class="align-middle">{{ $product->unit_of_measure }}</td>

    <td class="align-middle">
        <a class="editable"
           style="cursor:pointer;"
           data-name="price_fob"
           data-step="any"
           data-type="number"
           data-emptytext="0"
           data-pk="{{$product->id}}"
           data-url="{{route('inventory.update.column')}}"
           data-value="{{ $product->price_fob }}">
        </a>
    </td>

    <td class="align-middle">
        <a class="editable"
           style="cursor:pointer;"
           data-name="price_fedex"
           data-step="any"
           data-type="number"
           data-emptytext="0"
           data-pk="{{$product->id}}"
           data-url="{{route('inventory.update.column')}}"
           data-value="{{ $product->price_fedex }}">
        </a>
    </td>

    <td class="align-middle">
        <a class="editable"
           style="cursor:pointer;"
           data-name="price_hawaii"
           data-step="any"
           data-type="number"
           data-emptytext="0"
           data-pk="{{$product->id}}"
           data-url="{{route('inventory.update.column')}}"
           data-value="{{ $product->price_hawaii }}">
        </a>
    </td>


    {{--                                        <td class="align-middle">${{ $product->price_fedex }}</td>--}}
    {{--                                        <td class="align-middle">${{ $product->price_fob }}</td>--}}
    {{--                                        <td class="align-middle">${{ $product->price_hawaii }}</td>--}}

    <td class="align-middle">{{ $product->weight }}</td>
    <td class="align-middle">{{ $product->size }}</td>
    <td class="align-middle">{{ $product->quantity }}</td>
    <td class="align-middle">{{ $product->date_in }}</td>
    <td class="align-middle">{{ $product->date_out }}</td>
    <td class="align-middle">
        <a href="{{ route('products.delete', $product->id) }}"
           class="btn btn-icon"
           title="@lang('Delete Product')"
           data-toggle="tooltip"
           data-placement="top"
           data-method="DELETE"
           data-confirm-title="@lang('Please Confirm')"
           data-confirm-text="@lang('Are you sure that you want to delete this product?')"
           data-confirm-delete="@lang('Yes, delete it!')">
            <i class="fas fa-trash"></i>
        </a>
    </td>
</tr>
