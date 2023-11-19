@extends('layouts.app')

@section('page-title', __('Manage Inventory'))
@section('page-heading', __('Manage Inventory'))

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">
        @lang('Inventory')
    </li>
@stop

@section('styles')

@endsection

@section('content')
    @include('partials.messages')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body mt-0 p-3">

                    <div class="table-responsive mt-2" id="users-table-wrapper">
                        <table class="table table-borderless table-striped products-list-table">
                            <thead>
                            <tr>
                                <th class="min-width-80">@lang('Vendor')</th>
                                <th class="min-width-200">@lang('Product Description')</th>
                                <th class="min-width-80">@lang('Unit Price')</th>
                                <th class="min-width-80">@lang('Stem/Bunch')</th>
                                <th class="min-width-80">@lang('Quantity')</th>
                                <th class="min-width-80">@lang('Box Type')</th>

                                <th class="min-width-80">@lang('Unit/Box')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (count($products))
                                @foreach ($products as $index => $product)
                                    <tr>

                                        <td class="align-middle">{{ $product->vendor }}</td>
                                        <td class="align-middle">
                                            <img style="max-width: 35px; cursor: pointer;"
                                                 title="Click to show Larger image"
                                                 data-toggle="tooltip" data-placement="bottom"
                                                 data-largeimg="{{$product->image_url}}"
                                                 src="{{ $product->image_url ? $product->image_url : asset('assets\img\no-image.png') }}" class="img-thumbnail" alt="Virgin Farm">
                                            {{ $product->product_text }}

                                            {!!  $product->is_deal ? '<i class="fas fa-bolt text-danger" title="Deal"></i>' :'' !!}
                                        </td>

                                        <td class="align-middle">${{ $product->unit_price }}/ST</td>
                                        <td class="align-middle">{{ $product->stems }}</td>
                                        <td class="align-middle">{{ $product->quantity }} BX</td>
                                        <td class="align-middle">{{ $product->box_type }}</td>
                                        <td class="align-middle">{{ $product->box_type }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8"><em>@lang('No address found.')</em></td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    @include('partials.toaster-js')
    <script>
        $('.img-thumbnail').click(function () {
            $('#imagePreviewId').attr('src', $(this).data('largeimg'));
            $('#largeImgModal').modal('show');
        });
    </script>
@endsection
