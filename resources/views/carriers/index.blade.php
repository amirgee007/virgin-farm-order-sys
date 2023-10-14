@extends('layouts.app')

@section('page-title', __('Manage Carriers'))
@section('page-heading', __('Manage Carriers'))

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">
        @lang('Carriers')
    </li>
@stop

@section ('styles')
    <link media="all" type="text/css" rel="stylesheet" href="{{ url('assets/plugins/x-editable/bootstrap-editable.css') }}">
@stop

@section('content')

    @include('partials.messages')

    <div class="card">
        <div class="card-body">
            <form role="form" action="{{ route('carriers.create.update') }}" method="POST" >
                @csrf
                <div class="form-row align-items-center float-right">
                    <div class="col-auto">
                        <div class="input-group mb-2 input-group-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-truck"></i>
                                </div>
                            </div>
                            <input type="text" required class="form-control" name="carrier_name" id="inlineFormInputGroup" placeholder="Add New Carrier">
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
                        <th class="min-width-80">@lang('ID')</th>
                        <th class="min-width-100">@lang('Name')</th>
                        <th class="min-width-80">@lang('Created')</th>
                        <th class="min-width-80">@lang('Updated')</th>
                        <th class="min-width-80">@lang('Actions')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (count($carriers))
                        @foreach ($carriers as $index => $carrier)
                            <tr>

                                <td class="align-middle">{{ ++$index }}</td>

                                <td class="align-middle">
                                    <a class="editable"
                                       style="cursor:pointer;"
                                       data-name="carrier_name"
                                       data-type="text"
                                       data-emptytext="empty"
                                       data-pk="{{$carrier->id}}"
                                       data-url="{{route('carriers.create.update')}}"
                                       data-value="{{ $carrier->carrier_name }}">
                                    </a>
                                </td>

                                <td class="align-middle">{{ dateFormatMy($carrier->created_at) }}</td>
                                <td class="align-middle">{{ diff4Human($carrier->updated_at) }}</td>

                                <td class="align-middle">
                                    <a href="{{ route('carriers.index', $carrier->id) }}"
                                       class="btn btn-icon"
                                       title="@lang('Delete Carrier')"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       data-method="GET"
                                       data-confirm-title="@lang('Please Confirm')"
                                       data-confirm-text="@lang('Are you sure that you want to delete this carrier?')"
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

        $("#user_id").change(function () {
            $("#users-form").submit();
        });
    </script>
@stop
