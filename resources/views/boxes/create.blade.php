{!! Form::open(['route' => 'box.create.update', 'id' => 'user-form']) !!}
<div class="modal-body">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="description">@lang('Description')</label>
                                <textarea required name="description" rows="5" class="form-control input-solid" id="description"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="length">@lang('Length')</label>
                                <input required type="number" class="form-control input-solid" id="length"
                                       name="length" placeholder="@lang('length')">
                            </div>
                            <div class="form-group">
                                <label for="width">@lang('Width')</label>
                                <input required type="number" class="form-control input-solid" id="width"
                                       name="width" placeholder="@lang('Width')">
                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="height">@lang('Height')</label>
                                <input required type="number" class="form-control input-solid" id="height"
                                       name="height" placeholder="@lang('height')">
                            </div>
                            <div class="form-group">
                                <label for="min_value">@lang('Min Value')</label>
                                <input required type="number" class="form-control input-solid" id="min_value"
                                       name="min_value" placeholder="@lang('Minimum Value')">
                            </div>
                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="weight">@lang('Weight')</label>
                                <input required type="number" class="form-control input-solid" id="weight"
                                       name="weight" placeholder="@lang('Weight')">
                            </div>

                            <div class="form-group">
                                <label for="max_value">@lang('Max Value')</label>
                                <input required type="number" class="form-control input-solid" id="max_value"
                                       name="max_value" placeholder="@lang('Minimum Value')">
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
    <button type="submit" class="btn btn-primary">@lang('Create Box')</button>
</div>

{!! Form::close() !!}
