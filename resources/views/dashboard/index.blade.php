@extends('layouts.app')

@section('page-title', __('Dashboard'))
@section('page-heading', __('Dashboard'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('Dashboard')
    </li>
@stop

@section('styles')

    <style>
        /*table {*/
            /*border-collapse: separate;*/
            /*border-spacing: 0 15px;*/
        /*}*/

        /*th {*/
            /*background-color: #4287f5;*/
            /*color: white;*/
        /*}*/

        /*th,*/
        /*td {*/
            /*width: 150px;*/
            /*text-align: center;*/
            /*border: 1px solid black;*/
            /*padding: 5px;*/
        /*}*/

        /*h2 {*/
            /*color: #4287f5;*/
        /*}*/
    </style>
@endsection

@section('content')
    @include('partials.messages')

<div class="row">
    @foreach (\Vanguard\Plugins\Vanguard::availableWidgets(auth()->user()) as $widget)
        @if ($widget->width)
            <div class="col-md-{{ $widget->width }}">
        @endif
            {!! app()->call([$widget, 'render']) !!}
        @if($widget->width)
            </div>
        @endif
    @endforeach

</div>

    @if(myRoleName() == 'Admin')
        <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" id="users-table-wrapper">
                        <table class="table table-borderless table-striped table-sm">
                            <thead>
                            <tr>
                                <td class="min-width-150"><b>Type:</b></td>
                                <td>
                                    <div>Recent Orders will be here</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" id="users-table-wrapper">
                        <table class="table table-borderless table-striped">
                            <tr>
                                <td class="min-width-150"><b>Type:</b></td>
                                <td>
                                    <div>Recent Future Inventory</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive" id="users-table-wrapper">
                        <table class="table table-borderless table-striped">
                            <thead>
                            <th>
                                Show Low Inventry Or Negative
                            </th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @endif
@stop

@section('scripts')
    {{--@foreach (\Vanguard\Plugins\Vanguard::availableWidgets(auth()->user()) as $widget)--}}
        {{--@if (method_exists($widget, 'scripts'))--}}
            {{--{!! app()->call([$widget, 'scripts']) !!}--}}
        {{--@endif--}}
    {{--@endforeach--}}
@stop
