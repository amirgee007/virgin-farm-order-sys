@php $prodQty = $product->prodQty; @endphp

<tr data-toggle="collapse" data-target="#accordion{{$product->id}}" class="clickable {{$product->is_combo_product ? 'groupProd' : ''}}">
    <td>
        <input type="checkbox" class="product-checkbox" name="product_ids[]" value="{{ $product->id }}">
    </td>
    <td class="align-middle">
        @if(!Request::get('date_in'))
        <span class="badge badge-lg badge-danger" title="Total Rows found for the Inventory bellow.">
            <i class="fa fa-arrow-down" aria-hidden="true"></i>
            {{$prodQty ? count($prodQty) : 0}}
        </span>
        @endif
        {{ @$categories[$product->category_id] }}
    </td>
    <td class="align-middle">
        <x-editable name="item_no" pk="{{ $product->id }}" value="{{ $product->item_no }}" url="{{ route('product.update.column') }}" />
    </td>
    <td class="align-middle" title="Show Dutch or our own products">
        {{ $product->supplier_id == 1 ? 'VF' : 'Dutch' }}
    </td>

    <td class="align-middle">
        <img style="max-width: 35px; cursor: pointer;"
             id="{{$product->id}}imgTD"
             data-id="{{$product->id}}"
             title="Click to show Larger image OR Copy image from any other ITEM"
             data-info="{{$product->product_text}}"
             data-toggle="tooltip" data-placement="bottom"
             data-largeimg="{{$product->image_url}}"
             src="{{ asset('assets\img\no-image.png') }}" class="img-thumbnail" alt="VF Farm">

        <x-editable name="product_text" pk="{{ $product->id }}" value="{{ $product->product_text }}" url="{{ route('product.update.column') }}" />
    </td>

    <td class="align-middle">{{ $product->color_name  }} {{$product->color_sub_class }}</td>

    <td class="align-middle">
        <x-editable name="unit_of_measure" pk="{{ $product->id }}" value="{{ $product->unit_of_measure }}" url="{{ route('product.update.column') }}" />
    </td>

    <td class="align-middle">
        <x-editable name="weight" pk="{{ $product->id }}" type="number" step="any" empty="0" value="{{ $product->weight }}" url="{{ route('product.update.column') }}" />
    </td>

    <td class="align-middle">
        <x-editable name="size" pk="{{ $product->id }}" type="number" step="any" empty="0" value="{{ $product->size }}" url="{{ route('product.update.column') }}" />
    </td>

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
           title="@lang('Mark Product as Combo Group Product')"
           data-toggle="tooltip"
           data-placement="top"
           data-method="DELETE"
           data-confirm-title="@lang('Please Confirm')"
           data-confirm-text="@lang('Are you sure that you want to make this product as combo group product so later we can add many products into it?')"
           data-confirm-delete="@lang('Yes, make it!')">
            <i class="fas fa-arrow-circle-right text-danger"></i>
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
    <td colspan="10">
        <div id="accordion{{$product->id}}" class="collapse">
            <table class="table">
                <thead>
                    <tr>
                        <th class="min-width-80">@lang('#')</th>
                        <th class="min-width-80">@lang('Item')</th>
                        <th class="min-width-80">@lang('Price-FOB $')</th>
                        <th class="min-width-80">@lang('FedEx $')</th>
                        <th class="min-width-80">@lang('HI & AK $')</th>
                        <th class="min-width-80">@lang('FedEx Plus $')</th>
                        <th class="min-width-80">@lang('Quantity')</th>
                        <th class="min-width-80">@lang('Date In')</th>
                        <th class="min-width-80">@lang('Date Out')</th>
                        <th class="min-width-80">@lang('Expire Time')</th>
                    </tr>
                </thead>
                <tbody>

                @if ($product->prodQty)
                    @foreach ($product->prodQty as $index => $prod)
                        @php
                            $requestDateIn = Request::get('date_in');
                            $requestDateOut = Request::get('date_out');
                        @endphp
                        @if(!$requestDateIn || ($prod->date_in == $requestDateIn && $prod->date_out == $requestDateOut))
                            <tr>
                            <td scope="row">{{++$index}}</td>
                            <td scope="row">
                                {{$prod->item_no}}
                                {!!  $prod->is_special ? '<i class="fas fa-bolt text-danger blink" data-toggle="tooltip" data-placement="bottom" title="Special and Seasonal offers"></i>' :'' !!}
                            </td>

                            <td class="align-middle"><x-editable name="price_fob" type="number" step="any" empty="0" pk="{{ $prod->id }}" url="{{ route('product.qty.update.column') }}" value="{{ $prod->price_fob }}" /></td>
                            <td class="align-middle"><x-editable name="price_fedex" type="number" step="any" empty="0" pk="{{ $prod->id }}" url="{{ route('product.qty.update.column') }}" value="{{ $prod->price_fedex }}" /></td>
                            <td class="align-middle"><x-editable name="price_hawaii" type="number" step="any" empty="0" pk="{{ $prod->id }}" url="{{ route('product.qty.update.column') }}" value="{{ $prod->price_hawaii }}" /></td>
                            <td class="align-middle"><x-editable name="price_fedex_2" type="number" step="any" empty="0" pk="{{ $prod->id }}" url="{{ route('product.qty.update.column') }}" value="{{ $prod->price_fedex_2 }}" /></td>
                            <td class="align-middle"><x-editable name="quantity" type="number" empty="0" pk="{{ $prod->id }}" url="{{ route('product.qty.update.column') }}" value="{{ $prod->quantity }}" /></td>

                            <td class="align-middle">{{ $prod->date_in }}</td>
                            <td class="align-middle">{{ $prod->date_out }}</td>
                            <td class="align-middle">{{ $prod->expired_at }}</td>
                        </tr>
                        @endif
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
