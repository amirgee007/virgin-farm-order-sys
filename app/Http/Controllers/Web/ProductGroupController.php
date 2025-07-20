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
        $products = Product::all();
        return view('product-groups.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'products' => 'array',
            'products.*.item_no' => 'required|string',
            'products.*.stems' => 'required|integer|min:1',
        ]);

        // Create the group first
        $group = ProductGroup::create(['name' => $request->name]);

        $attach = [];

        foreach ($request->products as $row) {
            $product = Product::where('item_no', $row['item_no'])->first();

            if ($product) {
                $attach[$product->id] = ['stems' => $row['stems']];
            }
            // If product not found by item_no, we skip it (as per your instruction)
        }

        $group->products()->attach($attach);

        return redirect()->route('product-groups.index')->with('success', 'Group created successfully.');
    }


    public function edit(ProductGroup $productGroup)
    {
        $products = Product::all();
        $selected = $productGroup->products->pluck('pivot.stems', 'id')->toArray();
        return view('product-groups.edit', compact('productGroup', 'products', 'selected'));
    }

    public function update(Request $request, ProductGroup $productGroup)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'products' => 'array',
            'products.*.item_no' => 'required|string',
            'products.*.stems' => 'required|integer|min:1',
        ]);

        $productGroup->update(['name' => $request->name]);

        $sync = [];

        foreach ($request->products as $row) {
            $product = Product::where('item_no', $row['item_no'])->first();
            if ($product) {
                $sync[$product->id] = ['stems' => $row['stems']];
            }
        }

        $productGroup->products()->sync($sync);

        return redirect()->route('product-groups.index')->with('success', 'Group updated successfully.');
    }


    public function destroy(ProductGroup $productGroup)
    {
        $productGroup->delete();
        return redirect()->route('product-groups.index')->with('success', 'Group deleted');
    }

    public function getBreakdown($id)
    {
        $product = Product::with('groups')->findOrFail($id);

        // Gather all products from all groups this product belongs to
        $linkedProducts = collect();

        foreach ($product->groups as $group) {
            $groupProducts = $group->products()
                ->withPivot('stems')
                ->get();

            $linkedProducts = $linkedProducts->merge($groupProducts);
        }

        // Optional: remove duplicates (if a product is in multiple groups)
        $linkedProducts = $linkedProducts->unique('id');

        return response()->json([
            'html' => view('products._partial.breakdown_modal', [
                'product' => $product,
                'linkedProducts' => $linkedProducts,
            ])->render()
        ]);
    }


}
