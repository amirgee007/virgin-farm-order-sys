@extends('layouts.app')

@section('page-title', __('Manage Carriers'))
@section('page-heading', __('Manage Carriers'))

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">
        @lang('Carriers')
    </li>
@stop

@section('content')

    @include('partials.messages')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    Hello All Carriers here

                </div>
            </div>
        </div>
    </div>

@stop
