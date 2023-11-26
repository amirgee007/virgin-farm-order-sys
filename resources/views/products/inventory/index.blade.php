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
                                {{rand(10,1000)}}
                            </b>

                            <a href="javascript:void(0)" id="import_excel_inventory" title="Upload excel file to refresh inventory" data-toggle="tooltip" data-placement="left"
                               class="btn btn-primary btn-sm float-right ml-2 mr-1">
                                <i class="fas fa-upload"></i>
                            </a>
                        </p>
                    </div>

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
@stop

@section('scripts')
    @include('partials.toaster-js')
    <script>
        $('.img-thumbnail').click(function () {
            $('#imagePreviewId').attr('src', $(this).data('largeimg'));
            $('#largeImgModal').modal('show');
        });

        $('.datepicker').datepicker();

        $('#import_excel_inventory').on('click', function () {
            $('#upload_excel_inventory').modal('show');
        });
    </script>
@endsection
