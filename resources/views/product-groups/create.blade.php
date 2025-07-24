@extends('layouts.app')

@section('page-title', __('Product Groups'))
@section('page-heading', __('Products Groups'))

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">@lang('Manage Groups')</li>
@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/product-group.css') }}">
@stop

@section('content')
    @include('partials.messages')

    <form action="{{ route('product-groups.store') }}" method="POST" id="group-form">
        @include('product-groups.partials.form', ['edit' => false])
    </form>
@stop

@section('scripts')
    <script src="{{ url('assets/js/product-group-form.js') }}"></script>
    @include('partials.toaster-js')
@stop
