<?php

namespace Vanguard\Http\Controllers\Web\Auth;

use Vanguard\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Vanguard\User;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['verify']); // Allow guests to verify
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect($this->redirectPath())
            : view('auth.verify');
    }

    public function verify(Request $request, $id, $hash)
    {

        $user = User::find($id); // Manually fetch the user by ID

        if (!$user) {
            return redirect('/login')->with('error', 'User not found.');
        }

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect('/login')->with('error', 'Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect('/')->with('message', 'Your email is already verified.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        Auth::login($user); // Ensure the user is logged in after verification

        return redirect('/')->with('verified', true);
    }

    public function resend(Request $request)
    {
        if (Auth::check() && Auth::user()->hasVerifiedEmail()) {
            return redirect('/')->with('message', 'Your email is already verified.');
        }

        if (!$request->user()) {
            return redirect('/login')->with('error', 'Please log in to resend the verification email.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }
}
