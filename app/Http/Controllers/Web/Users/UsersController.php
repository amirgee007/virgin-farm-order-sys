<?php

namespace Vanguard\Http\Controllers\Web\Users;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Vanguard\Events\User\Deleted;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\User\CreateUserRequest;
//use Vanguard\Repositories\Activity\ActivityRepository;
use Vanguard\Models\Carrier;
use Vanguard\Models\ClientNotification;
use Vanguard\Repositories\Country\CountryRepository;
use Vanguard\Repositories\Role\RoleRepository;
use Vanguard\Repositories\User\UserRepository;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;

/**
 * Class UsersController
 * @package Vanguard\Http\Controllers
 */
class UsersController extends Controller
{
    public function __construct(private UserRepository $users) {  }

    /**
     * Display paginated list of all users.
     *
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        $carriers = getCarriers();
        $prices = getPrices();

        $users = $this->users->paginate($perPage = 20, $request->search, $request->status);

        $statuses = ['' => __('All')] + UserStatus::lists();

        $salesRep = getSalesReps();
        return view('user.list', compact('users', 'statuses' , 'carriers' , 'prices' , 'salesRep'));
    }

    /**
     * Displays user profile page.
     *
     * @param User $user
     * @return Factory|View
     */
    public function show(User $user)
    {
        $states = getStates();
        $carriers = getCarriers();
        $prices = getPrices();

        $terms = getTerms();
        $salesRep = getSalesReps();

        return view('user.view', compact(
            'user' ,
            'states',
            'prices',
            'carriers',
            'terms'
        ));
    }

    /**
     * Displays form for creating a new user.
     *
     * @param CountryRepository $countryRepository
     * @param RoleRepository $roleRepository
     * @return Factory|View
     */
    public function create(CountryRepository $countryRepository, RoleRepository $roleRepository)
    {

        return view('user.add', [
            'countries' => $this->parseCountries($countryRepository),
            'roles' => $roleRepository->lists(),
            'statuses' => UserStatus::lists(),
            'salesRep' => getSalesReps(),
            'carriers' => getCarriers(),
            'states' => getStates(),
            'terms' => getTerms(),
            'prices' => getPrices(),
        ]);
    }

    public function approve(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->is_approved = $request->is_approved;
        $user->save();

        if (false && $request->is_approved) {
            // Send email notification
            \Mail::raw('Your account has been approved. Please login to the website.', function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Virgin farms Account Approved');
            });
        }

        return response()->json(['success' => true]);
    }

    /**
     * Parse countries into an array that also has a blank
     * item as first element, which will allow users to
     * leave the country field unpopulated.
     *
     * @param CountryRepository $countryRepository
     * @return array
     */
    private function parseCountries(CountryRepository $countryRepository)
    {
        return [0 => __('Select a Country')] + $countryRepository->lists()->toArray();
    }

    /**
     * Stores new user into the database.
     *
     * @param CreateUserRequest $request
     * @return mixed
     */
    public function store(CreateUserRequest $request)
    {
        // When user is created by administrator, we will set his
        // status to Active by default.
        $data = $request->all() + [
            'status' => UserStatus::ACTIVE,
            'email_verified_at' => now()
        ];

        if (! data_get($data, 'country_id')) {
            $data['country_id'] = null;
        }

        // Username should be updated only if it is provided.
        if (! data_get($data, 'username')) {
            $data['username'] = null;
        }

        $this->users->create($data);

        #admin notify
        $message = 'New customer profile added : '.$data['first_name'];
        addOwnNotification($message);

        return redirect()->route('users.index')
            ->withSuccess(__('User created successfully.'));
    }

    /**
     * Displays edit user form.
     *
     * @param User $user
     * @param CountryRepository $countryRepository
     * @param RoleRepository $roleRepository
     * @return Factory|View
     */
    public function edit(User $user, CountryRepository $countryRepository, RoleRepository $roleRepository)
    {

        return view('user.edit', [
            'edit' => true,
            'user' => $user,
            'countries' => $this->parseCountries($countryRepository),
            'roles' => $roleRepository->lists(),
            'statuses' => UserStatus::lists(),
            'salesRep' => getSalesReps(),
            'carriers' => getCarriers(),
            'prices' => getPrices(),
            'states' => getStates(),
            'terms' => getTerms(),
            'socialLogins' => $this->users->getUserSocialLogins($user->id)
        ]);
    }

    /**
     * Removes the user from database.
     *
     * @param User $user
     * @return $this
     */
    public function destroy(User $user)
    {
        if ($user->is(auth()->user())) {
            return redirect()->route('users.index')
                ->withErrors(__('You cannot delete yourself.'));
        }

        $this->users->delete($user->id);

        event(new Deleted($user));

        return redirect()->route('users.index')
            ->withSuccess(__('User deleted successfully.'));
    }

    public function adminLogin(User $user)
    {
        auth()->loginUsingId($user->id);

        $user->update([
            'last_ship_date' => null,
            'supplier_id' => 0,
        ]);
        return redirect()->route('dashboard');
    }

    #every user has its own page but if admin want to see all, he can see it.
    public function indexShippingAddress(){
        return 'plz wait for the front page';
    }

    public function indexNotifications(){


        $notifications = ClientNotification::mine()->limit(500)->latest()->get();
        return view('notifications.index' , compact('notifications'));
    }

    public function deleteNotifications($id){

        $not = ClientNotification::find($id);
        $not->delete();

        session()->flash('app_message', 'The Notification has been deleted successfully.');
        return back();
    }

}
