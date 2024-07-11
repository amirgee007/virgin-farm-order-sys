<?php

namespace Vanguard\Listeners\Users;

use Illuminate\Auth\Events\Verified;
use Vanguard\Mail\VirginFarmGlobalMail;
use Vanguard\Repositories\User\UserRepository;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;

class ActivateUser
{
    public function __construct(private UserRepository $users)
    {
    }

    /**
     * Handle the event.
     *
     * @param Verified $event
     * @return void
     */
    public function handle(Verified $event)
    {
        $this->users->update($event->user->id, [
            'status' => UserStatus::ACTIVE
        ]);

        $user = User::find($event->user->id);

        if($user){
            $content = '<p>This user has verified his email address, Please update profile information and approve it.</p>'
                . '<ul>'
                . '<li><strong>Full Name:</strong> ' . $user->name . '</li>'
                . '<li><strong>Company Name:</strong> ' . $user->company_name . '</li>'
                . '<li><strong>Phone No:</strong> ' . $user->phone . '</li>'
                . '<li><strong>Email:</strong> ' . $user->email . '</li>'
                . '<li><strong>Username:</strong> ' . $user->username . '</li>'
                . '</ul>';

            \Log::info($user->name . ' New Virgin Farms User Verified Email.');

            \Mail::to('weborders@virginfarms.com')
                ->cc('amirseersol@gmail.com')
                ->send(new VirginFarmGlobalMail('New Virgin Farms User Verified Email', $content));
        }
    }
}
