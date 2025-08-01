<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\ColorClass;

class ColorClassController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:permissions.manage');
    }
    public function index()
    {
        return view('colors.colors_class');
    }

    public function getColorsClass()
    {
        return response()->json(ColorClass::all());
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'class_id' => 'required|integer',
            'sub_class' => 'required|string|max:10',
            'description' => 'required|string|max:255',
            'color' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $colorClass = ColorClass::create($request->all());

        return response()->json(['message' => 'Color Class Created', 'data' => $colorClass]);
    }

    public function edit($id)
    {
        $colorClass = ColorClass::findOrFail($id);
        return response()->json($colorClass);
    }

    public function update(Request $request, $id)
    {
        $colorClass = ColorClass::findOrFail($id);

        $data = \Validator::make($request->all(), [
            'class_id'    => 'required|integer',
            'sub_class'   => 'required|string|max:10',
            'description' => 'required|string|max:255',
            'color'       => 'required|string|max:50',
        ])->validate();

        // Make color uppercase only if it's "mix" or "assorted"
        if (in_array(strtolower($data['color']), ['mix', 'assorted'])) {
            $data['color'] = strtoupper($data['color']);
        }

        // Update this record
        $colorClass->update($data);

        // Keep all records with the same sub_class in sync
        ColorClass::where('sub_class', $data['sub_class'])->update([
            'color' => $data['color'],
        ]);

        return response()->json([
            'message' => 'Color Class Updated',
            'data' => $colorClass->fresh()
        ]);
    }


    public function destroy($id)
    {
        $colorClass = ColorClass::findOrFail($id);
        $colorClass->delete();

        return response()->json(['message' => 'Color Class Deleted']);
    }
}
