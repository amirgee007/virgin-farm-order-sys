<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Vanguard\Events\Settings\Updated as SettingsUpdated;
use Illuminate\Http\Request;
use Setting;
use Vanguard\Http\Controllers\Controller;
use Carbon\Carbon;

/**
 * Class SettingsController
 * @package Vanguard\Http\Controllers
 */
class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display general settings page.
     *
     * @return Factory|View
     */
    public function general()
    {
        $popup = \Vanguard\Models\Setting::where('key', 'popup-seting')->first();
        $minimumOrder = \Vanguard\Models\Setting::where('key', 'minimum_order_amount')->first();

        return view('settings.general', compact('popup' , 'minimumOrder'));
    }

    public static function unitOfMeaures()
    {
        return [
            "B03" => "Pack 3 stems (bunch)",
            "B16" => "Pack 16 (Used for bouquets)",
            "B18" => "Pack 18 (bunch)",
            "B1C" => "Pack 100 (box)",
            "B24" => "Pack 24 stems (bunch)",
            "B30" => "Pack 30 stems (bunch)",
            "B32" => "Pack 32 stems (bunch)",
            "B35" => "Pack 35 stems (bunch)",
            "B40" => "Pack 40 stems (bunch)",
            "B48" => "Pack 48 stems (bunch or mini box)",
            "B60" => "Pack 60 stems (bunch or mini box)",
            "B80" => "Pack 80 stems (mini box)",
            "BC6" => "Pack 126 units (large Box)",
            "BLC" => "Pack 150 stems (mini box)",
            "BU1" => "Pack 25 stems (bunch)",
            "BU2" => "Pack 20 stems (bunch)",
            "BU3" => "Pack 10 stems (bunch)",
            "BU4" => "Pack 15 stems (bunch)",
            "BU5" => "Pack 6 stems (bunch)",
            "BU6" => "Pack 5 stems (bunch)",
            "BU7" => "Pack 50 stems (bunch)",
            "BUD" => "Pack 12 stems (bunch)",
            "BUE" => "Pack Bunch (usually a weighted bunch)",
        ];
    }

    /**
     * Display Authentication & Registration settings page.
     *
     * @return Factory|View
     */
    public function auth()
    {
        return view('settings.auth');
    }

    /**
     * Handle application settings update.
     *
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        if ($request->pop_up_dynamic) {
            $popup = \Vanguard\Models\Setting::firstOrNew(['key' => 'popup-seting']);

            $popup->value = $request->pop_up_text;
            $popup->label = $request->start_date;
            $popup->extra_info = $request->end_date;

            $popup->save();
        }

        if ($request->minimum_order_amount) {
            $minimumOrder = \Vanguard\Models\Setting::firstOrNew(['key' => 'minimum_order_amount']);

            $minimumOrder->value = $request->minimum_order_amount;
            $minimumOrder->save();
        }
        else {
            $this->updatesetting($request->except("_token"));
        }
        return back()->withSuccess(__('Settings updated successfully.'));
    }

    public function checkPopupDate(Request $request)
    {
        // Get the shipped_date from the AJAX request
        $shippedDate = Carbon::parse($request->input('shipped_date'));

        // Get the popup settings
        $popup = \Vanguard\Models\Setting::where('key', 'popup-seting')->first();
        $startDate = Carbon::parse($popup->label);
        $endDate = Carbon::parse($popup->extra_info);

        // Check if the shipped_date is within the start_date and end_date
        $showPopup = $shippedDate->between($startDate, $endDate);

        // Return JSON response with the popup text if the date is within range
        return response()->json([
            'show_popup' => $showPopup,
            'popup_text' => $showPopup ? $popup->value : ''
        ]);
    }

    /**
     * Update settings and fire appropriate event.
     *
     * @param $input
     */
    private function updatesetting($input)
    {
        foreach ($input as $key => $value) {
            Setting::set($key, $value);
        }

        Setting::save();

        event(new SettingsUpdated);
    }

    /**
     * Enable system 2FA.
     *
     * @return mixed
     */
    public function enableTwoFactor()
    {
        $this->updatesetting(['2fa.enabled' => true]);

        return back()->withSuccess(__('Two-Factor Authentication enabled successfully.'));
    }

    /**
     * Disable system 2FA.
     *
     * @return mixed
     */
    public function disableTwoFactor()
    {
        $this->updatesetting(['2fa.enabled' => false]);

        return back()->withSuccess(__('Two-Factor Authentication disabled successfully.'));
    }

    /**
     * Enable registration captcha.
     *
     * @return mixed
     */
    public function enableCaptcha()
    {
        $this->updatesetting(['registration.captcha.enabled' => true]);

        return back()->withSuccess(__('reCAPTCHA enabled successfully.'));
    }

    /**
     * Disable registration captcha.
     *
     * @return mixed
     */
    public function disableCaptcha()
    {
        $this->updatesetting(['registration.captcha.enabled' => false]);

        return back()->withSuccess(__('reCAPTCHA disabled successfully.'));
    }

    /**
     * Display notification settings page.
     *
     * @return Factory|View
     */
    public function notifications()
    {
        return view('settings.notifications');
    }
}
