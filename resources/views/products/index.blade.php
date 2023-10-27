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
                <div class="card-body mt-0 p-3">

                    <span><b>1. Enter your shipping information	</b></span>
                    <div class="row my-2 flex-md-row flex-column-reverse">
                        <div class="col-md-10 col-sm-12 mt-md-0 mt-1">
                            <form action="" method="GET" id="filters-form" class="border-bottom-light">
                                <div class="input-group custom-search-form">

                                    <input type="date"
                                           class="form-control rounded"
                                           name="search"
                                           title="What is your carrier choice?"
                                           data-trigger="hover"
                                           data-toggle="tooltip"
                                           value="{{ \Request::get('search') }}">

                                    <select name="search_by" class="form-control ml-2 rounded"
                                            title="What is your carrier choice?"
                                            data-trigger="hover"
                                            data-toggle="tooltip"
                                    >
                                        <option hidden value="">Search By</option>
                                        @foreach([] AS $key => $searchBy)
                                            <option value="{{$key}}"
                                                {{ Request::get('search_by') == $key ? 'selected' : '' }}>
                                                {{$searchBy}}
                                            </option>
                                        @endforeach
                                        <option value="" >Clear Option</option>
                                    </select>

                                    <input type="date"
                                           class="form-control rounded ml-2"
                                           name="search"
                                           title="What is your PO#? (optional)"
                                           data-trigger="hover"
                                           data-toggle="tooltip"
                                           value="{{ \Request::get('search') }}">

                                    <span class="input-group-append">
                                    @if (\Request::has('search') && \Request::get('search') != '')
                                            <a href="{{ route('orders') }}"
                                               class="btn btn-light d-flex align-items-center text-muted"
                                               role="button">
                                                <i class="fas fa-times"></i>
                                        </a>
                                        @endif
                                        <button class="btn btn-secondary ml-1" type="submit" id="search-users-btn">
                                        <i class="fas fa-search "></i>
                                    </button>
                                </span>
                                </div>
                            </form>
                        </div>

                        {{--@permission('orders.filter')--}}
                        @include('products._partial.filter')
                        {{--@endpermission--}}


                    </div>

                </div>
            </div>
        </div>
    </div>
@stop
