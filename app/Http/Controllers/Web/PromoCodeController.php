<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\PromoCode;

class PromoCodeController extends Controller
{
    public function index()
    {
        if (myRoleName() != 'Admin') {
            abort(403);
        }

        return view('promocodes.promo_codes');
    }

    public function getPromoCodes()
    {
        return response()->json(PromoCode::all());
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'code' => 'required|unique:promo_codes',
            'promo_disc_class' => 'nullable|string|max:255',
            'min_box_weight' => 'nullable|numeric',
            'discount_percentage' => 'nullable|numeric|min:1|max:100',
            'max_usage' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Default unchecked checkboxes to 0
        $data = $request->all();
        $data['price_fob'] = $request->input('price_fob', 0);
        $data['price_fedex'] = $request->input('price_fedex', 0);
        $data['price_hawaii'] = $request->input('price_hawaii', 0);

        $promoCode = PromoCode::create($data);

        return response()->json(['message' => 'Promo Code Created', 'data' => $promoCode]);
    }

    public function edit($id)
    {
        $promoCode = PromoCode::findOrFail($id);
        return response()->json($promoCode);
    }

    public function update(Request $request, $id)
    {
        $promoCode = PromoCode::findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'code' => 'required|unique:promo_codes,code,' . $id,
            'promo_disc_class' => 'nullable|string|max:255',
            'min_box_weight' => 'nullable|numeric',
            'discount_percentage' => 'nullable|numeric|min:1|max:100',
            'max_usage' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = $request->all();
        $data['price_fob'] = $request->input('price_fob', 0);
        $data['price_fedex'] = $request->input('price_fedex', 0);
        $data['price_hawaii'] = $request->input('price_hawaii', 0);

        $promoCode->update($data);

        return response()->json(['message' => 'Promo Code Updated', 'data' => $promoCode]);
    }

    public function destroy($id)
    {
        if ($id == 1) {
            return response()->json(['message' => 'The default promo code cannot be deleted.']);
        }

        $promoCode = PromoCode::findOrFail($id);
        $promoCode->delete();

        return response()->json(['message' => 'Promo Code Deleted']);
    }
}
