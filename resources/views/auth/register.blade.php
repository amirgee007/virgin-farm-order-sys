@extends('layouts.auth')

@section('page-title', __('Sign Up'))

@section('content')

    <div class="col-md-8 col-lg-8 col-xl-6 mx-auto my-5p">
        <div class="text-center">
            <img src="{{ url('assets/img/vanguard-logo.png') }}" alt="{{ setting('app_name') }}" height="100">
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title text-center text-uppercase">
                    @lang('Register')
                </h5>

                <div class="p-3">
                    @include('partials/messages')

                    <form role="form" action="<?= url('register') ?>" method="post" id="registration-form"
                          autocomplete="off" class="mt-3">
                        <input type="hidden" value="<?= csrf_token() ?>" name="_token">

                        <div class="form-row">
                            <div class="form-group col-6">
                                <input type="text"
                                       name="first_name"
                                       id="first_name"
                                       class="form-control input-solid"
                                       placeholder="First Name"
                                       value="{{ old('first_name') }}"
                                       required>
                            </div>
                            <div class="form-group col-6">
                                <input type="text"
                                       name="last_name"
                                       id="last_name"
                                       class="form-control input-solid"
                                       placeholder="Last Name"
                                       value="{{ old('last_name') }}"
                                       required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-6">
                                <input type="text"
                                       name="company_name"
                                       id="company_name"
                                       class="form-control input-solid"
                                       placeholder="Company Name"
                                       value="{{ old('company_name') }}"
                                       required>
                            </div>
                            <div class="form-group col-6">
                                <input type="tel"
                                       name="phone"
                                       id="phone"
                                       class="form-control input-solid"
                                       placeholder="Phone No"
                                       value="{{ old('phone') }}"
                                       required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-6">
                                <input type="email"
                                       name="email"
                                       id="email"
                                       class="form-control input-solid"
                                       placeholder="Email"
                                       value="{{ old('email') }}">
                            </div>
                            <div class="form-group col-6">
                                <input type="text"
                                       name="username"
                                       id="username"
                                       class="form-control input-solid"
                                       placeholder="Username"
                                       value="{{ old('username') }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-6">
                                <input type="password"
                                       name="password"
                                       id="password"
                                       class="form-control input-solid"
                                       placeholder="Password">
                            </div>
                            <div class="form-group col-6">
                                <input type="password"
                                       name="password_confirmation"
                                       id="password_confirmation"
                                       class="form-control input-solid"
                                       placeholder="Confirm Password">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-12">
                                <select name="sales_rep"
                                        id="sales_rep"
                                        class="form-control input-solid"
                                        required>
                                    <option value="">If you are an older customer, please select here</option>
                                    <option value="Mario">Mario</option>
                                    <option value="Robert">Robert</option>
                                    <option value="Joe">Joe</option>
                                    <option value="Nestor">Nestor</option>
                                    <option value="Peter">Peter</option>
                                    <option value="Esteban">Esteban</option>
                                </select>
                            </div>
                            <div class="form-group col-12">
            <textarea name="address"
                      id="address"
                      class="form-control input-solid"
                      placeholder="Shipping Address"
                      rows="3"
                      required>{{ old('address') }}</textarea>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-12">
                                <input type="text"
                                       name="apt_suit"
                                       id="apt_suit"
                                       class="form-control input-solid"
                                       placeholder="Appt/Suite"
                                       value="{{ old('apt_suit') }}"
                                       required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-6">
                                <input type="text"
                                       name="city"
                                       id="city"
                                       class="form-control input-solid"
                                       placeholder="City"
                                       value="{{ old('city') }}"
                                       required>
                            </div>

                            <div class="form-group col-6">
                                <input type="text"
                                       name="state"
                                       id="state"
                                       class="form-control input-solid"
                                       placeholder="State"
                                       value="{{ old('state') }}"
                                       required>
                            </div>

                        </div>

                        <div class="form-row">
                            <div class="form-group col-6">
                                <input type="text"
                                       name="zip"
                                       id="zip"
                                       class="form-control input-solid"
                                       placeholder="Zip"
                                       value="{{ old('zip') }}"
                                       required>
                            </div>

                            <div class="form-group col-6">
                                <input type="text"
                                       name="ship_method"
                                       id="ship_method"
                                       class="form-control input-solid"
                                       placeholder="Shipping Method"
                                       value="{{ old('ship_method') }}"
                                       required>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-lg btn-block" id="btn-login">
                                @lang('Register')
                            </button>
                        </div>
                    </form>


                </div>
            </div>
        </div>

        <div class="text-center text-muted">
            @if (setting('reg_enabled'))
                @lang('Already have an account?')
                <a class="font-weight-bold" href="<?= url("login") ?>">@lang('Login')</a>
            @endif
        </div>

    </div>

    @if (setting('tos'))
        <div class="modal fade" id="tos-modal" tabindex="-1" role="dialog" aria-labelledby="tos-label">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tos-label">@lang('Terms of Service')</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @include('auth.tos')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            @lang('Close')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

@stop

@section('scripts')
    {!! JsValidator::formRequest('Vanguard\Http\Requests\Auth\RegisterRequest', '#registration-form') !!}
@stop
