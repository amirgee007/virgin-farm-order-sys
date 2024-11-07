@extends('layouts.app')

@section('page-title', __('All Products Manage'))
@section('page-heading', __('All Products Manage'))

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">
        @lang('Manage Inventory')
    </li>
@stop

@section('styles')
    <link media="all" type="text/css" rel="stylesheet" href="{{ url('assets/css/custom.css') }}">
    <link media="all" type="text/css" rel="stylesheet" href="{{ url('assets/plugins/x-editable/bootstrap-editable.css') }}">
    <link media="all" type="text/css" rel="stylesheet" href="{{ url('assets/plugins/daterangepicker/daterangepicker.css') }}">
    <link media="all" type="text/css" rel="stylesheet" href="{{ url('assets/plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/plugins/dropzone/dist/min/dropzone.min.css')}}">

    <style>
        .clickable {
            cursor: pointer;
        }
    </style>

@endsection

@section('content')
    @include('partials.messages')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body mt-0 p-3">

                    <div class="notes-success" style="">
                        <p>Total products in the system are.
                            <b>
                                {{$count}}
                            </b>

                            <a href="javascript:void(0)" title="Create New Product" data-toggle="modal"
                               data-target="#createProductModal" class="btn btn-primary btn-sm float-right ml-2 mr-1">
                                <i class="fas fa-plus-circle"></i>
                            </a>

                            <a href="javascript:void(0)" id="import_excel_images" title="Upload products images file as zip with SKU name" data-toggle="tooltip" data-placement="left"
                               class="btn btn-warning btn-sm float-right ml-2 mr-1">
                                <i class="fas fa-upload"></i>
                            </a>

                            <a href="javascript:void(0)" id="import_excel_products" title="Upload products file i.e Web item Masters with Class This is the basis of all products." data-toggle="tooltip" data-placement="left"
                               class="btn btn-danger btn-sm float-right ml-2 mr-1">
                                <i class="fas fa-upload"></i>
                            </a>

                            <a href="javascript:void(0)" id="import_excel_inventory" title="Upload excel file to refresh inventory" data-toggle="tooltip" data-placement="left"
                               class="btn btn-primary btn-sm float-right ml-2 mr-1">
                                <i class="fas fa-upload"></i>
                            </a>

                            <a href="{{route('inventory.reset.clear')}}"
                               data-toggle="tooltip"
                               data-placement="top"
                               data-method="GET"
                               data-confirm-title="@lang('Please Confirm')"
                               data-confirm-text="@lang('Are you sure that you want to Reset,Refresh or Clear the inventory availability?')"
                               data-confirm-delete="@lang('Yes, delete it!')"
                               title="Reset,Refresh or Clear the inventory availability."
                               class="btn btn-warning btn-sm float-right ml-2 mr-1">
                                <i class="fas fa-sync"></i>
                            </a>

                            <a href="javascript:void(0)" id="reset_delete_inventory" title="Reset/Delete a specified inventory" data-toggle="tooltip" data-placement="left"
                               class="btn btn-danger btn-sm float-right ml-2 mr-1">
                                <i class="fas fa-trash"></i>
                            </a>

                            <a href="javascript:void(0)" id="copy_multiple_img" title="Bulk assign feature: copy to more than one image" data-toggle="tooltip" data-placement="left"
                               class="btn btn-primary btn-sm float-right ml-2 mr-1">
                                <i class="fas fa-puzzle-piece"></i>
                            </a>

                            <a href="javascript:void(0)" id="import_excel_inventory_bulk" title="Upload BULK excel file to refresh inventory" data-toggle="tooltip" data-placement="left"
                               class="btn btn-secondary btn-sm float-right ml-2 mr-1">
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
                                        <button class="btn btn-light" type="submit"> <i class="fas fa-search text-muted"></i></button>
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-2 mb-2">
                                <select  class="form-control " name="category" id="category">
                                    <option selected value="">Filter By Category</option>
                                    @foreach($categories AS $id => $val)
                                        <option {{Request::get('category') == $id ? 'selected' : ''}} value="{{$id}}">{{$val}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                {!! Form::select('filter', $filters, Request::get('filter') , ['id' => 'filter', 'class' => 'form-control input-solid']) !!}
                            </div>

{{--                            <div class="col-md-2 mb-2">--}}
{{--                                <input type="checkbox" id="qty_found" {{Request::get('qty_found') ? 'checked' : ''}} name="qty_found" value="2">--}}
{{--                                <label for="qty_found">Only Show QTY > 0</label>--}}
{{--                            </div>--}}

                        </div>
                    </form>

                    <div class="table-responsive mt-2" id="users-table-wrapper">
                        <table class="table table-borderless table-striped products-list-table">
                            <thead>
                            <tr>
                                <th class="min-width-80">@lang('Category')</th>
                                <th class="min-width-80">@lang('item')</th>
                                <th class="min-width-80">@lang('Supplier')</th>
                                <th class="min-width-200">@lang('Product Description')</th>
                                <th class="min-width-80" title="the UOM is how many stems per bunch">@lang('UOM')</th>

                                <th class="min-width-80">@lang('Weight')</th>
                                <th class="min-width-80">@lang('Size')</th>
                                <th class="min-width-80">@lang('Action')</th>

                            </tr>
                            </thead>
                            <tbody>
                            @if (count($products))
                                @foreach ($products as $index => $product)
                                    @include('products.row')
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="12">
                                        No products found
                                    </td>
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

    <div class="modal" id="upload_excel_inventory_bulk" type="">
        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        Upload Bulk Inventory
                        <a href="javascript:void(0)" title="Sync Inventory From FTP" data-toggle="tooltip" data-placement="left" id="inventory_sync_ftp" >
                            <i class="fas fa-sync text-primary"></i>
                        </a>
                        <span id="spinner" class="spinner-border spinner-border-sm text-danger d-none" role="status" aria-hidden="true"></span>
                    </h5>

                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form action="{{route('upload.inventory.excel')}}" class='dropzone' >
                        @csrf
                    </form>

                    <small>
                        6 Columns include i.e ITEM#, ITEM DESC, PRICE 1, PRICE 2,PRICE 3,QUANTITY
                    </small>
                </div>

            </div>
        </div>
    </div>

    <div class="modal" id="reset_delete_inventory_mod" type="">
        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Reset/Delete a specified inventory by date</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form action="{{route('reset.specific.inventory')}}" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}

                        <div class="form-group">
                            <input type="hidden" name="range" value="" class="dateRangeVal">
                            <label for="dateRange" class="form-label mt-3">Date in/out</label>
                            <div id="dateRange" class="form-control float-right dateRanges" style="cursor: pointer; ">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span>
                                &nbsp;<i class="fa fa-caret-down"></i>
                            </div>
                        </div>

                        <br/>
                        <br/>
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" value="reset" name="inventory_action" id="resetInventory" checked>
                                <label class="form-check-label" for="resetInventory">
                                    Reset this date inventory
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" value="delete" name="inventory_action" id="deleteInventory">
                                <label class="form-check-label" for="deleteInventory">
                                    Delete this date inventory
                                </label>
                            </div>
                        </div>
                        <hr>

                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" value="0" name="flower_type" id="allType" checked>
                                <label class="form-check-label" for="allType">
                                   All
                                </label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" value="1" name="flower_type" id="onlyVirgin">
                                <label class="form-check-label" for="onlyVirgin">
                                    Only Virgin Farms
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" value="2" name="flower_type" id="dutchFlowers">
                                <label class="form-check-label" for="dutchFlowers">
                                    Dutch Flowers
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" value="3" name="flower_type" id="specialSeasonal">
                                <label class="form-check-label" for="specialSeasonal">
                                    Special Seasonal
                                </label>
                            </div>
                        </div>
                        <hr>
                        <br>
                        <input type="submit" value="Delete/Reset Inventory" class="btn btn-primary btn-sm float-right">
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal" id="upload_excel_inventory" type="">
        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Upload Inventory (<small>Future inventory for xxx to xxx</small>)</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form action="{{route('upload.inventory.excel')}}" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}

                        <div class="form-groups">
                            <label for="file_inventory" class="form-label">Click to Upload Inventory File</label>
                            <input class="form-control" required type="file" id="file_inventory" name="file_inventory">
                        </div>
                        <small>
                            ITEM#, DESCRIPTION, PRICE 1, PRICE 2,PRICE 3,QUANTITY
                        </small>

                        <div class="form-group">
                            <input type="hidden" required name="range" value="" class="dateRangeVal">
                            <label for="dateRange" class="form-label mt-3">Date Range</label>
                            <div id="dateRange" class="form-control float-right dateRanges" style="cursor: pointer; ">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span>
                                &nbsp;<i class="fa fa-caret-down"></i>
                            </div>
                        </div>
                        <br><br>
                        <div class="form-groups">
                            <label for="expired_at" class="form-label">Expiry Time</label>
                            <input class="form-control" required id="expired_at" type="time" name="expired_at">
                        </div>
                        <br>
                        <div class="form-check"
                             title="Please confirm if you would like to mark all these products as special seasonal items for the above selected dates." data-toggle="tooltip" data-placement="left" >
                            <input class="form-check-input" type="checkbox" value="1" id="is_special" name="is_special">
                            <label class="form-check-label" for="is_special">
                                Is Special Seasonal Inventory?
                            </label>
                        </div>
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
                    <form action="{{route('upload.create.products.excel')}}" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}

                        <div class="form-groups">
                            <label for="file_products" class="form-label">Click to Upload Products File</label>
                            <input class="form-control" type="file" id="file_products" name="file_products">
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
                            Only 9 columns include i.e Item Class,	Item No, Description, UOM Price 1, Price 3,Price 5, Weight, Size, Type
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
                            <label for="images_zip" class="form-label">Click to Upload Products Zip Images</label>
                            <input class="form-control" type="file" id="images_zip" name="images_zip">
                        </div>

                        <small>
                            Only zip file allowed & image names should be same as SKU OR match any part product text.
                        </small>
                        <br>
                        <br>
                        <input type="submit" value="Upload Images" class="btn btn-primary btn-sm float-right">
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal" id="largeImgModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagePreviewTitle">Large Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img src="" id="imagePreviewId" style="width: 450px; height: 450px;" >
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="copyPicModal" role="dialog" aria-labelledby="copyPicModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Copy Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('copy.image.product')}}" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <input type="hidden" name="item_copy_too" value="0" id="item_copy_too">

{{--                        <div class="form-group row">--}}
{{--                            We can also show all items here to faciliate user--}}
{{--                            <label for="copyImg" class="col-sm-3 col-form-label">Write Item No</label>--}}
{{--                            <div class="col-sm-8">--}}
{{--                                <input required type="text" name="item_copy_from" class="form-control " id="copyImg" placeholder="Item Name">--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="form-group">
                            <label for="copyImg">Select Item:</label>
                            <select  class="form-control select2" name="item_copy_from" id="copyImg" style="width: 100%; height: 25px">
                                <option selected value="">Search & Select</option>
                                @foreach($itemsHaveImage AS $val)
                                    <option value="{{$val}}">{{$val}}</option>
                                @endforeach
                            </select>
                        </div>

                       <br>
                        <input type="submit" value="Copy Image" class="btn btn-primary btn-sm float-right">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="createProductModal" role="dialog" aria-labelledby="createProductModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create New Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('create.product')}}" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id">@lang('Select Category')</label>
                                    {!! Form::select('category_id', $categories, 0, ['class' => 'form-control input-solid', 'id' => 'category_id'] , ['required']) !!}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="item_no">@lang('Item No.')</label>
                                    <input type="text" class="form-control input-solid" id="item_no" name="item_no" placeholder="@lang('Item No')" required>
                                </div>

                            </div>

                            <div class="col-md-12">

                                <div class="form-group">
                                    <label for="product_text">@lang('Item Text')</label>
                                    <input type="text" class="form-control input-solid" id="product_text" name="product_text" placeholder="@lang('Item Text')" required>
                                </div>

                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="unit_of_measure">@lang('UOM')</label>
                                    <input type="text" class="form-control input-solid" id="unit_of_measure" name="unit_of_measure" placeholder="@lang('Unit Of Measure')" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="size">@lang('Size')</label>
                                    <input type="number" step="0.01" class="form-control input-solid" id="size" name="size" placeholder="@lang('Size')" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="weight">@lang('Weight')</label>
                                    <input type="number" step="0.01" class="form-control input-solid" id="weight" name="weight" placeholder="@lang('Weight')" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <input type="submit" value="Create Product" class="btn btn-primary btn-sm float-right">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="copy_multiple_img_mod" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Copy Multiple Images</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body" id="imagesModalBody"  style="overflow:hidden;">
                    Please wait...!!
                </div>

            </div>
        </div>

    </div>
@stop

@section('scripts')
    @include('partials.toaster-js')
    <script src="{{ url('assets/plugins/daterangepicker/daterangepicker.min.js') }}"></script>
    <script src="{{ url('assets/plugins/select2/select2.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/x-editable/bootstrap-editable.min.js') }}" ></script>
    <script src="{{asset('assets/plugins/dropzone/dist/min/dropzone.min.js')}}" type="text/javascript"></script>

    <script>
        $(function() {

            var currentUrl = window.location;

            // var myDropzone = new Dropzone(".dropzone",{
            //     maxFilesize: 10, // MB
            //     acceptedFiles: ".xls,.xlsx",
            //     addRemoveLinks: true,
            //     timeout: 50000,
            //     init: function() {
            //         this.on("error", function(file, response) {
            //             alert(response);
            //         });
            //         this.on("success", function(file, response) {
            //             console.log(response);
            //         });
            //     }
            // });

            // Dropzone.autoDiscover = false;

            $("#category ,#filter, #qty_found").change(function () {
                $("#product-form").submit();
            });

            $('.select2').select2();

            var start = moment('{{$selected['start']}}');
            var end = moment('{{$selected['end']}}');

            function cb(start, end) {
                $('#dateRange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $('.dateRangeVal').val($("#dateRange span").html());
            }

            $('.dateRanges').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Next 6 Days': [moment(), moment().add(6, 'days')],
                    'Next 7 Days': [moment(),moment().add(7, 'days')],
                    'Next 15 Days': [moment(), moment().add(15, 'days'), moment()],
                    'Next 30 Days': [moment(), moment().add(30, 'days'), moment()]
                }
            }, cb);

            cb(start, end);
        });

        $.fn.editable.defaults.mode = 'inline';
        $.fn.editable.defaults.ajaxOptions = {
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        };

        $('.editable').editable();

        $('.collapse').on('show.bs.collapse', function () {
            $('.collapse.in').collapse('hide');
        });

        $('.img-thumbnail').click(function () {
            var url = $(this).data('largeimg');

            if(url){
                $('#imagePreviewId').attr('src', url);
                $('#imagePreviewTitle').text($(this).data('info'));
                $('#largeImgModal').modal('show');
            }
            else{
                $('#item_copy_too').val($(this).data('id'));
                $('#copyPicModal').modal('show');
            }
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

        $('#import_excel_inventory_bulk').on('click', function () {
            $('#upload_excel_inventory_bulk').modal('show');
        });

        $('#reset_delete_inventory').on('click', function () {
            $('#reset_delete_inventory_mod').modal('show');
        });

        $('#inventory_sync_ftp').on('click', function () {

            var $button = $(this);
            $('.spinner-border').removeClass('d-none');
            $button.hide();

            $.ajax({
                url: '{{route('inventory.sync.ftp')}}',
                type: 'GET',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function (response) {
                    toastr.success(response.message);
                },
                error: function () {
                    toastr.error('Something went wrong during syncing.');
                },
                complete: function() {
                    $('.spinner-border').addClass('d-none');
                    $button.show();
                }
            });
        });

        $('#copy_multiple_img').on('click', function () {
            $('#copy_multiple_img_mod').modal('show');

            $.ajax({
                url: '{{route('copy.image.product')}}',
                data: {
                    'load_img_modal': 1
                },
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function (response) {
                    $('#imagesModalBody').html(response.modal);
                }
            });
        });

        $(window).on('load', function () {
            $(function(){
                setTimeout(function(){

                    var selectedProductsIds = [];

                    $(".img-thumbnail").each( function() {
                        selectedProductsIds.push($(this).data('id'));
                    });

                    $.ajax({
                        url: "{{ route('get.image.data.ajax') }}",
                        method: 'post',
                        data: {
                            ids:selectedProductsIds.join(",")
                        },
                        success: function (response) {
                            $.each(response,function(id, image) {
                                var idImg = `#${id}imgTD`;
                                if(image)
                                    $(idImg).attr("src",image);
                            });
                        },
                        error: function () {
                            toastr.error('Something went wrong during loading images data.');
                        }
                    });

                }, 250);
            });
        });

    </script>
@endsection
