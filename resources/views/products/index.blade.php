@extends('layouts.app')

@section('page-title', __('Manage Products'))
@section('page-heading', __('Manage Products'))

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
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    Hello All products here

                </div>
            </div>
        </div>
    </div>

@stop
