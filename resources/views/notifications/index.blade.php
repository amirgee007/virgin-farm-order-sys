@extends('layouts.app')

@section('page-title', __('Notifications'))
@section('page-heading', __('Notifications'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('Notifications')
    </li>
@stop

@section('styles')
    <link media="all" type="text/css" rel="stylesheet"
          href="{{ url('assets/plugins/data-tables/dataTables.bootstrap4.min.css') }}">
@stop

@section('content')

@include('partials.messages')

<div class="card">
    <div class="card-body">

        <div class="table-responsive" id="users-table-wrapper">
            <table class="table table-borderless table-striped">
                <thead>
                <tr>
                    <th>@lang('ID')</th>
                    <th>@lang('Client')</th>
                    <th>@lang('Order Id')</th>
                    <th class="min-width-100">@lang('Message')</th>
                    <th>@lang('Created')</th>
                    <th>@lang('Action')</th>
                </tr>
                </thead>
                <tbody>
                    @if (count($notifications))
                        @foreach ($notifications as $index => $notification)
                            <tr>
                                <td class="align-middle">{{ ++$index }}</td>
                                <td class="align-middle">
                                    <span class="badge badge-lg badge-warning">
                                        {{ $notification->user->first_name ?: __('N/A') }}
                                    </span>
                                </td>

                                <td class="align-middle">
                                    <a target="_blank" href="{{route('orders.index')."?search=WO".$notification->id}}"
                                       title="@lang('View order detail')"
                                       data-toggle="tooltip"
                                       data-placement="top">
                                        <span class="badge badge-lg badge-primary">
                                            WO-{{ $notification->order_id }}
                                        </span>
                                    </a>
                                </td>

                                <td class="align-middle">{{ Str::limit($notification->message , 50) }}</td>
                                <td class="align-middle" title="{{dateFormatMy($notification->created_at)}}">{{ diff4Human($notification->updated_at) }}</td>

                                <td class="align-middle">
                                    <a href="{{ route('notification.delete', $notification->id) }}"
                                       class="btn btn-icon"
                                       title="@lang('Delete Notification')"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       data-method="DELETE"
                                       data-confirm-title="@lang('Please Confirm')"
                                       data-confirm-text="@lang('Are you sure that you want to delete this notification?')"
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

{{--{!! $notifications->render() !!}--}}

@stop

@section('scripts')
    @include('partials.toaster-js')

    <script src="{{ url('assets/plugins/data-tables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ url('assets/plugins/data-tables/jquery.dataTables.min.js') }}"></script>

    <script>

        $(document).ready(function () {

            $('.table-striped').DataTable({
                "pageLength": 100,
                "order": []
            });
        });

    </script>

@stop
