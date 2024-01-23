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
                        <p>Total products are in the system are.
                            <b>
                                {{$count}}
                            </b>

                            <a href="javascript:void(0)" title="Create New Product" data-toggle="modal"
                               data-target="#createProductModal" class="btn btn-primary btn-sm float-right ml-2 mr-1">
                                <i class="fas fa-edit"></i>
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
                               data-confirm-text="@lang('Are you sure that you want to delete this product?')"
                               data-confirm-delete="@lang('Yes, delete it!')"
                               title="Reset,Refresh or Clear the inventory availability."
                               class="btn btn-warning btn-sm float-right ml-2 mr-1">
                                <i class="fas fa-sync"></i>
                            </a>

                            <a href="javascript:void(0)" id="reset_delete_inventory" title="Reset/Delete a specified inventory" data-toggle="tooltip" data-placement="left"
                               class="btn btn-danger btn-sm float-right ml-2 mr-1">
                                <i class="fas fa-trash"></i>
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

                                <th class="min-width-80">@lang('Weight')</th>
                                <th class="min-width-80">@lang('Size')</th>
                                <th class="min-width-80">@lang('Delete')</th>

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

    <div class="modal" id="reset_delete_inventory_mod" type="">
        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Reset a specified inventory By Date</h5>
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
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="reset" name="flag" id="flexRadioDefault1" checked>
                                <label class="form-check-label" for="flexRadioDefault1">
                                    Reset Selected Date Inventory
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="delete" name="flag" id="flexRadioDefault2" >
                                <label class="form-check-label" for="flexRadioDefault2">
                                    Delete Selected Date Inventory
                                </label>
                            </div>
                        </div>

                        <br>
                        <input type="submit" value="Upload Inventory" class="btn btn-primary btn-sm float-right">
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
                            <input class="form-control" type="file" id="file_inventory" name="file_inventory">
                        </div>

{{--                        <div class="form-group row">--}}
{{--                            <label for="dateInput" class="col-sm-3 col-form-label">Date in</label>--}}
{{--                            <div class="col-sm-8 mt-2">--}}
{{--                                <input required type="text" name="date_in" class="form-control-sm datepicker" id="dateInput" placeholder="Date In">--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="form-group">
                            <input type="hidden" name="range" value="" class="dateRangeVal">
                            <label for="dateRange" class="form-label mt-3">Date in/out</label>
                            <div id="dateRange" class="form-control float-right dateRanges" style="cursor: pointer; ">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span>
                                &nbsp;<i class="fa fa-caret-down"></i>
                            </div>




{{--                            <label for="dateInput" class="col-sm-3 col-form-label">Date Out</label>--}}
{{--                            <div class="col-sm-8 mt-2">--}}
{{--                                <input required type="text" name="date_out"  class="form-control-sm datepicker" id="dateInput" placeholder="Date Out">--}}
{{--                            </div>--}}
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
                            <label for="images_zip" class="form-label">Click to Upload Products Zip Images</label>
                            <input class="form-control" type="file" id="images_zip" name="images_zip">
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
                    <h5 class="modal-title" id="exampleModalLabel">Create New</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}

                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label for="terms">@lang('Select Category')</label>
                                    {!! Form::select('terms', [],['class' => 'form-control input-solid']) !!}
                                </div>


                                <div class="form-group">
                                    <label for="contract_code">@lang('item Description')</label>
                                    <input type="number" class="form-control input-solid" id="contract_code"
                                           name="contract_code" placeholder="@lang('Contract Code')" value="">
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

@stop

@section('scripts')
    @include('partials.toaster-js')
    <script src="{{ url('assets/plugins/daterangepicker/daterangepicker.min.js') }}"></script>
    <script src="{{ url('assets/plugins/select2/select2.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/x-editable/bootstrap-editable.min.js') }}" ></script>

    <script>
        $(function() {

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
                $('#item_copy_too').val($(this).attr('id'));
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

        $('#reset_delete_inventory').on('click', function () {
            $('#reset_delete_inventory_mod').modal('show');
        });
    </script>
@endsection
