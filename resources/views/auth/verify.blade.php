@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('Verify Your Email Address & Wait for Pending Account Approval By Admin.') }}</h5>

                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                {{ __('A fresh verification link has been sent to your email address.') }}
                            </div>
                        @endif

                        {{ __('Before proceeding, please check your email for a verification link.') }}
                        {{ __('If you did not receive the email') }},
                        <form action="{{ route('verification.resend') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="d-inline btn btn-link p-0">
                                {{ __('click here to request another email') }}
                            </button>.
                        </form>

                        <br><br><hr>

                        <h5 class="fas fa-info-circle"> Meanwhile Check out our help and FAQs Page by <a href="{{ route('help.faq.index') }}">Clicking Here</a>.</h5>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
