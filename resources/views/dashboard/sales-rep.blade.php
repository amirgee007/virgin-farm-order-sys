@extends('layouts.app')

@section('page-title', __('Sales Rep'))
@section('page-heading', __('Sales Rep'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('Sales Representative')
    </li>
@stop

@section('styles')

@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Meet Your Sales Representative <b>{{ Auth::user()->sales_rep }}</b></h5>
        <div class="card-body">
            <img src="{{ asset('assets/img/salerep/' . Auth::user()->sales_rep . '.png') }}" alt="{{ Auth::user()->sales_rep }}" class="img-fluid">
        </div>
    </div>
@stop

@section('scripts')

@stop
