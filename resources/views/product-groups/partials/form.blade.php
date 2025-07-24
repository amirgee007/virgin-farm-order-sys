{{-- resources/views/product-groups/partials/form.blade.php --}}
@csrf

@if(isset($edit) && $edit)
    @method('PUT')
@endif

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <h5 class="card-title">
                    {{ $edit ? __('Edit Product Group') : __('Product Combo Group') }}
                </h5>
                <p class="text-muted small">
                    {{ $edit
                        ? __('Modify the combo group and its associated products.')
                        : __('Define a combo group of related products.')
                    }}
                </p>
            </div>

            <div class="col-md-9">
                {{-- Parent Product --}}
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Parent Product</label>
                        @if($edit)
                            @php
                                $selectedProduct = $products->firstWhere('id', old('parent_product_id', $productGroup->parent_product_id ?? null));
                            @endphp
                            @if($selectedProduct)
                                <select class="form-control" disabled>
                                    <option>{{ $selectedProduct->item_no }} - {{ $selectedProduct->product_text }}</option>
                                </select>
                                <input type="hidden" name="parent_product_id" value="{{ $selectedProduct->id }}">
                            @else
                                <div class="text-danger">Invalid parent product selected.</div>
                            @endif
                        @else
                            <select name="parent_product_id" class="form-control" required>
                                <option value="">-- None --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('parent_product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->item_no }} - {{ $product->product_text }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Group Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $productGroup->name ?? '') }}" required>
                    </div>
                </div>

                {{-- Product Search --}}
                @include('product-groups.partials.search')

                {{-- Product List --}}
                @include('product-groups.partials.product-list')

                <div class="text-end">
                    <button type="submit" class="btn btn-primary float-right">
                        <i class="fa fa-save"></i> {{ $edit ? 'Save Group' : 'Save Combo Group' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
