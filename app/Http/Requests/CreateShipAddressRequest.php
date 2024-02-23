<?php

namespace Vanguard\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateShipAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => 'min:5|max:50|string|required',
            'company_name' => 'min:5|max:150|string|required',
            'phone' => 'min:5|max:20|string|required',
            'address' => 'required|string',
            'state_id' => 'required',
            'city_id' => 'required',
            'zip' => 'required',
        ];
    }
}
