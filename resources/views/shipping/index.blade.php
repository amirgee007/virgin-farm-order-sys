@extends('layouts.app')

@section('page-title', __('Shipping Addresses'))
@section('page-heading', __('Shipping Addresses'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('Shipping Addresses')
    </li>
@stop

@section ('styles')
    <link media="all" type="text/css" rel="stylesheet" href="{{ url('assets/plugins/x-editable/bootstrap-editable.css') }}">
@stop

@section('content')

@include('partials.messages')

<div class="card">
    <div class="card-body">

        <form action="" method="GET" id="users-form" class="pb-2 mb-3 border-bottom-light">
            <div class="row my-3 flex-md-row flex-column-reverse">
                <div class="col-md-8 mt-md-0 mt-2">
                    <div class="input-group custom-search-form">
                        <input type="text"
                               class="form-control input-solid"
                               name="search"
                               value="{{ Request::get('search') }}"
                               placeholder="@lang('Search for client address')">

                            <span class="input-group-append">
                                @if (Request::has('search') && Request::get('search') != '')
                                <a href="{{ route('shipping.address.index') }}"
                                   class="btn btn-light d-flex align-items-center text-muted"
                                   role="button">
                                        <i class="fas fa-times"></i>
                                </a>
                            @endif
                            <button class="btn btn-light" type="submit" id="search-users-btn">
                                    <i class="fas fa-search text-muted"></i>
                                </button>
                            </span>
                    </div>
                </div>

                <div class="col-md-2 mt-2 mt-md-0">
                    {!!
                        Form::select(
                            'user_id',
                            $users,
                            Request::get('user_id'),
                            ['id' => 'user_id', 'class' => 'form-control input-solid']
                        )
                    !!}
                </div>

                <div class="col-md-2">
                    <a href="#" class="btn btn-primary btn-rounded float-right btn-sm" data-toggle="modal" data-target="#createAddressModal">
                        <i class="fas fa-plus mr-2"></i>
                        @lang('Add Shipping Address')
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive" id="users-table-wrapper">
            <table class="table table-borderless table-striped">
                <thead>
                <tr>
                    <th class="min-width-80">@lang('ID')</th>
                    <th class="min-width-150">@lang('User')</th>
                    <th class="min-width-100">@lang('Name')</th>
                    <th class="min-width-80">@lang('Company')</th>
                    <th class="min-width-80">@lang('Phone')</th>
                    <th class="min-width-80">@lang('Address')</th>
                    <th class="min-width-80">@lang('Created')</th>
                    <th class="min-width-80">@lang('Updated')</th>
                    <th class="min-width-80">@lang('Actions')</th>
                </tr>
                </thead>
                <tbody>
                    @if (count($addresses))
                        @foreach ($addresses as $index => $address)
                            <tr>

                                <td class="align-middle">{{ ++$index }}</td>
                                <td class="align-middle">
                                    <span class="badge badge-lg badge-primary">
                                        {{ @$address->user->first_name ?: __('N/A') }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <a class="editable"
                                       style="cursor:pointer;"
                                       data-name="name"
                                       data-type="text"
                                       data-emptytext="0"
                                       data-pk="{{$address->id}}"
                                       data-url="{{route('ship.address.create.update')}}"
                                       data-value="{{ $address->name }}">
                                    </a>
                                </td>
                                <td class="align-middle">
                                    <a class="editable"
                                       style="cursor:pointer;"
                                       data-name="company_name"
                                       data-type="text"
                                       data-emptytext="0"
                                       data-pk="{{$address->id}}"
                                       data-url="{{route('ship.address.create.update')}}"
                                       data-value="{{ $address->company_name }}">
                                    </a>
                                </td>

                                <td class="align-middle">
                                    <a class="editable"
                                       style="cursor:pointer;"
                                       data-name="phone"
                                       data-type="text"
                                       data-emptytext="-"
                                       data-pk="{{$address->id}}"
                                       data-url="{{route('ship.address.create.update')}}"
                                       data-value="{{ $address->phone }}">
                                    </a>
                                </td>

{{--                                <td class="align-middle">{{ $address->phone }}</td>--}}
                                <td class="align-middle">{{ Str::limit($address->address , 50) }}</td>
                                <td class="align-middle">{{ dateFormatMy($address->created_at) }}</td>
                                <td class="align-middle">{{ diff4Human($address->updated_at) }}</td>

                                <td class="align-middle">
                                    <a href="{{ route('shipping.address.delete', $address->id) }}"
                                       class="btn btn-icon"
                                       title="@lang('Delete Address')"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       data-method="DELETE"
                                       data-confirm-title="@lang('Please Confirm')"
                                       data-confirm-text="@lang('Are you sure that you want to delete this address?')"
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

{!! $addresses->render() !!}


<!-- Create Address Modal -->
<div class="modal fade" id="createAddressModal" tabindex="-1" role="dialog" aria-labelledby="createAddressModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Shipping Address</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @include('shipping.create')
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

        $('#state_id').on('change', function () {
            var data = {
                state_id : $(this).val(),
            };

            $.ajax({
                url: '{{route('ship.address.load.cities')}}',
                data: data,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function (response) {
                    $('#city_id').html('<option value="">-- Select City --</option>');
                    $.each(response.cities, function (key, value) {
                        $("#city_id").append('<option value="' + value
                            .id + '">' + value.city + '</option>');
                    });
                }
            });

        });
    </script>
@stop
