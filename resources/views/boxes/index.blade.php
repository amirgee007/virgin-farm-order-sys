@extends('layouts.app')

@section('page-title', __('Manage Boxes'))
@section('page-heading', __('Manage Boxes'))

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

                    Hello All Boxes here

                </div>
            </div>
        </div>
    </div>

@stop
