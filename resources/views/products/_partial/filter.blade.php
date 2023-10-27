<div class="col-md-2 col-sm-12 mt-md-0 mt-1 ">
        <button type="button"

                class="btn btn-secondary mr-1 float-right"
                data-toggle="collapse"
                data-target="#filterBy">
            <i class="fas fa-cogs"></i> Filter By
        </button>
</div>

<div class="clearfix"></div>

<div class="col-12 mt-4 collapse {{ @$filter ? 'show' : '' }}" id="filterBy" >

    <span><b>2. Add items from the product availability to your shopping cart</b></span>
    <form id="filterOrdersForm2" method="GET">
        <div class="row mt-1">
            <div class="col-4">
                <div class="form-group">
                    <label for="show_order_by">Category:</label>
                    <select class="form-control" name="show_order_by" id="show_order_by" >
                        <option  value="">All</option>
                        @foreach([
                            'is_active' => 'Is Active',
                            'is_delivered' => 'Is Delivered',
                            'is_deleted' => 'Is Deleted',
                        ] AS $key => $val)
                            <option value="{{$key}}" {{ @$showOrderBy == $key ? 'selected': '' }}>{{$val}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-4">
                <div class="form-group">
                    <label for="select_status">
                        Search Product Description:
                        <i class="fa fa-question-circle"
                           title="You can search for products by name, category, variety, grade, color and box size OR code."
                           data-trigger="hover"
                           data-toggle="tooltip"
                           aria-hidden="true"></i>
                    </label>
                    <select class="form-control" name="filter[status_id]" id="select_status" >
                        <option  value="">All</option>
                        {{--@foreach($orderStatus AS $key => $status)--}}
                            {{--<option value="{{$key}}"--}}
                                    {{--{{ (isset($filter['status_id']) && $filter['status_id'] == $key) ? 'selected' : '' }}>--}}
                                {{--{{$status}}--}}
                            {{--</option>--}}
                        {{--@endforeach--}}
                    </select>
                </div>
            </div>

            <div class="col-4">
                <div class="form-group">
                    <label for="select_country">Vendor:</label>

                    <input type="text"
                           class="form-control rounded"
                           name="filter[vendor]"
                           value="{{ \Request::get('vendor') }}">
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="col-12">
                <div class="text-center align-content-center">
                    <a class="btn btn-secondary btn-sm" href="{{route('products.index')}}">
                        <i class="fas fa-times"></i>
                        Clear Filter
                    </a>
                    <button class="btn btn-success btn-sm" type="submit">
                        <i class="fas fa-search-plus"></i>
                        Filter
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
