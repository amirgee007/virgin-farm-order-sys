<div class="row">

    <div class="col-md-6">
        <div class="form-group">
            <label for="first_name">@lang('Role')</label>
            {!! Form::select('role_id', $roles, $edit ? $user->role->id : '',
                ['class' => 'form-control input-solid', 'id' => 'role_id', $profile ? 'disabled' : '']) !!}
        </div>

        <div class="form-group">
            <label for="first_name">@lang('First Name')</label>
            <input type="text" class="form-control input-solid" id="first_name" required
                   name="first_name" placeholder="@lang('First Name')" value="{{ $edit ? $user->first_name : '' }}">
        </div>

    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="status">@lang('Status')</label>
            {!! Form::select('status', $statuses, $edit ? $user->status : '',
                ['class' => 'form-control input-solid', 'id' => 'status', $profile ? 'disabled' : '']) !!}
        </div>

        <div class="form-group">
            <label for="last_name">@lang('Last Name')</label>
            <input type="text" class="form-control input-solid" id="last_name"
                   name="last_name" placeholder="@lang('Last Name')" value="{{ $edit ? $user->last_name : '' }}">
        </div>
    </div>


    <div class="col-md-12">
        <div class="form-group">
            <label for="address">@lang('Address')</label>
            <input type="text" class="form-control input-solid" id="address"
                   name="address" placeholder="@lang('Address')" value="{{ $edit ? $user->address : '' }}">
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="apt_suit">@lang('Apt/Suit')</label>
            <input type="text" class="form-control input-solid" id="apt_suit"
                   name="apt_suit" placeholder="@lang('Apt/Suit')" value="{{ $edit ? $user->apt_suit : '' }}">
        </div>

        <div class="form-group">
            <label for="zip">@lang('Zip')</label>
            <input type="number" class="form-control input-solid" id="zip" maxlength="10"
                   name="zip" placeholder="@lang('Zip')" value="{{ $edit ? $user->zip : '' }}">
        </div>

        <div class="form-group">
            <label for="phone">@lang('Phone')</label>
            <input type="text" class="form-control input-solid" id="phone"
                   name="phone" placeholder="918-486-7161" value="{{ $edit ? $user->phone : '' }}">
        </div>
        <div class="form-group">
            <label for="credit_limit">@lang('Credit Limit')</label>
            <input type="number" class="form-control input-solid" id="credit_limit"
                   name="terms" placeholder="@lang('100,2000,40000')" value="{{ $edit ? $user->credit_limit : '' }}">
        </div>

    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="city">@lang('City')</label>
            <input type="text" class="form-control input-solid" id="city"
                   name="city" placeholder="@lang('City')" required value="{{ $edit ? $user->city : '' }}">
        </div>


        <div class="form-group">
            <label for="address">@lang('Company Contact')</label>
            <input type="text" class="form-control input-solid" id="company_contact"
                   name="company_contact" placeholder="@lang('Company Contact')" required value="{{ $edit ? $user->company_contact : '' }}">
        </div>

        <div class="form-group">
            <label for="price_list">@lang('Price List')</label>
            {!! Form::select('price_list', $prices, $edit ? $user->price_list : '', ['class' => 'form-control input-solid']) !!}
        </div>

        <div class="form-group">
            <label for="carrier_id">@lang('Carrier')</label>
            {!! Form::select('carrier_id', $carriers, $edit ? $user->carrier_id : '', ['class' => 'form-control input-solid']) !!}
        </div>

    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="state">@lang('State')</label>
            <input type="text" class="form-control input-solid" id="state"
                   name="state" placeholder="@lang('State')" maxlength="3" value="{{ $edit ? $user->state : '' }}">
        </div>
        <div class="form-group">
            <label for="terms">@lang('Terms')</label>
            <input type="text" class="form-control input-solid" id="terms"
                   name="terms" placeholder="@lang('i.e N1, CC')" value="{{ $edit ? $user->terms : '' }}">
        </div>
        <div class="form-group">
            <label for="contract_code">@lang('Contract Code')</label>
            <input type="number" class="form-control input-solid" id="contract_code"
                   name="contract_code" placeholder="@lang('Contract Code')" value="{{ $edit ? $user->contract_code : '' }}">
        </div>

{{--        <div class="form-group">--}}
{{--            <label for="birthday">@lang('Date of Birth')</label>--}}
{{--            <div class="form-group">--}}
{{--                <input type="text"--}}
{{--                       name="birthday"--}}
{{--                       id='birthday'--}}
{{--                       value="{{ $edit && $user->birthday ? $user->present()->birthday : '' }}"--}}
{{--                       class="form-control input-solid" />--}}
{{--            </div>--}}
{{--        </div>--}}

    </div>

    @if ($edit)
        <div class="col-md-12 mt-2">
            <button type="submit" class="btn btn-primary" id="update-details-btn">
                <i class="fa fa-refresh"></i>
                @lang('Update Details')
            </button>
        </div>
    @endif
</div>
