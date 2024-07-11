<?php

namespace Vanguard\Http\Requests\Auth;

use Vanguard\Http\Requests\Request;
use Vanguard\Support\Enum\UserStatus;

class RegisterRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username',
            'password' => 'required|confirmed|min:8',
            'address' => 'required|string|max:500',
            'apt_suit' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:10',
            'carrier_id' => 'required|string|max:255',
        ];

        if ($this->input('customer_type') === 'current') {
            $rules['sales_rep'] = 'required|string|max:255';
        }

//        if (setting('registration.captcha.enabled')) {
//            $rules['g-recaptcha-response'] = 'required|captcha';
//        }

        if (setting('tos')) {
            $rules['tos'] = 'accepted';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'tos.accepted' => __('You have to accept Terms of Service.')
        ];
    }

    /**
     * Get the valid request data.
     *
     * @return array
     */
    public function validFormData()
    {
        // Determine user status. User's status will be set to UNCONFIRMED
        // if he has to confirm his email or to ACTIVE if email confirmation is not required
        $status = setting('reg_email_confirmation')
            ? UserStatus::UNCONFIRMED
            : UserStatus::ACTIVE;

        #ship_method remaning here yet.
        return array_merge($this->only('email', 'username', 'password' ,'first_name', 'last_name',
            'company_name', 'phone', 'sales_rep', 'address', 'apt_suit', 'city', 'state', 'zip' , 'carrier_id'), [
            'status' => $status,
            'email_verified_at' => setting('reg_email_confirmation') ? null : now()
        ]);
    }
}
