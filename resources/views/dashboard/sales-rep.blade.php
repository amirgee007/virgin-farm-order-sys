@extends('layouts.app')

@section('page-title', __('Sales Rep'))
@section('page-heading', __('Sales Rep'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('Sales Rep')
    </li>
@stop

@section('styles')

@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Meet Your Sales Representative: John Doe</h5>
        <div class="card-body">
            <h5 class="card-title">Exclusive Offers Just for You!</h5>
            <p class="card-text">John is here to help you find the best deals and assist with all your inquiries.</p>
            <a href="#" class="btn btn-primary">Contact John</a>
        </div>
    </div>
@stop

@section('scripts')

@stop
