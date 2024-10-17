<?php

namespace Vanguard\Http\Controllers\Web\Auth;

use App\Services\MailchimpService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Storage;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\Auth\RegisterRequest;
use Vanguard\Mail\VirginFarmGlobalMail;
use Vanguard\Repositories\Role\RoleRepository;
use Vanguard\Repositories\User\UserRepository;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;

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
        $this->handleFileUpload($request);

        $user = $this->createUser($request, $roles);

        event(new Registered($user));

        $message = $this->getSuccessMessage();

        \Auth::login($user);

        $this->sendNewUserNotification($user);

        if ($user->state == 10) {
            $this->sendTaxFileNotification($user);
        }

        $this->addToMailchimp($user);

        return redirect('/')->with('success', $message);
    }

    private function handleFileUpload(RegisterRequest $request)
    {
        if ($request->hasFile('tax_file')) {
            $file = $request->file('tax_file');
            $username = $request->input('username');
            $filename = $username . '.' . $file->getClientOriginalExtension();

            $request->tax_file = $file->storeAs('tax_files', $filename);
        }
    }

    private function createUser(RegisterRequest $request, RoleRepository $roles)
    {
        $finalData = array_merge(
            $request->validFormData(),
            ['role_id' => $roles->findByName('Client')->id]
        );
        $finalData['tax_file'] = $request->tax_file;

        return $this->users->create($finalData);
    }

    private function getSuccessMessage()
    {
        return setting('reg_email_confirmation')
            ? __('Your account is created successfully! Please confirm your email.')
            : __('Your account is created successfully!');
    }

    private function sendNewUserNotification($user)
    {
        $content = $this->buildNewUserNotificationContent($user);

        \Mail::to('weborders@virginfarms.com')
            ->send(new VirginFarmGlobalMail('New User Registration Notification', $content));
    }

    private function buildNewUserNotificationContent($user)
    {
        return '<p>New user has been successfully registered on Virgin farms order system. Here are the details of the new user:</p>'
            . '<ul>'
            . '<li><strong>Full Name:</strong> ' . $user->name . '</li>'
            . '<li><strong>Company Name:</strong> ' . $user->company_name . '</li>'
            . '<li><strong>Phone No:</strong> ' . $user->phone . '</li>'
            . '<li><strong>Email:</strong> ' . $user->email . '</li>'
            . '<li><strong>Username:</strong> ' . $user->username . '</li>'
            . '<li><strong>Sales Representative:</strong> ' . $user->sales_rep . '</li>'
            . '<li><strong>Shipping Address:</strong> ' . $user->address . '</li>'
            . '<li><strong>Appt/Suite:</strong> ' . $user->apt_suit . '</li>'
            . '<li><strong>City:</strong> ' . $user->city . '</li>'
            . '<li><strong>State:</strong> ' . @$user->usState->state_name . '</li>'
            . '<li><strong>Zip:</strong> ' . $user->zip . '</li>'
            . '<li><strong>Shipping Method:</strong> ' . @$user->carrier->carrier_name . '</li>'
            . '</ul>';
    }

    private function sendTaxFileNotification($user)
    {
        $subject = 'New User Registration & Tax File Uploaded';
        $content = 'Please find attachment. A tax file has been uploaded by ' . $user->username . '.';

        $email = new VirginFarmGlobalMail($subject, $content);

        // Attach the file
        $email->setAttach('attach', storage_path('app/' . $user->tax_file));

        // Send email with attachment
        \Mail::to(['juan@virginfarms.com', 'olif@virginfarms.com'])->send($email);
    }

    private function addToMailchimp($user)
    {
        $mergeFields = [
            'FNAME' => $user->first_name,
            'LNAME' => $user->last_name,
            'COMPANY' => $user->company_name,
            'ADDRESS' => $user->address,
            'PHONE' => $user->phone
        ];

        $mailchimpService = new \Vanguard\Services\MailchimpService();
        $mailchimpService->addSubscriber($user->email, ['Web Shop Users'], $mergeFields);
    }

}
