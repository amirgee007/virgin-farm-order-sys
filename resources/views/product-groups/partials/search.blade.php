{{-- resources/views/product-groups/partials/search.blade.php --}}

<div class="card mb-4 border shadow-sm">
    <div class="card-header bg-light py-2 px-3 border-bottom small fw-semibold">
        <i class="fa fa-magic"></i> Search/Add Products to Group
    </div>

    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-4 position-relative">
                <label class="form-label">
                    Search Product
                    <i class="fa fa-search text-danger"></i>
                </label>
                <input type="text" id="search_product" class="form-control" placeholder="Item No or Name" autocomplete="off">
                <div id="product-suggestions" class="list-group position-absolute shadow-sm bg-white border rounded mt-1" style="z-index: 1000; max-height: 250px; overflow-y: auto;"></div>
                <input type="hidden" id="product_id">
            </div>

            <div class="col-md-5">
                <label class="form-label">Product Name(Auto Show)</label>
                <input type="text" id="product_name" class="form-control" readonly>
            </div>

            <div class="col-md-2">
                <label class="form-label">Stems</label>
                <input type="number" id="stems" class="form-control" min="1" placeholder="20">
            </div>

            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-primary" id="add-product">
                    <i class="fa fa-plus-circle"></i>
                </button>
            </div>
        </div>

        <div class="mt-2 text-center">
            <b id="search-status" class="text-muted d-block fst-italic">Waiting for input...</b>
        </div>
    </div>
</div>
