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

    <style>
        .dutch{
            background: rgba(213, 25, 38, 0.78);
        }
        .virginFarm {
            background: #5a7161;
        }
    </style>
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
                <small><span class="virginFarm">Green</span> means its Virgin farms and <span class="dutch">Red</span> means its dutch category.</small>
                <table class="table table-bordered ">
                    <thead>
                    <tr>
                        <th>@lang('#')</th>
                        <th>@lang('Item Id')(Unique)</th>
                        <th class="min-width-100">@lang('Name')</th>
                        <th class="min-width-100">@lang('Created')</th>
                        <th class="min-width-80">@lang('Updated')</th>
                        <th class="min-width-80">@lang('Type')</th>
                        <th class="min-width-80">@lang('Action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (count($categories))
                        @foreach ($categories as $index => $category)
                            <tr class="">
                                <td class="align-middle {{$category->product_type == 'dutch' ? 'dutch' : 'virginFarm'}}">{{ ++$index }}</td>
                                <td class="align-middle">
                                    <a class="editable"
                                       style="cursor:pointer;"
                                       data-name="category_id"
                                       data-type="number"
                                       data-emptytext="empty"
                                       data-pk="{{$category->id}}"
                                       data-url="{{route('categories.update')}}"
                                       data-value="{{ $category->category_id }}">
                                    </a>
                                </td>
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

                                <td>
                                    <select class="productTypeSelect" data-id="{{$category->id}}">
                                        <option value="vf" {{$category->product_type == 'vf' ? 'selected' :''}} >Virgin Farms</option>
                                        <option value="dutch" {{$category->product_type == 'dutch' ? 'selected' :''}} >Dutch Flower</option>
                                    </select>
                                </td>

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

    <script>
        $.fn.editable.defaults.mode = 'inline';
        $.fn.editable.defaults.ajaxOptions = {
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        };

        $('.editable').editable();

        $('.productTypeSelect').change(function() {
            var product_type = $(this).val();
            var rowId = $(this).data('id');

            $.ajax({
                url: '{{route('categories.update')}}',  // This should match the route defined in Laravel
                type: 'POST',
                data: {
                    pk: rowId,  // Pass the row ID to identify which record to update
                    value: product_type,
                    name : 'product_type'
                },
                success: function(response) {
                    toastr.success('Product Type updated succesfully.');
                },
                error: function(response) {
                    toastr.error('Something went wrong plz check with admin.');
                }
            });
        });
    </script>
@stop
