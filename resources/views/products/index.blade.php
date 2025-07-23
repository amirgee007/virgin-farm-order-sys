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
        .product-checkbox,#select-all {
            transform: scale(1.5); /* or 2 for even larger */
            margin: 5px;
            margin-top: 10px;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem; /* override for more compact size */
            font-size: 0.75rem;
        }
        .groupProd {
            background: rgb(211, 246, 211) !important;
        }
    </style>

@endsection

@section('content')
    @include('partials.messages')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body mt-0 p-3">
                    <!-- Header bar with product count and toggle -->
                    <div class="notes-success p-2 d-flex justify-content-between align-items-center" style="background-color: #d4f8d4; border-radius: 5px;">
                        <div>
                            <span>Total products in the system are: <strong>{{$count}}</strong></span>
                        </div>
                        <div>
                            <button class="btn btn-primary btn-sm" type="button" data-toggle="collapse" data-target="#actionGroups" aria-expanded="false" aria-controls="actionGroups">
                                <i class="fas fa-cogs"></i> Show/Hide Actions <i class="fas fa-arrow-down"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Collapsible Action Section -->
                    <div class="collapse mt-3" id="actionGroups">
                        <div class="p-3 border rounded bg-light shadow-sm">
                            <div class="row">

                            <!-- Product Management -->
                            <div class="col-md-4 mb-3">
                                <strong>Product Management:</strong><br>
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#createProductModal"
                                   class="btn btn-success btn-sm mr-2 mb-1"
                                   title="Create a new product" data-toggle="tooltip">
                                    <i class="fas fa-plus-circle"></i> New Product
                                </a>
                                <a href="javascript:void(0)" id="import_excel_products"
                                   class="btn btn-danger btn-sm mr-2 mb-1"
                                   title="Upload Web Item Masters with Class" data-toggle="tooltip">
                                    <i class="fas fa-upload"></i> Import Master
                                </a>
                                <a href="javascript:void(0)" id="bulk_delete_excel"
                                   class="btn btn-danger btn-sm mr-2 mb-1"
                                   title="Bulk delete products via Excel" data-toggle="tooltip">
                                    <i class="fas fa-times"></i> Bulk Delete
                                </a>
                            </div>

                            <!-- Image Management -->
                            <div class="col-md-4 mb-3">
                                <strong>Image Management:</strong><br>
                                <a href="javascript:void(0)" id="import_excel_images"
                                   class="btn btn-warning btn-sm mr-2 mb-1"
                                   title="Upload product images ZIP with SKU" data-toggle="tooltip">
                                    <i class="fas fa-upload"></i> Upload Images
                                </a>
                                <a href="javascript:void(0)" id="copy_multiple_img"
                                   class="btn btn-success btn-sm mr-2 mb-1"
                                   title="Bulk assign images to products" data-toggle="tooltip">
                                    <i class="fas fa-puzzle-piece"></i> Bulk Assign Images
                                </a>
                            </div>

                            <!-- Inventory Management -->
                            <div class="col-md-4 mb-3">
                                <strong>Inventory Management:</strong><br>
                                <a href="javascript:void(0)" id="import_excel_inventory"
                                   class="btn btn-success btn-sm mr-1 mb-1"
                                   title="Refresh inventory via Excel file" data-toggle="tooltip">
                                    <i class="fas fa-upload"></i> Update Ranges
                                </a>

                                <a href="javascript:void(0)" id="reset_delete_inventory"
                                   class="btn btn-danger btn-sm mr-1 mb-1"
                                   title="Reset or delete a specific inventory entry" data-toggle="tooltip">
                                    <i class="fas fa-trash"></i> Delete Ranges
                                </a>

                                <a href="javascript:void(0)" id="import_excel_inventory_bulk"
                                   class="btn btn-secondary btn-sm mr-1 mb-1"
                                   title="Bulk inventory upload from Excel" data-toggle="tooltip">
                                    <i class="fas fa-upload"></i> Bulk Upload
                                </a>

{{--                                <a href="{{route('inventory.reset.clear')}}"--}}
{{--                                   class="btn btn-warning btn-sm mr-1 mb-1"--}}
{{--                                   data-toggle="tooltip"--}}
{{--                                   title="Reset or clear current inventory"--}}
{{--                                   data-method="GET"--}}
{{--                                   data-confirm-title="@lang('Please Confirm')"--}}
{{--                                   data-confirm-text="@lang('Are you sure that you want to Reset,Refresh or Clear the inventory availability?')"--}}
{{--                                   data-confirm-delete="@lang('Yes, delete it!')">--}}
{{--                                    <i class="fas fa-sync"></i> Reset All--}}
{{--                                </a>--}}

                                <button type="button"
                                        title="Reset or clear current inventory"
                                        class="btn btn-warning btn-sm mr-1 mb-1"
                                        data-toggle="modal"
                                        data-target="#confirmResetModal">
                                    <i class="fas fa-sync"></i> Reset Inventory
                                </button>

                            </div>

                        </div>
                        </div>
                    </div>

                    <form action="" method="GET" id="product-form" class="pb-2 mb-1 border-bottom-light">
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

                            <div class="col-md-2 mt-2">
                                <a href="#" id="showModalBtn" class="btn btn-primary btn-sm">
                                    <i class="fas fa-download"></i> Download Report
                                </a>
                            </div>

                            {{-- <div class="col-md-2 mb-2">--}}
{{--                                <input type="checkbox" id="qty_found" {{Request::get('qty_found') ? 'checked' : ''}} name="qty_found" value="2">--}}
{{--                                <label for="qty_found">Only Show QTY > 0</label>--}}
{{--                            </div>--}}

                        </div>
                    </form>

                    <form method="POST" action="{{ route('products.bulk.delete') }}" id="bulkDeleteForm">
                        @csrf
                        <div class="mb-2">
                            <small class="text-gray-500">Hi, products with a green background are combo products.</small>
                            <br/>
                            <button type="submit" id="bulkDeleteBtn" class="btn btn-danger btn-sm d-none">
                                <i class="fas fa-trash"></i> Delete  <span id="selectedCountValue">0</span> Selected Products
                            </button>
                        </div>
                        <div class="table-responsive mt-2" id="users-table-wrapper">
                        <table class="table table-borderless table-striped products-list-table">
                            <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>

                                <th class="min-width-80">@lang('Category')</th>
                                <th class="min-width-80">@lang('Item')</th>
                                <th class="min-width-80">@lang('Supplier')</th>
                                <th class="min-width-200">@lang('Product Description')</th>
                                <th class="min-width-80">@lang('Color')</th>
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
                    </form>
                </div>
            </div>
        </div>
    </div>

    {!! $products->render() !!}

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmResetModal" tabindex="-1" role="dialog" aria-labelledby="confirmResetLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-warning">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="confirmResetLabel">@lang('Confirm Inventory Reset')</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p class="text-danger"><i class="fas fa-exclamation-triangle"></i>
                        This will permanently clear all current inventory availability. After this action, you will need to upload the full inventory again.
                    </p>
                    <p><b>@lang('To confirm, please type') <code>DELETE</code>:</b></p>
                    <input type="text" id="confirm-delete-input" class="form-control" placeholder="Type DELETE to confirm">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Cancel')</button>
                    <a href="{{ route('inventory.reset.clear') }}"
                       id="confirm-delete-btn"
                       class="btn btn-danger"
                       style="display: none;">
                        <i class="fas fa-trash-alt"></i> @lang('Yes, Reset It')
                    </a>
                </div>
            </div>
        </div>
    </div>

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
                        7 Columns include i.e ITEM#, ITEM DESC, PRICE 1, PRICE 2,PRICE 3,PRICE 4,QUANTITY
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
                        <div class="form-group mt-3"
                             title="Select the inventory type for the above selected dates."
                             data-toggle="tooltip" data-placement="top">
                            <label for="inventory_type" class="form-label">Inventory Type</label>
                            <select class="form-control" id="inventory_type" name="inventory_type" required>
                                <option value="" selected>Virgin Farms Default</option>
                                <option value="1">Special Seasonal Inventory</option>
                                <option value="2">Farms-Direct Inventory</option>
                            </select>
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
                            Only 10 columns include i.e Item Class,	Item No, Description, UOM Price 1, Price 3,Price 5,PriceFedex +, Weight, Size, Type
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

    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">Generate Report</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="generateReportForm" method="POST" action="{{ route('generate.report') }}">
                        @csrf

                        <div class="form-group">
                            <label for="columns">Select Columns:</label>
                            <select required class="form-control select2" name="columns[]" multiple id="columns" style="width: 100%; height: 25px">
                                @foreach ($columnCustomNames as $key => $name)
                                    <option value="{{$key}}" {{ in_array($key, ['product_text' , 'quantity']) ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="date_in" class="form-label">Date In</label>
                            <input required type="date" id="date_in" name="date_in" class="form-control"
                                   value="{{ request('date_in', now()->toDateString()) }}">
                        </div>
                        <div class="form-group">
                            <label for="date_out" class="form-label">Date Out</label>
                            <input required type="date" id="date_out" name="date_out" class="form-control"
                                   value="{{ request('date_out', now()->toDateString()) }}">
                        </div>
                        <div class="form-group">
                            <label for="report_type" class="form-label">Report Format</label>
                            <select required id="report_type" name="report_type" class="form-control ">
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label d-block">Select Supplier:</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" checked name="supplier_id" id="supplier_all" value="all" required>
                                <label class="form-check-label" for="supplier_all">All</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="supplier_id" id="supplier_vf" value="1">
                                <label class="form-check-label" for="supplier_vf">VF</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="supplier_id" id="supplier_dutch" value="2">
                                <label class="form-check-label" for="supplier_dutch">Dutch</label>
                            </div>
                        </div>


                        <button type="submit" class="btn btn-success">Generate Report</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form method="POST" action="{{ route('products.bulk.delete') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Bulk Delete Products</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
{{--                        <label for="sku_list">Enter SKUs (comma-separated or new lines):</label>--}}
{{--                        <textarea readonly name="sku_list" id="sku_list" class="form-control" rows="6" required></textarea>--}}
{{--                        <hr>--}}
                        <label>Upload Excel:</label>
                        <input type="file" name="sku_excel" accept=".xlsx,.xls,.csv" class="form-control">
                        <small>File must have a 1 column with <b>item_nos</b></small>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Delete Products</button>
                    </div>
                </div>
            </form>
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
            const $input = $('#confirm-delete-input');
            const $deleteBtn = $('#confirm-delete-btn');

            $input.on('input', function () {
                const val = $.trim($input.val());
                if (val === 'DELETE') {
                    $deleteBtn.show();
                    toastr.success('You are now authorized to reset the inventory. Click "YES" to proceed..');
                } else {
                    if (val.toUpperCase() === 'DELETE') {
                        toastr.warning('Please type DELETE in all capital letters to reset.');
                    }
                    $deleteBtn.hide();
                }
            });

            $('#inventory_type').on('change', function () {
                var selected = $(this).val();
                if (selected === '1' || selected === '2') {
                    var msg = 'Please check with the admin about the correct inventory type before uploading.';
                    toastr.warning(msg);
                    // Speak the alert message
                    if ('speechSynthesis' in window) {
                        var msg = new SpeechSynthesisUtterance(msg);
                        window.speechSynthesis.speak(msg);
                    }
                }
            });

            $('#confirmResetModal').on('hidden.bs.modal', function () {
                $input.val('');
                $deleteBtn.hide();
            });

            document.getElementById('showModalBtn').addEventListener('click', function () {
                // Show the modal programmatically
                const modal = new bootstrap.Modal(document.getElementById('reportModal'));
                modal.show();
            });

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

        $(document).ready(function () {

            $(document).on('click', '.product-checkbox', function(event) {
                event.stopPropagation(); // Stops the click from triggering the parent row's collapse
            });

            // Show/hide delete button and update selected count
            function updateBulkActions() {
                const selectedCount = $('.product-checkbox:checked').length;
                $('#bulkDeleteBtn').toggleClass('d-none', selectedCount === 0);
                $('#selectedCountValue').text(selectedCount);
            }

            $('#select-all').on('change', function () {
                $('.product-checkbox').prop('checked', this.checked).trigger('change');
            });

            $(document).on('change', '.product-checkbox', function () {
                updateBulkActions();
            });

            $('#bulkDeleteForm').on('submit', function (e) {
                const selectedCount = $('.product-checkbox:checked').length;
                if (selectedCount === 0) {
                    e.preventDefault();
                    toastr.warning('Please select at least one product.');
                    return;
                }

                const confirmed = confirm(`Are you sure you want to delete ${selectedCount} selected product(s)?`);
                if (!confirmed) {
                    e.preventDefault();
                }
            });

        });


        $.fn.editable.defaults.mode = 'inline';
        $.fn.editable.defaults.ajaxOptions = {
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        };

        $('.editable').editable({
            success: function(response, newValue) {
                toastr.success('Updated successfully.');
            },
            error: function(response) {
                toastr.error(response.responseText);
            }
        });


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

        $('#bulk_delete_excel').on('click', function () {
            $('#bulkDeleteModal').modal('show');
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
