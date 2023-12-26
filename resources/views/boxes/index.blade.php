@extends('layouts.app')

@section('page-title', __('Boxes'))
@section('page-heading', __('Boxes'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('Boxes') & UOM
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
                <div class="col-md-10 mt-md-0 mt-2">
                    <div class="input-group custom-search-form">
                        <input type="text"
                               class="form-control input-solid"
                               name="search"
                               value="{{ Request::get('search') }}"
                               placeholder="@lang('Search for box name, size, volume')">

                            <span class="input-group-append">
                                @if (Request::has('search') && Request::get('search') != '')
                                <a href="{{ route('boxes.index') }}"
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


                <div class="col-md-2">

{{--                    <a class="float-right mb-2" href="#">--}}
{{--                        <i title="Import BOX by EXCEL sheet"--}}
{{--                           data-toggle="tooltip" data-placement="top" class="fas fa-upload fa-2x"></i>--}}
{{--                    </a>--}}

                    <a href="#" class="btn btn-primary btn-rounded float-right btn-sm" data-toggle="modal" data-target="#createBoxModal">
                        <i class="fas fa-plus mr-2"></i>
                        @lang('Add New Box')
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive" id="users-table-wrapper">
            <table class="table table-borderless table-striped">
                <thead>
                <tr>
                    <th class="min-width-80">@lang('ID')</th>
                    <th class="min-width-150">@lang('Description')</th>
                    <th class="min-width-80">@lang('Length')</th>
                    <th class="min-width-100">@lang('Width')</th>
                    <th class="min-width-80">@lang('Height')</th>
                    <th class="min-width-80">@lang('Volume')</th>
                    <th class="min-width-80">@lang('Weight')</th>
                    <th class="min-width-80">@lang('Min Value(Cube)')</th>
                    <th class="min-width-80">@lang('Max Value(Cube)')</th>
                    <th class="min-width-80">@lang('Created')</th>
                    <th class="min-width-80">@lang('Updated')</th>
                    <th class="min-width-80">@lang('Actions')</th>
                </tr>
                </thead>
                <tbody>
                    @if (count($boxes))
                        @foreach ($boxes as $index => $box)
                            <tr>

                                <td class="align-middle">{{ ++$index }}</td>
                                {{--<td class="align-middle">{{ Str::limit($address->address , 50) }}</td>--}}

                                <td class="align-middle">
                                    <a class="editable"
                                       style="cursor:pointer;"
                                       data-name="description"
                                       data-type="text"
                                       data-emptytext="0"
                                       data-pk="{{$box->id}}"
                                       data-url="{{route('box.create.update')}}"
                                       data-value="{{ $box->description }}">
                                    </a>
                                </td>

                                <td class="align-middle">
                                    <a class="editable"
                                       style="cursor:pointer;"
                                       data-name="length"
                                       data-type="text"
                                       data-emptytext="0"
                                       data-pk="{{$box->id}}"
                                       data-url="{{route('box.create.update')}}"
                                       data-value="{{ $box->length }}">
                                    </a>
                                </td>

                                <td class="align-middle">
                                    <a class="editable"
                                       style="cursor:pointer;"
                                       data-name="width"
                                       data-type="text"
                                       data-emptytext="0"
                                       data-pk="{{$box->id}}"
                                       data-url="{{route('box.create.update')}}"
                                       data-value="{{ $box->width }}">
                                    </a>
                                </td>

                                <td class="align-middle">
                                    <a class="editable"
                                       style="cursor:pointer;"
                                       data-name="height"
                                       data-type="text"
                                       data-emptytext="0"
                                       data-pk="{{$box->id}}"
                                       data-url="{{route('box.create.update')}}"
                                       data-value="{{ $box->height }}">
                                    </a>
                                </td>

                                <td class="align-middle">
                                    <a class="editable"
                                       style="cursor:pointer;"
                                       data-name="volume"
                                       data-type="text"
                                       data-emptytext="0"
                                       data-pk="{{$box->id}}"
                                       data-url="{{route('box.create.update')}}"
                                       data-value="{{ $box->volume }}">
                                    </a>
                                </td>

                                <td class="align-middle">
                                    <a class="editable"
                                       style="cursor:pointer;"
                                       data-name="weight"
                                       data-type="text"
                                       data-emptytext="0"
                                       data-pk="{{$box->id}}"
                                       data-url="{{route('box.create.update')}}"
                                       data-value="{{ $box->weight }}">
                                    </a>
                                </td>

                                <td class="align-middle">
                                    <a class="editable"
                                       style="cursor:pointer;"
                                       data-name="min_value"
                                       data-type="number"
                                       data-emptytext="0"
                                       data-pk="{{$box->id}}"
                                       data-url="{{route('box.create.update')}}"
                                       data-value="{{ $box->min_value }}">
                                    </a>
                                </td>
                                <td class="align-middle">
                                    <a class="editable"
                                       style="cursor:pointer;"
                                       data-name="max_value"
                                       data-type="number"
                                       data-emptytext="0"
                                       data-pk="{{$box->id}}"
                                       data-url="{{route('box.create.update')}}"
                                       data-value="{{ $box->max_value }}">
                                    </a>
                                </td>


                                <td class="align-middle">{{ dateFormatMy($box->created_at) }}</td>
                                <td class="align-middle">{{ diff4Human($box->updated_at) }}</td>

                                <td class="align-middle">
                                    <a href="{{ route('boxes.delete', $box->id) }}"
                                       class="btn btn-icon"
                                       title="@lang('Delete Box')"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       data-method="DELETE"
                                       data-confirm-title="@lang('Please Confirm')"
                                       data-confirm-text="@lang('Are you sure that you want to delete this box?')"
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

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Unit Of Measures</h5>
                <div class="table-responsive" id="users-table-wrapper">
                    <table class="table table-borderless table-striped table-sm">
                        <thead>
                        @foreach($unitOfMeasure as $measure)
                            <tr>
                                <th><b>{{$measure->unit}}</b></th>
                                <td>
                                <td class="align-middle">
                                    <a class="editable"
                                       style="cursor:pointer;"
                                       data-name="detail"
                                       data-type="text"
                                       data-emptytext="empty"
                                       data-pk="{{$measure->id}}"
                                       data-url="{{route('unit_of_measures.update')}}"
                                       data-value=" {{$measure->detail}}">
                                    </a>
                                </td>

                                <td class="float-right">
                                    {{diff4Human($measure->updated_at)}}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{!! $boxes->render() !!}


<!-- Create Address Modal -->
<div class="modal fade" id="createBoxModal" tabindex="-1" role="dialog" aria-labelledby="createBoxModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create New Box</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @include('boxes.create')
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

        $("#user_id").change(function () {
            $("#users-form").submit();
        });
    </script>
@stop
