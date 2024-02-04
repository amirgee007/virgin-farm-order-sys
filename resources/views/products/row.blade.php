<tr data-toggle="collapse" data-target="#accordion{{$product->id}}" class="clickable">
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

    <td class="align-middle">{{ $product->weight }}</td>
    <td class="align-middle">{{ $product->size }}</td>
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
            <i class="fas fa-trash text-danger"></i>
        </a>

        <a href="{{ route('products.reset', $product->id) }}"
           class="btn btn-icon"
           title="@lang('Reset Product Image')"
           data-toggle="tooltip"
           data-placement="top"
           data-method="DELETE"
           data-confirm-title="@lang('Please Confirm')"
           data-confirm-text="@lang('Are you sure that you want to reset this product image?')"
           data-confirm-delete="@lang('Yes, reset it!')">
            <i class="fas fa-sync text-primary"></i>
        </a>
    </td>
</tr>

<tr>
    <td colspan="7">
        <div id="accordion{{$product->id}}" class="collapse">

            <table class="table">
                <thead>
                <tr>
                    <th class="min-width-80">@lang('#')</th>
                    <th class="min-width-80">@lang('Item')</th>
                    <th class="min-width-80">@lang('Price-FOB $')</th>
                    <th class="min-width-80">@lang('FedEx $')</th>
                    <th class="min-width-80">@lang('HI & AK $')</th>
                    <th class="min-width-80">@lang('Quantity')</th>
                    <th class="min-width-80">@lang('Date In')</th>
                    <th class="min-width-80">@lang('Date Out')</th>
                    {{--<th scope="col">#</th>--}}
                </tr>
                </thead>
                <tbody>

                @if ($product->prodQty)
                    @foreach ($product->prodQty as $index => $prod)
                        <tr>
                            <td scope="row">{{++$index}}</td>
                            <td scope="row">{{$prod->item_no}}</td>
                            <td class="align-middle">
                                <a class="editable"
                                   style="cursor:pointer;"
                                   data-name="price_fob"
                                   data-step="any"
                                   data-type="number"
                                   data-emptytext="0"
                                   data-pk="{{$prod->id}}"
                                   data-url="{{route('product.update.column')}}"
                                   data-value="{{ $prod->price_fob }}">
                                </a>
                            </td>

                            <td class="align-middle">
                                <a class="editable"
                                   style="cursor:pointer;"
                                   data-name="price_fedex"
                                   data-step="any"
                                   data-type="number"
                                   data-emptytext="0"
                                   data-pk="{{$prod->id}}"
                                   data-url="{{route('product.update.column')}}"
                                   data-value="{{ $prod->price_fedex }}">
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
                                   data-url="{{route('product.update.column')}}"
                                   data-value="{{ $prod->price_hawaii }}">
                                </a>
                            </td>

                            <td class="align-middle">{{ $prod->quantity }}</td>
                            <td class="align-middle">{{ $prod->date_in }}</td>
                            <td class="align-middle">{{ $prod->date_out }}</td>
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
