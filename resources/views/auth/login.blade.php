@extends('layouts.auth')

@section('page-title', trans('Login'))

@if (setting('registration.captcha.enabled'))
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endif

@section('content')

<div class="col-md-8 col-lg-6 col-xl-5 mx-auto my-5p" id="login">
    <div class="text-center">
        <img src="{{ url('assets/img/virgin-farms-logo.png') }}" alt="{{ setting('app_name') }}" height="100">
    </div>

    <div class="card mt-5">
        <div class="card-body">
            <h5 class="card-title text-center mt-2 text-uppercase">
                @lang('Login')
            </h5>

            <div class="p-4">
{{--                @include('auth.social.buttons')--}}

                @include('partials.messages')

                <form role="form" action="<?= url('login') ?>" method="POST" id="login-form" autocomplete="off" class="mt-3">

                    <input type="hidden" value="<?= csrf_token() ?>" name="_token">

                    @if (Request::has('to'))
                        <input type="hidden" value="{{ Request::get('to') }}" name="to">
                    @endif

                    <div class="form-group">
                        <label for="username" class="sr-only">@lang('Email or Username')</label>
                        <input type="text"
                                name="username"
                                id="username"
                                class="form-control input-solid"
                                placeholder="@lang('Email or Username')"
                                value="{{ old('username') }}">
                    </div>

                    <div class="form-group password-field">
                        <label for="password" class="sr-only">@lang('Password')</label>
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control input-solid"
                               placeholder="@lang('Password')">
                    </div>

                    {{-- Only display captcha if it is enabled --}}
                    @if (setting('registration.captcha.enabled'))
                        <div class="form-group my-4">
                            {!! app('captcha')->display() !!}
                        </div>
                    @endif
                    {{-- end captcha --}}

                    @if (setting('remember_me'))
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" name="remember" id="remember" value="1"/>
                            <label class="custom-control-label font-weight-normal" for="remember">
                                @lang('Remember me?')
                            </label>
                        </div>
                    @endif


                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary btn-lg btn-block" id="btn-login">
                            @lang('Log In')
                        </button>
                    </div>
                </form>

                @if (setting('forgot_password'))
                    <a href="<?= route('password.request') ?>" class="forgot">@lang('I forgot my password')</a>
                @endif
            </div>
            <div class="text-center text-muted">
                @if (setting('reg_enabled'))
                    @lang("Don't have an account?")
                    <a class="font-weight-bold" href="<?= url("register") ?>">@lang('Sign Up')</a>
                @endif
            </div>
        </div>
    </div>


    <div class="text-center text-muted">
        Visit our main website <a class="font-weight-bold text-danger" target="_blank" href="https://www.virginfarms.com/">virginfarms.com</a> to explore our floral catalog, subscribe to our Weekly Special newsletter, and explore our services.
    </div>
</div>

@stop

@section('scripts')
    {!! HTML::script('assets/js/as/login.js') !!}
    {!! JsValidator::formRequest('Vanguard\Http\Requests\Auth\LoginRequest', '#login-form') !!}
@stop
