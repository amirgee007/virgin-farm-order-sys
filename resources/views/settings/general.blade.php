@extends('layouts.app')

@section('page-title', __('General Settings'))
@section('page-heading', __('General Settings'))

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">
        @lang('Settings')
    </li>
    <li class="breadcrumb-item active">
        @lang('General')
    </li>
@stop

@section('content')

@include('partials.messages')

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">

                {!! Form::open(['route' => 'settings.general.update', 'id' => 'general-settings-form']) !!}

                <div class="form-group">
                    <label for="name">@lang('Webshop Name')</label>
                    <input type="text" class="form-control input-solid" id="app_name"
                           name="app_name" value="{{ setting('app_name') }}">
                </div>

                <button type="submit" class="btn btn-primary">
                    @lang('Update')
                </button>

                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                {!! Form::open(['route' => 'settings.general.update', 'id' => 'general-settings-form']) !!}
                <input type="hidden" name="pop_up_dynamic" value="YES">

                <div class="form-group">
                    <label for="pop_up_text">@lang('DynamicText')</label>
                    <input type="text" class="form-control input-solid" maxlength="100" required id="pop_up_text" name="pop_up_text" value="{{$popup->value}}">
                </div>

                <div class="form-group">
                    <label for="start_date">@lang('Start Date')</label>
                    <input type="date" class="form-control input-solid" min="{{ date('Y-m-d') }}" required id="start_date" name="start_date" value="{{$popup->label}}">
                </div>

                <div class="form-group">
                    <label for="end_date">@lang('End Date')</label>
                    <input type="date" class="form-control input-solid" min="{{ date('Y-m-d') }}" required id="end_date" name="end_date" value="{{$popup->extra_info}}">
                </div>

                <button type="submit" class="btn btn-primary">
                    @lang('Update')
                </button>

                {{ Form::close() }}
            </div>

        </div>
    </div>
</div>
@stop
