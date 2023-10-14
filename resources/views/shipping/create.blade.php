{!! Form::open(['route' => 'shipping.address.create', 'id' => 'user-form']) !!}
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
                                <input type="text" class="form-control input-solid" id="company"
                                       name="company" placeholder="@lang('Company')" value="">
                            </div>

                            <div class="form-group">
                                <label for="status">@lang('Select User')</label>
                                {!! Form::select('user_id', $users, auth()->id(),['class' => 'form-control input-solid', 'id' => 'status']) !!}
                                <small class="text-warning">Client can select only his name.</small>
                            </div>
                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="phone">@lang('Phone')</label>
                                <input type="text" class="form-control input-solid" id="phone"
                                       name="phone" placeholder="@lang('Phone')" value="">
                            </div>
                            <div class="form-group">
                                <label for="address">@lang('Address')</label>
                                <textarea name="address" rows="5" class="form-control input-solid" id="address"></textarea>
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
