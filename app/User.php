<?php

namespace Vanguard;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Mail;
use Vanguard\Events\User\RequestedPasswordResetEmail;
use Vanguard\Models\Carrier;
use Vanguard\Models\ShippingAddress;
use Vanguard\Models\UsCity;
use Vanguard\Models\UsState;
use Vanguard\Presenters\Traits\Presentable;
use Vanguard\Presenters\UserPresenter;
use Vanguard\Services\Auth\TwoFactor\Authenticatable as TwoFactorAuthenticatable;
use Vanguard\Services\Auth\TwoFactor\Contracts\Authenticatable as TwoFactorAuthenticatableContract;
use Vanguard\Support\Authorization\AuthorizationUserTrait;
use Vanguard\Support\CanImpersonateUsers;
use Vanguard\Support\Enum\UserStatus;

class User extends Authenticatable implements TwoFactorAuthenticatableContract, MustVerifyEmail
{
    use TwoFactorAuthenticatable,
        CanResetPassword,
        Presentable,
        AuthorizationUserTrait,
        Notifiable,
        CanImpersonateUsers,
        HasApiTokens,
        HasFactory;

    protected $presenter = UserPresenter::class;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    protected $casts = [
        'last_login' => 'datetime',
        'birthday' => 'date',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'username', 'first_name', 'last_name', 'phone', 'avatar',
        'address', 'country_id', 'birthday', 'last_login', 'confirmation_token', 'status',
        'remember_token', 'role_id', 'email_verified_at','company_name','phone','customer_number','sales_rep',
        'apt_suit',
        'city',
        'state',
        'zip',
        'price_list',
        'contract_code',
        'terms',
        'credit_limit',
        'carrier_id',
        'carrier_id_default',
        'country_id',
        'address_id',
        'last_ship_date',
        'edit_order_id',
        'is_approved',
        'tax_file',
        'promo_disc_class'
    ];

    /**
     * Always encrypt password when it is updated.
     *
     * @param $value
     * @return string
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function setBirthdayAttribute($value)
    {
        $this->attributes['birthday'] = trim($value) ?: null;
    }

    public function gravatar()
    {
        $hash = hash('md5', strtolower(trim($this->attributes['email'])));

        return sprintf("https://www.gravatar.com/avatar/%s?size=150", $hash);
    }

    public function isUnconfirmed()
    {
        return $this->status == UserStatus::UNCONFIRMED;
    }

    public function isActive()
    {
        return $this->status == UserStatus::ACTIVE;
    }

    public function isBanned()
    {
        return $this->status == UserStatus::BANNED;
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function role()
    {
        return $this->hasOne(Role::class , 'id' , 'role_id');
    }

    public function shipAddress()
    {
        return $this->hasMany(ShippingAddress::class , 'user_id' , 'id');
    }

    public function shipingAddress()
    {
        return $this->hasOne(ShippingAddress::class , 'id' , 'address_id');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        Mail::to($this)->send(new \Vanguard\Mail\ResetPassword($token));
        event(new RequestedPasswordResetEmail($this));
    }

    public function getNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function carrier()
    {
        return $this->hasOne(Carrier::class , 'id' , 'carrier_id');
    }

    public function carrierDefault()
    {
        return $this->hasOne(Carrier::class , 'id' , 'carrier_id_default');
    }

    public function usState() {
        return $this->hasOne(UsState::class , 'id' , 'state');
    }


    public function getStateNameAttribute(){
        return $this->usState ? $this->usState->state_name : ''; #2,12
    }

    public function getCityNameAttribute(){
        return $this->city;
    }
}
