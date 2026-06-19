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

        <form method="POST" action="{{ route('notification.markAllRead') }}" class="d-inline float-right">
            @csrf
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="fas fa-check-double"></i> Mark all as Read
            </button>
        </form>

        <form method="GET" class="form-inline mb-3">
            <label class="mr-2">Type:</label>
            <select name="type" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="order" {{ ($type ?? '') == 'order' ? 'selected' : '' }}>Order</option>
                <option value="wishlist" {{ ($type ?? '') == 'wishlist' ? 'selected' : '' }}>Wish List</option>
            </select>
        </form>

        <div class="table-responsive" id="users-table-wrapper">
            <table class="table table-borderless table-striped">
                <thead>
                <tr>
                    <th>@lang('ID')</th>
                    <th>@lang('Client')</th>
                    <th>@lang('Type')</th>
                    <th>@lang('Order Id')</th>
                    <th class="min-width-100">@lang('Message')</th>
                    <th>@lang('Created')</th>
                    <th>Read At</th>
                    <th>@lang('Action')</th>
                </tr>
                </thead>
                <tbody>
                    @if (count($notifications))
                        @foreach ($notifications as $index => $notification)
                            @php
                                $isWish = $notification->type === 'wishlist';
                                $clientUser = $notification->user
                                    ?: ($isWish ? optional($notification->wishList)->user : optional($notification->order)->user);
                                $clientName = optional($clientUser)->first_name ?: 'N/A';
                            @endphp
                            <tr>
                                <td class="align-middle">{{ ++$index }}</td>
                                <td class="align-middle">
                                    <span class="badge badge-lg badge-warning">{{ $clientName }}</span>
                                </td>

                                <td class="align-middle">
                                    <span class="badge badge-info">{{ ucfirst($notification->type ?: 'order') }}</span>
                                </td>

                                <td class="align-middle">
                                    @if($isWish && $notification->wish_list_id)
                                        <a target="_blank" href="{{ route('wishlist.show', $notification->wish_list_id) }}">
                                            <span class="badge badge-lg badge-success">WL-{{ $notification->wish_list_id }}</span>
                                        </a>
                                    @elseif($notification->order_id)
                                        <a target="_blank" href="{{route('orders.index')."?search=WO".$notification->order_id}}"
                                           title="View order detail"
                                           data-toggle="tooltip" data-placement="top">
                                            <span class="badge badge-lg badge-primary">WO-{{ $notification->order_id }}</span>
                                        </a>
                                    @endif
                                </td>

                                <td class="align-middle">{{ $notification->message }}</td>
                                <td class="align-middle" title="{{dateFormatMy($notification->created_at)}}">{{ diff4Human($notification->updated_at) }}</td>

                                <td class="align-middle" title="{{ $notification->read_at ? dateFormatMy($notification->read_at) : '' }}">
                                    @if($notification->read_at)
                                        {{ diff4Human($notification->read_at) }}
                                    @else
                                        <span class="badge badge-secondary">Unread</span>
                                    @endif
                                </td>

                                <td class="align-middle">
                                    @if(!$notification->read_at)
                                        <form method="POST" action="{{ route('notification.markRead', $notification->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-icon" title="Mark as Read" data-toggle="tooltip" data-placement="top">
                                                <i class="fas fa-check text-success"></i>
                                            </button>
                                        </form>
                                    @endif

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
