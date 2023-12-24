@extends('layouts.app')

@section('page-title', __('Manage Inventory'))
@section('page-heading', __('Manage Inventory'))

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">
        @lang('Inventory')
    </li>
@stop

@section('styles')
    <link media="all" type="text/css" rel="stylesheet" href="{{ url('assets/css/custom.css') }}">
@endsection

@section('content')
    @include('partials.messages')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body mt-0 p-3">

                    <div class="notes-success" style="">
                        <p>Total products are in the system are.
                            <b>
                                {{$count}}
                            </b>

                            <a href="javascript:void(0)" id="import_excel_inventory" title="Upload excel file to refresh inventory" data-toggle="tooltip" data-placement="left"
                               class="btn btn-primary btn-sm float-right ml-2 mr-1">
                                <i class="fas fa-upload"></i>
                            </a>

                            <a href="javascript:void(0)" id="import_excel_products" title="Upload products file i.e Web item Masters with Class This is the basis of all products." data-toggle="tooltip" data-placement="left"
                               class="btn btn-danger btn-sm float-right ml-2 mr-1">
                                <i class="fas fa-upload"></i>
                            </a>

                            <a href="javascript:void(0)" id="import_excel_images" title="Upload products images file as zip with SKU name" data-toggle="tooltip" data-placement="left"
                               class="btn btn-warning btn-sm float-right ml-2 mr-1">
                                <i class="fas fa-upload"></i>
                            </a>
                        </p>
                    </div>

                    <form action="" method="GET" id="product-form" class="pb-2 mb-3 border-bottom-light">
                        <div class="row my-2 flex-md-row flex-column-reverse">
                            <div class="col-md-6 mt-md-0 mt-2">
                                <div class="input-group custom-search-form">
                                    <input type="text"
                                           class="form-control input-solid"
                                           name="search"
                                           value="{{ Request::get('search') }}"
                                           placeholder="Search by Item, Description">

                                    <span class="input-group-append">
                                    @if (Request::has('search') && Request::get('search') != '')
                                            <a href="{{ route('inventory.index') }}"
                                               class="btn btn-light d-flex align-items-center text-muted"
                                               role="button">
                                                <i class="fas fa-times"></i>
                                        </a>
                                   @endif
                                    <button class="btn btn-light" type="submit">
                                              <i class="fas fa-search text-muted"></i>
                                    </button>
                                </span>
                                </div>
                            </div>

                        </div>
                    </form>

                    <div class="table-responsive mt-2" id="users-table-wrapper">
                        <table class="table table-borderless table-striped products-list-table">
                            <thead>
                            <tr>
                                <th class="min-width-80">@lang('Category')</th>
                                <th class="min-width-80">@lang('item')</th>
                                <th class="min-width-200">@lang('Product Description')</th>
                                <th class="min-width-80">@lang('UOM')</th>
                                <th class="min-width-80">@lang('Price 1')</th>
                                <th class="min-width-80">@lang('Price 2')</th>
                                <th class="min-width-80">@lang('Price 3')</th>
                                <th class="min-width-80">@lang('Weight')</th>
                                <th class="min-width-80">@lang('Size')</th>
                                <th class="min-width-80">@lang('Quantity')</th>
                                <th class="min-width-80">@lang('Date IN')</th>
                                <th class="min-width-80">@lang('Date Out')</th>

                            </tr>
                            </thead>
                            <tbody>
                            @if (count($products))
                                @foreach ($products as $index => $product)
                                    <tr>
                                        <td class="align-middle">{{ @$categories[$product->category_id] }}</td>
                                        <td class="align-middle">{{ $product->item_no }}</td>
                                        <td class="align-middle">
                                            <img style="max-width: 35px; cursor: pointer;"
                                                 title="Click to show Larger image"
                                                 data-toggle="tooltip" data-placement="bottom"
                                                 data-largeimg="{{$product->image_url}}"
                                                 src="{{ $product->image_url ? $product->image_url : asset('assets\img\no-image.png') }}" class="img-thumbnail" alt="Virgin Farm">
                                            {{ $product->product_text }}

                                            {!!  $product->is_deal ? '<i class="fas fa-bolt text-danger" title="Deal"></i>' :'' !!}
                                        </td>

                                        <td class="align-middle">{{ $product->unit_of_measure }}</td>
                                        <td class="align-middle">${{ $product->price_fedex }}</td>
                                        <td class="align-middle">${{ $product->price_fob }}</td>
                                        <td class="align-middle">${{ $product->price_hawaii }}</td>

                                        <td class="align-middle">{{ $product->weight }}</td>
                                        <td class="align-middle">{{ $product->size }}</td>
                                        <td class="align-middle">{{ $product->quantity }}</td>
                                        <td class="align-middle">{{ $product->date_in }}</td>
                                        <td class="align-middle">{{ $product->date_out }}</td>
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

    {!! $products->render() !!}


    <div class="modal" id="upload_excel_inventory" type="">
        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Upload Inventory (<small>Future inventory for Nov to Dec</small>)</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form action="{{route('upload.inventory.excel')}}" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}

                        <div class="form-groups">
                            <label for="number_socks">Click to Upload Inventory File</label>
                            <label class="btn btn-primary btn-sm center-block btn-file">
                                <i class="fa fa-upload " aria-hidden="true"></i>
                                <input required type="file" style="display: none;" name="file_inventory">
                            </label>
                        </div>


                        <div class="form-group row">
                            <label for="dateInput" class="col-sm-3 col-form-label">Date in</label>
                            <div class="col-sm-8 mt-2">
                                <input required type="text" name="date_in" class="form-control-sm datepicker" id="dateInput" placeholder="Date In">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="dateInput" class="col-sm-3 col-form-label">Date Out</label>
                            <div class="col-sm-8 mt-2">
                                <input required type="text" name="date_out"  class="form-control-sm datepicker" id="dateInput" placeholder="Date Out">
                            </div>
                        </div>

                        <small>
                            Only 6 columns include i.e ITEM#, ITEM DESC, PRICE 1, PRICE 2,PRICE 3,QUANTITY
                        </small>
                        <br>
                        <br>
                        <input type="submit" value="Upload Inventory" class="btn btn-primary btn-sm float-right">
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal" id="upload_excel_products" type="">
        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Upload Products </h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form action="{{route('upload.products.excel')}}" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}

                        <div class="form-groups">
                            <label for="number_socks">Click to Upload Products File</label>
                            <label class="btn btn-primary btn-sm center-block btn-file">
                                <i class="fa fa-upload " aria-hidden="true"></i>
                                <input required type="file" style="display: none;" name="file_products">
                            </label>
                        </div>

{{--                        <div class="form-group row">--}}
{{--                            <label for="dateInput" class="col-sm-3 col-form-label">Date in</label>--}}
{{--                            <div class="col-sm-8 mt-2">--}}
{{--                                <input required type="text" name="date_in" class="form-control-sm datepicker" id="dateInput" placeholder="Date In">--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="form-group row">--}}
{{--                            <label for="dateInput" class="col-sm-3 col-form-label">Date Out</label>--}}
{{--                            <div class="col-sm-8 mt-2">--}}
{{--                                <input required type="text" name="date_out"  class="form-control-sm datepicker" id="dateInput" placeholder="Date Out">--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <small>
                            Only 9 columns include i.e Item Class,	Item No., Description,	UOM	Price 1, Price 3,Price 5, Weight, Size
                        </small>
                        <br>
                        <br>
                        <input type="submit" value="Create Products" class="btn btn-primary btn-sm float-right">
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal" id="upload_excel_images" type="">
        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Upload Products Images </h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form action="{{route('upload.products.zip.images')}}" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}

                        <div class="form-groups">
                            <label for="number_socks">Click to Upload Products Zip Images</label>
                            <label class="btn btn-primary btn-sm center-block btn-file">
                                <i class="fa fa-upload " aria-hidden="true"></i>
                                <input required type="file" style="display: none;" name="images_zip">
                            </label>
                        </div>

                        <small>
                            Only zip file is allowed here also image names are should be SKU
                        </small>
                        <br>
                        <br>
                        <input type="submit" value="Upload Images" class="btn btn-primary btn-sm float-right">
                    </form>
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

        $('.datepicker').datepicker();

        $('#import_excel_products').on('click', function () {
            $('#upload_excel_products').modal('show');
        });

        $('#import_excel_images').on('click', function () {
            $('#upload_excel_images').modal('show');
        });

        $('#import_excel_inventory').on('click', function () {
            $('#upload_excel_inventory').modal('show');
        });
    </script>
@endsection
