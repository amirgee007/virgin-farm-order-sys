<?php

namespace Vanguard\Http\Controllers\Web\Profile;

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Repositories\Country\CountryRepository;
use Vanguard\Repositories\Role\RoleRepository;
use Vanguard\Repositories\User\UserRepository;
use Vanguard\Support\Enum\UserStatus;

/**
 * Class ProfileController
 * @package Vanguard\Http\Controllers
 */
class ProfileController extends Controller
{
    public function __construct(
        private UserRepository $users,
        private RoleRepository $roles,
        private CountryRepository $countries
    )
    {
    }

    /**
     * Display user's profile page.
     *
     * @return Factory|View
     */
    public function show()
    {

        $roles = $this->roles->all()->pluck('name', 'id');

        $carriers = getCarriers();
        $prices = getPrices();
        $states = getStates();
        $terms = getTerms();
        $salesRep = getSalesReps();


        return view('user.profile', [
            'user' => auth()->user(),
            'edit' => true,
            'roles' => $roles,
            'countries' => [0 => __('Select a Country')] + $this->countries->lists()->toArray(),
            'socialLogins' => $this->users->getUserSocialLogins(auth()->id()),
            'statuses' => UserStatus::lists(),
            'carriers' => $carriers,
            'prices' => $prices,
            'states' => $states,
            'terms' => $terms,
            'salesRep' => $salesRep,
        ]);
    }
}
