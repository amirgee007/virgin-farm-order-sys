<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\Setting;


class HelpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display products page page.
     *
     * @return View
     */
    public function index()
    {
        $text = Setting::where('key' , 'help-faq')->first();
        return view('help.index' , compact('text'));
    }

    public function edit(){
        $text = Setting::where('key' , 'help-faq')->first();

       return view('help.edit-file', compact(
            'text'
        ));
    }

    public function update(Request $request){

        $text = Setting::where('id' , $request->file_type_amir)->first();

        $text->value = $request->value;
        $text->done_by = auth()->id();
        $text->save();

        session()->flash('app_message', 'Your page has been updated successfully.');
        return redirect(route('help.faq.index'));
    }
}
