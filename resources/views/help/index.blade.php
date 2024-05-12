@extends('layouts.app')

@section('page-title', __('Help & FAQ'))
@section('page-heading', __('Help & FAQ'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('Help & FAQ')
    </li>
@stop

@section ('styles')
    <style>
        .help-search-container {
            background: url('/assets/img/virgin-farms.png') no-repeat center center;
            background-size: cover;
            color: #333; /* Adjust text color for visibility */
            padding: 100px 0;
            text-align: center;
        }
        .search-bar {
            max-width: 700px; /* Adjust width as needed */
            margin: auto;
        }
        .search-input {
            border-radius: 20px;
            padding: 10px 20px;
            width: 100%;
        }
        .search-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25); /* Bootstrap blue glow */
        }

        .content-box {
            margin-top: 17px;
            background: #ffffff; /* Background color */
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Shadow for the box */
        }
    </style>
@stop

@section('content')

@include('partials.messages')

<div class="card">

    @if(myRoleName() == 'Admin')
        <a target="_blank" href="{{route('help.faq.edit')}}" class="btn btn-icon"
           title="@lang('Click To Edit Help and FAQs page')" data-toggle="tooltip" data-placement="top">
            <i class="fas fa-edit text-danger fa-2x"></i>
        </a>
    @endif

    <div class="card-body">

        <div class="container-fluid help-search-container">
            <h1>Help & Frequently Asked Questions (FAQs) </h1>
            <div class="search-bar">
                <input type="text" class="form-control search-input" placeholder="Search for any help, question etc">
            </div>
        </div>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="content-box">
                        {!! $text->value !!}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@stop

@section('scripts')

    @include('partials.toaster-js')
{{--    <script src="{{ url('assets/plugins/daterangepicker/daterangepicker.min.js') }}"></script>--}}
{{--    <script type="text/javascript" src="{{ asset('assets/plugins/x-editable/bootstrap-editable.min.js') }}" ></script>--}}

    <script>

    </script>
@stop
