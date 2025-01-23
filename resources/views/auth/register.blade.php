@extends('layouts.auth')

@section('page-title', __('Sign Up'))

@if (setting('registration.captcha.enabled'))
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endif

@section('content')

    <div class="col-md-8 col-lg-8 col-xl-6 mx-auto my-5p">
        <div class="text-center">
            <img src="{{ url('assets/img/virgin-farms-logo.png') }}" alt="{{ setting('app_name') }}" height="100">
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h4 class="card-title text-center text-uppercase">
                    @lang('Sign Up')
                </h4>

                <div class="p-2">
                    @include('partials/messages')
                    <form role="form" action="<?= url('register') ?>" method="post" id="registration-form"
                          autocomplete="off" enctype="multipart/form-data">
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

                        <!-- Add the new radio buttons -->
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label>
                                    <input type="radio" name="customer_type" checked value="current" id="current_customer"> Current Customer
                                </label>
                                &nbsp;
                                <label>
                                    <input type="radio" name="customer_type" value="new" id="new_customer"> New Customer
                                </label>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-12">
                                <select name="sales_rep"
                                        id="sales_rep"
                                        class="form-control input-solid">
                                    <option value="">Indicate Sales Representative</option>
                                    <option value="Mario">Mario</option>
                                    <option value="Robert">Robert</option>
                                    <option value="Joe">Joe</option>
                                    <option value="Nestor">Nestor</option>
                                    <option value="Peter">Peter</option>
                                    <option value="Esteban">Esteban</option>
                                </select>
                                <p id="new_customer_message" class="text-danger" style="display: none;"><b>Sales Representative will be assigned upon confirmation.</b></p>
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
                                {!! Form::select('state', getStates(), '', ['class' => 'form-control input-solid' , 'id' => 'state_id']) !!}
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
                                <select class="form-control form-control-md"
                                        name="carrier_id"
                                        id="carrier_id"
                                        title="Select Shipping Method how you want to ship the items?"
                                        data-trigger="hover"
                                        data-toggle="tooltip">
                                    <option hidden value="">Select Shipping Method</option>
                                    @foreach(getCarriers() AS $key => $name)
                                        <option value="{{$key}}" > {{$name}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row align-items-center" id="tax-file-input" style="display: none;">
                            <!-- Input Section -->
                            <div class="form-group flex-grow-1 me-3">
                                <label for="tax_file" class="form-label">Tax ID For FL. Customers</label>
                                <input type="file" name="tax_file" id="tax_file" class="form-control input-solid">
                            </div>

                            <!-- Description Section -->
                            <div class="form-group text-muted">
                                <i class="fa fa-search me-2" aria-hidden="true"></i>
                                <a href="https://ritx-fl-sales.bswa.net/" target="_blank" style="font-weight: bold; text-decoration: underline; color: inherit;">
                                    Current Florida Annual Resale Certificate for Sales Tax
                                </a>
                            </div>
                        </div>


                        <div class="form-row">
                            <label for="tax_file" class="form-label">Do you want to receive our latest updates, offers, exclusive content?</label>
                            <div class="form-group col-6">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="email_opt_in" id="opt_in_yes" value="yes">
                                    <label class="form-check-label" for="opt_in_yes">Yes!</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="email_opt_in" id="opt_in_no" value="no">
                                    <label class="form-check-label" for="opt_in_no">No.</label>
                                </div>
                            </div>
                        </div>

                        @if (setting('registration.captcha.enabled'))
                            <div class="form-group my-4">
                                {!! app('captcha')->display() !!}
                            </div>
                        @endif

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-lg btn-block" id="btn-login">
                                @lang('Register')
                            </button>
                        </div>
                    </form>
                </div>

                <div class="text-center text-muted">
                    @if (setting('reg_enabled'))
                        @lang('Already have an account?')
                        <a class="font-weight-bold" href="<?= url("login") ?>">@lang('Login')</a>
                    @endif
                </div>
            </div>
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

    <script>
        $(document).ready(function() {
            $('input[name="customer_type"]').change(function() {
                if ($('#current_customer').is(':checked')) {
                    $('#sales_rep').show();
                    $('#new_customer_message').hide();
                } else if ($('#new_customer').is(':checked')) {
                    $('#sales_rep').hide();
                    $('#sales_rep-error').hide();
                    $('#new_customer_message').show();
                }
            });

            // Listen for change event on the state select input
            $('#state_id').on('change', function() {
                // Get the selected option value
                var selectedState = $(this).val();

                // If Florida (value 10) is selected, show the file input
                if (selectedState == '10') {
                    $('#tax-file-input').show(); // Show the file input
                } else {
                    $('#tax-file-input').hide(); // Hide the file input for other states
                }
            });
        });
    </script>
@stop
