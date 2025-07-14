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

        $validator = \Validator::make($request->all(), [
            'class_id' => 'required|integer',
            'sub_class' => 'required|string|max:10',
            'description' => 'required|string|max:255',
            'color' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $colorClass->update($request->all());

        ColorClass::where('sub_class' , $request->sub_class)->update([
            'color' => $request->color
        ]);

        return response()->json(['message' => 'Color Class Updated', 'data' => $colorClass]);
    }

    public function destroy($id)
    {
        $colorClass = ColorClass::findOrFail($id);
        $colorClass->delete();

        return response()->json(['message' => 'Color Class Deleted']);
    }
}
