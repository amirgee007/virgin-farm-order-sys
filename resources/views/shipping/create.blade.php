{!! Form::open(['route' => 'ship.address.create.update', 'id' => 'user-form']) !!}
<div class="modal-body">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">@lang('Name')</label>
                                <input type="text" class="form-control input-solid" id="name"
                                       name="name" placeholder="@lang('Name')" value="">
                            </div>
                            <div class="form-group">
                                <label for="address">@lang('Company')</label>
                                <input type="text" class="form-control input-solid" id="company_name"
                                       name="company_name" placeholder="@lang('Company')" required>
                            </div>

                            <div class="form-group">
                                <label for="state_id">@lang('Select State')</label>
                                {!! Form::select('state_id', $states, 0, ['class' => 'form-control input-solid', 'id' => 'state_id']) !!}
                            </div>

                            <div class="form-group">
                                <label for="zip_code">@lang('Zip Code')</label>
                                <input type="text" class="form-control input-solid" id="zip_code" name="zip_code" placeholder="@lang('Zip Code')" required>
                            </div>
                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="phone">@lang('Phone')</label>
                                <input type="text" class="form-control input-solid" id="phone" name="phone" placeholder="@lang('Phone')" value="">
                            </div>
                            <div class="form-group">
                                <label for="address">@lang('Address')</label>
                                <input type="text" class="form-control input-solid" id="address" name="address" placeholder="@lang('Phone')" value="">
                            </div>


                            <div class="form-group">
                                <label for="city_id">@lang('Select City')</label>
                                {!! Form::select('city_id', [], 0, ['class' => 'form-control input-solid', 'id' => 'city_id']) !!}
                            </div>

                        </div>

                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary">@lang('Create Shipping Address')</button>
</div>

{!! Form::close() !!}
