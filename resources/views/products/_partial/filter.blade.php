<div class="col-md-2 col-sm-12 mt-md-0 mt-1 ">
    <button type="button" class="btn btn-secondary mr-1 float-right" data-toggle="collapse" data-target="#filterBy">
        <i class="fas fa-cogs"></i> Filter By
    </button>
</div>

<div class="clearfix"></div>

<div class="col-12 mt-4 collapse {{ $filter ? 'show' : '' }}" id="filterBy" >
    <span><b>2. Add items from the product availability to your shopping cart</b></span>
    <form id="filterProductForm2" method="GET">
        <div class="row mt-1">
            <div class="col-4">
                <div class="form-group">
                    <label for="category_id">Category:</label>
                    <select class="form-control" name="filter[category]" id="category_id" >
                        <option selected value="">All</option>
                        @foreach($categories AS $key => $val)
                            <option value="{{$key}}"
                                {{ (isset($filter['category']) && $filter['category'] == $key) ? 'selected': ''  }}
                            >{{$val}}</option>
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

                    <input type="text"
                           class="form-control rounded"
                           placeholder="Search by name, category, variety, grade, color"
                           name="filter[product_text]"
                           value="{{@$filter['product_text']}}">
                </div>
            </div>

            <div class="col-4">
                <div class="form-group">
                    <label for="select_country">Vendor:</label>

                    <input type="text"
                           placeholder="Search by vendor name"
                           class="form-control rounded"
                           name="filter[vendor]"
                           value="{{@$filter['vendor']}}">
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="col-12">
                <div class="text-center align-content-center">
                    <a class="btn btn-secondary btn-sm" href="{{route('products.index.manage')}}">
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
