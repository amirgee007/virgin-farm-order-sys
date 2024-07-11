@extends('layouts.app')

@section('page-title', __('Clients & Admins'))
@section('page-heading', __('Clients & Admins'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('Clients & Admins')
    </li>

@stop

@section ('styles')
    <style>
        .loader {
            height: 60px !important;
        }
        .users-list-table th, .users-list-table td {
            padding: 0.25rem !important;
        }
        .users-list-table {
            font-weight: 400 !important;
            font-size: 13px !important;
            line-height: 1.228571429 !important;
        }
        button {
            font-size: 11px !important;
            font-weight: 400 !important;
        }
        caption {
            caption-side:top;
        }
        tr:hover > td {
            cursor: pointer !important;
        }
    </style>
@stop

@section('content')

@include('partials.messages')

<div class="card">
    <div class="card-body">

        <form action="" method="GET" id="users-form" class="pb-2 mb-3 border-bottom-light">
            <div class="row my-3 flex-md-row flex-column-reverse">
                <div class="col-md-4 mt-md-0 mt-2">
                    <div class="input-group custom-search-form">
                        <input type="text"
                               class="form-control input-solid"
                               name="search"
                               value="{{ Request::get('search') }}"
                               placeholder="@lang('Search for clients and admins...')">

                            <span class="input-group-append">
                                @if (Request::has('search') && Request::get('search') != '')
                                    <a href="{{ route('users.index') }}"
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
                            'status',
                            $statuses,
                            Request::get('status'),
                            ['id' => 'status', 'class' => 'form-control input-solid']
                        )
                    !!}
                </div>

                <div class="col-md-6">
                    <a href="{{ route('users.create') }}" class="btn btn-primary btn-rounded float-right btn-sm">
                        <i class="fas fa-plus mr-2"></i>
                        @lang('Add Client OR Admin')
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive" id="users-table-wrapper">
            <table class="table table-borderless table-striped users-list-table">
                <thead>
                <tr>
                    <th></th>
                    <th class="min-width-80">@lang('Username')</th>
                    <th class="min-width-80"
                        title="Once admin confirm the account is ok to use then click here to approve client to be login and use."
                        data-toggle="tooltip" data-placement="top"
                    >@lang('Approved')</th>
                    <th class="min-width-150">@lang('Full Name')</th>
                    <th class="min-width-100">@lang('Email')</th>
                    <th class="min-width-100">@lang('Address')</th>
                    <th class="min-width-80">@lang('Signup Date')</th>
                    <th class="min-width-80">@lang('Company')</th>
                    <th class="min-width-80">@lang('Role')</th>
                    <th class="min-width-80">@lang('Email Status')</th>
                    <th class="text-center min-width-150">@lang('Action')</th>
                </tr>
                </thead>
                <tbody>
                    @if (count($users))
                        @foreach ($users as $user)
                            @include('user.partials.row')
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7"><em>@lang('No records found.')</em></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{!! $users->render() !!}

@stop

@section('scripts')
    @include('partials.toaster-js')
    <script>
        $(document).ready(function() {

            $("#status").change(function () {
                $("#users-form").submit();
            });

            $('.approved-toggle').change(function() {
                var userId = $(this).data('id');
                var isApproved = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: '{{ route('users.approve', '') }}/' + userId,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        is_approved: isApproved
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success("User approval status updated successfully.", "Message");
                        } else {
                            toastr.error("An error occurred. Please try again.", "Error");
                        }
                    }
                });
            });
        });
    </script>
@stop
