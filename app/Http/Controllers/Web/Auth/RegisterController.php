<?php

namespace Vanguard\Http\Controllers\Web\Auth;

use Illuminate\Auth\Events\Registered;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\Auth\RegisterRequest;
use Vanguard\Mail\VirginFarmGlobalMail;
use Vanguard\Repositories\Role\RoleRepository;
use Vanguard\Repositories\User\UserRepository;
use Vanguard\Support\Enum\UserStatus;

class RegisterController extends Controller
{
    public function __construct(private UserRepository $users)
    {
        $this->middleware('registration')->only('show', 'register');

    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return view('auth.register', [
            'socialProviders' => config('auth.social.providers')
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param RegisterRequest $request
     * @param RoleRepository $roles
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request, RoleRepository $roles)
    {
        $user = $this->users->create(
            array_merge($request->validFormData(), ['role_id' => $roles->findByName('Client')->id])
        );

        event(new Registered($user));

        $message = setting('reg_email_confirmation')
            ? __('Your account is created successfully! Please confirm your email.')
            : __('Your account is created successfully!');

        \Auth::login($user);

        $content = '<p>New user has been successfully registered on Virgin farms order system. Here are the details of the new user:</p>'
            . '<ul>'
            . '<li><strong>Full Name:</strong> ' . $user->name . '</li>'
            . '<li><strong>Last Name:</strong> ' . $user->last_name . '</li>'
            . '<li><strong>Company Name:</strong> ' . $user->company_name . '</li>'
            . '<li><strong>Phone No:</strong> ' . $user->phone . '</li>'
            . '<li><strong>Email:</strong> ' . $user->email . '</li>'
            . '<li><strong>Username:</strong> ' . $user->username . '</li>'
            . '<li><strong>Sales Representative:</strong> ' . $user->sales_rep . '</li>'
            . '<li><strong>Shipping Address:</strong> ' . $user->address . '</li>'
            . '<li><strong>Appt/Suite:</strong> ' . $user->apt_suit . '</li>'
            . '<li><strong>City:</strong> ' . $user->city . '</li>'
            . '<li><strong>State:</strong> ' . $user->state . '</li>'
            . '<li><strong>Zip:</strong> ' . $user->zip . '</li>'
            . '<li><strong>Shipping Method:</strong> ' . @$user->carrier->carrier_name . '</li>'
            . '</ul>';

        \Mail::to('weborders@virginfarms.com')
            ->cc('amirseersol@gmail.com')
            ->send(new VirginFarmGlobalMail('New User Registration Notification', $content));

        return redirect('/')->with('success', $message);
    }
}
