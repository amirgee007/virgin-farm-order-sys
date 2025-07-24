<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\Product;
use Vanguard\Models\ProductGroup;

class ProductGroupController extends Controller
{
    public function index()
    {
        $groups = ProductGroup::with('products')->get();
        return view('product-groups.index', compact('groups'));
    }

    public function create()
    {
        $products = Product::where('is_combo_product', 1)
            ->whereNotIn('id', function ($query) {
                $query->select('parent_product_id')
                    ->from('product_groups')
                    ->whereNotNull('parent_product_id');
            })
            ->orderBy('item_no')
            ->get();
        return view('product-groups.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_product_id' => [
                'nullable',
                'exists:products,id',
                'unique:product_groups,parent_product_id'
            ],
            'products' => 'array',
            'products.*.item_no' => 'required|string',
            'products.*.product_text_temp' => 'required|string',
            'products.*.stems' => 'required|integer|min:1',
        ]);

        // Create the group first
        $group = ProductGroup::create([
            'name' => $request->name,
            'parent_product_id' => $request->parent_product_id,
        ]);

        $attach = [];

        foreach ($request->products as $row) {
            $product = Product::where('item_no', $row['item_no'])->first();

            if ($product) {
                $attach[$product->id] = [
                    'stems' => $row['stems'],
                    'product_text_temp' => $row['product_text_temp'],
                ];
            }
            // If product not found by item_no, we skip it (as per your instruction)
        }

        $group->products()->attach($attach);

        return redirect()->route('product-groups.index')->with('success', 'Combo products added with others successfully.');
    }


    public function edit(ProductGroup $productGroup)
    {
        $products = Product::where('is_combo_product' , 1)->orderBy('item_no')->get();
        $selected = $productGroup->products->pluck('pivot.stems', 'id')->toArray();
        return view('product-groups.edit', compact('productGroup', 'products', 'selected'));
    }

    public function update(Request $request, ProductGroup $productGroup)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_product_id' => 'nullable|exists:products,id',
            'products' => 'array',
            'products.*.item_no' => 'required|string',
            'products.*.stems' => 'required|integer|min:1',
        ]);


        $productGroup->update([
            'name' => $request->name,
//            'parent_product_id' => $request->parent_product_id,
        ]);

        $sync = [];

        foreach ($request->products as $row) {
            $product = Product::where('item_no', $row['item_no'])->first();
            if ($product) {
                $sync[$product->id] = [
                    'stems' => $row['stems'],
                    'product_text_temp' => $row['product_text_temp'],
                ];
            }
        }


        $productGroup->products()->sync($sync);

        return redirect()->route('product-groups.index')->with('success', 'Group combo products updated successfully.');
    }


    public function destroy(ProductGroup $productGroup)
    {
        $productGroup->delete();
        return redirect()->route('product-groups.index')->with('success', 'Group deleted');
    }

    public function getBreakdown($product_id)
    {
        $group = ProductGroup::with('products')->where('parent_product_id', $product_id)->first();

        $totalStems = 0;
        foreach ($group->products as $product) {
            $totalStems += $product->pivot->stems;
        }

        return response()->json([
            'html' => view('product-groups._breakdown', compact('group', 'totalStems'))->render()
        ]);
    }


}
