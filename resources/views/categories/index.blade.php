@extends('layouts.app')

@section('page-title', __('Manage Categories'))
@section('page-heading', __('Manage Categories'))

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">
        @lang('Categories')
    </li>
@stop

@section ('styles')
    <link media="all" type="text/css" rel="stylesheet" href="{{ url('assets/plugins/x-editable/bootstrap-editable.css') }}">
@stop

@section('content')

    @include('partials.messages')

    <div class="card">
        <div class="card-body">
            <form role="form" action="{{ route('categories.update') }}" method="POST" >
                @csrf
                <div class="form-row align-items-center float-right">
                    <div class="col-auto">
                        <div class="input-group mb-2 input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                            </div>
                            <input type="text" required class="form-control" name="category_name" id="inlineFormInputGroup" placeholder="Add New Category">
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary mb-2 btn-sm">Create</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive" id="users-table-wrapper">
                <table class="table table-borderless table-striped">
                    <thead>
                    <tr>
                        <th class="min-width-80">@lang('#')</th>
                        <th class="min-width-100">@lang('Item Id')</th>
                        <th class="min-width-100">@lang('Name')</th>
                        <th class="min-width-80">@lang('Created')</th>
                        <th class="min-width-80">@lang('Updated')</th>
                        <th class="min-width-80">@lang('Action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (count($categories))
                        @foreach ($categories as $index => $category)
                            <tr>
                                <td class="align-middle">{{ ++$index }}</td>
                                <td class="align-middle">{{ $category->category_id }}</td>
                                <td class="align-middle">
                                    <a class="editable"
                                       style="cursor:pointer;"
                                       data-name="description"
                                       data-type="text"
                                       data-emptytext="empty"
                                       data-pk="{{$category->id}}"
                                       data-url="{{route('categories.update')}}"
                                       data-value="{{ $category->description }}">
                                    </a>
                                </td>

                                <td class="align-middle">{{ dateFormatMy($category->created_at) }}</td>
                                <td class="align-middle">{{ diff4Human($category->updated_at) }}</td>
                                <td class="align-middle">
                                    <a href="{{ route('categories.delete', $category->id) }}"
                                       class="btn btn-icon"
                                       title="@lang('Delete Category')"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       data-method="DELETE"
                                       data-confirm-title="@lang('Please Confirm')"
                                       data-confirm-text="@lang('Are you sure that you want to delete this category?')"
                                       data-confirm-delete="@lang('Yes, delete it!')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8"><em>@lang('No address found.')</em></td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@stop

@section('scripts')

    @include('partials.toaster-js')
    <script type="text/javascript" src="{{ asset('assets/plugins/x-editable/bootstrap-editable.min.js') }}" ></script>
    {!! JsValidator::formRequest('Vanguard\Http\Requests\CreateShipAddressRequest', '#user-form') !!}
    <script>
        $.fn.editable.defaults.mode = 'inline';
        $.fn.editable.defaults.ajaxOptions = {
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        };

        $('.editable').editable();
    </script>
@stop
