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

        // Parse the content to split into different tutorials
        $tutorials = $this->splitTutorials($text->value);

        return view('help.index' , compact('tutorials'));
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

    private function splitTutorials($content)
    {
        // Use regex to split content based on the pattern of Tutorial headings
        $pattern = '/(<p[^>]*>.*?Tutorial\s*\d+.*?<\/p>)/i';
        $parts = preg_split($pattern, $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $tutorials = [];
        $currentTitle = '';
        $currentContent = '';

        foreach ($parts as $part) {
            if (preg_match($pattern, $part)) {
                // Save the previous tutorial if exists
                if ($currentTitle && $currentContent) {
                    $tutorials[] = [
                        'title' => strip_tags($currentTitle),
                        'content' => $currentContent
                    ];
                }
                // Start a new tutorial
                $currentTitle = $part;
                $currentContent = '';
            } else {
                $currentContent .= $part;
            }
        }

        // Add the last tutorial
        if ($currentTitle && $currentContent) {
            $tutorials[] = [
                'title' => strip_tags($currentTitle),
                'content' => $currentContent
            ];
        }

        return $tutorials;
    }
}
