<div class="row">

    @php $isAdmin = myRoleName() == 'Admin'; @endphp

    @if($isAdmin)
        <div class="col-md-4">
            <div class="form-group">
                <label for="first_name">@lang('Role')</label>
                {!! Form::select('role_id', $roles, $edit ? $user->role->id : '',
                    ['class' => 'form-control input-solid', 'id' => 'role_id', $profile ? 'disabled' : '']) !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="status">@lang('Status')</label>
                {!! Form::select('status', $statuses, $edit ? $user->status : '',
                    ['class' => 'form-control input-solid', 'id' => 'status', $profile ? 'disabled' : '']) !!}
            </div>
        </div>
    @endif

    <div class="col-md-4">
        <div class="form-group">
            <label for="first_name">@lang('First Name')</label>
            <input type="text" class="form-control input-solid" id="first_name" required
                   name="first_name" placeholder="@lang('First Name')" value="{{ $edit ? $user->first_name : '' }}">
        </div>
    </div>


    <div class="col-md-4">
        <div class="form-group">
            <label for="last_name">@lang('Last Name')</label>
            <input type="text" class="form-control input-solid" id="last_name"
                   name="last_name" placeholder="@lang('Last Name')" value="{{ $edit ? $user->last_name : '' }}">
        </div>
    </div>


    <div class="col-md-4">
        <div class="form-group">
            <label for="customer_number">@lang('Customer Number')</label>

            <input type="text" class="form-control input-solid" id="customer_number"
                   {{$isAdmin ? '' : 'readonly'}}
                   name="customer_number" placeholder="@lang('Customer Number')"
                   value="{{ $edit ? $user->customer_number : 0 }}">
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="sales_rep">@lang('Sales Rep')</label>
            @if($isAdmin)
                {!! Form::select('sales_rep', $salesRep, $edit ? $user->sales_rep : '', ['class' => "form-control input-solid ", 'id' => 'sales_rep']) !!}
            @else
                <input type="text" value="{{$user->sales_rep}}"  readonly class="form-control input-solid">
            @endif
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
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="zip">@lang('Zip')</label>
            <input type="number" class="form-control input-solid" id="zip" maxlength="10"
                   name="zip" placeholder="@lang('Zip')" value="{{ $edit ? $user->zip : '' }}">
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="phone">@lang('Phone')</label>
            <input type="text" class="form-control input-solid" id="phone"
                   name="phone" placeholder="918-486-7161" value="{{ $edit ? $user->phone : '' }}">
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="city">@lang('City')</label>
            <input type="text" class="form-control input-solid" id="city"
                   name="city" placeholder="@lang('City')" required value="{{ $edit ? $user->city : '' }}">
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="address">@lang('Company Name')</label>
            <input type="text" class="form-control input-solid" id="company_name"
                   name="company_name" placeholder="@lang('Company Name')" required
                   value="{{ $edit ? $user->company_name : '' }}">
        </div>
    </div>


    <div class="col-md-4">
        <div class="form-group">
            <label for="state">@lang('State')</label>
            {!! Form::select('state', $states, $edit ? $user->state : '', ['class' => 'form-control input-solid']) !!}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="terms">@lang('Terms')</label>
            {!! Form::select('terms', $terms, $edit ? $user->terms : '', ['class' => 'form-control input-solid']) !!}
        </div>
    </div>

    @if($isAdmin)
        <div class="col-md-4">
            <div class="form-group">
                <label for="price_list">@lang('Price List')</label>
                {!! Form::select('price_list', $prices, $edit ? $user->price_list : '', ['class' => 'form-control input-solid']) !!}
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="contract_code">@lang('Contract Code')</label>
                <input type="number" class="form-control input-solid" id="contract_code"
                       name="contract_code" placeholder="@lang('Contract Code')"
                       value="{{ $edit ? $user->contract_code : '' }}">
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label for="credit_limit">@lang('Credit Limit')</label>
                <input type="number" class="form-control input-solid" id="credit_limit"
                       name="credit_limit" placeholder="@lang('100,2000,40000')"
                       value="{{ $edit ? $user->credit_limit : '' }}">
            </div>
        </div>

        <div class="col-md-4">
        <div class="form-group">
            <label for="carrier_id">@lang('Carrier')</label>
            {!! Form::select('carrier_id', $carriers, $edit ? $user->carrier_id : '', ['class' => 'form-control input-solid']) !!}
        </div>
    </div>
    @endif

    @if ($edit)
        <div class="col-md-12 mt-2">
            <button type="submit" class="btn btn-primary" id="update-details-btn">
                <i class="fa fa-refresh"></i>
                @lang('Update Details')
            </button>
        </div>
    @endif
</div>
