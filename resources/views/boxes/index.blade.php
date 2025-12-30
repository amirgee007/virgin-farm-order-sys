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
    <link media="all" type="text/css" rel="stylesheet" href="{{ url('assets/plugins/daterangepicker/daterangepicker.css') }}">

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
                               placeholder="@lang('Search for box name, size')">

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

                <div class="col-md-4">

                    <a href="#" class="btn btn-danger btn-rounded float-right btn-sm ml-2" data-toggle="modal" data-target="#changeExtraFees">
                        <i class="fas fa-plus mr-2"></i>
                        @lang('Update Extra Fee Date')
                    </a>

                    <a href="#" class="btn btn-primary btn-rounded float-right btn-sm" data-toggle="modal" data-target="#createBoxModal">
                        <i class="fas fa-plus mr-2"></i>
                        @lang('Add New Box')
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive" id="users-table-wrapper">
            <small class="text-danger"><b>For any changes here, inform Amir — this table isn’t part of the core logic yet.</b></small>
            <table class="table table-borderless table-striped">
                <thead>
                <tr>
                    <th class="min-width-80">@lang('ID')</th>
                    <th class="min-width-150">@lang('Description')</th>
                    <th class="min-width-80">@lang('Length')</th>
                    <th class="min-width-100">@lang('Width')</th>
                    <th class="min-width-80">@lang('Height')</th>
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
                <h5 class="card-title">Unit Of Measures (Detail + Total Bunches)
                    <a href="#" class="btn btn-primary btn-rounded float-right btn-sm" data-toggle="modal" data-target="#createUOMModal">
                        <i class="fas fa-plus mr-2"></i>
                        @lang('Add New UOM')
                    </a>
                </h5>
                <div class="table-responsive" id="users-table-wrapper">
                    <table class="table table-borderless table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Unit</th>
                                <th>Detail</th>
                                <th>Total</th>
                                <th class="float-right">Last Update</th>
                            </tr>
                        </thead>
                        @foreach($unitOfMeasure as $measure)
                            <tr>
                                <td>
                                    <a class="editable"
                                       style="cursor:pointer;"
                                       data-name="unit"
                                       data-type="text"
                                       data-emptytext="empty"
                                       data-pk="{{$measure->id}}"
                                       data-url="{{route('unit_of_measures.update')}}"
                                       data-value=" {{$measure->unit}}">
                                    </a>
                                </td>
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

                                <td class="align-middle">
                                    <a class="editable"
                                       style="cursor:pointer;"
                                       data-name="total"
                                       data-type="number"
                                       data-emptytext="0"
                                       data-pk="{{$measure->id}}"
                                       data-url="{{route('unit_of_measures.update')}}"
                                       data-value=" {{$measure->total}}">
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

<!-- Modal -->
<div class="modal fade" id="createUOMModal" tabindex="-1" aria-labelledby="createUOMModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Create/Update Unit of Measure</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createForm">
                    @csrf
                    <input type="hidden" name="is_adding_new" value="1">
                    <div class="form-group">
                        <label for="unit">Unit</label>
                        <input type="text" class="form-control" id="unit" name="unit" placeholder="Unit i.e U123" required>
                    </div>

                    <div class="form-group">
                        <label for="detail">Detail</label>
                        <input type="text" class="form-control" id="detail" name="detail" placeholder="Pack 3 Stems" required>
                    </div>

                    <div class="form-group">
                        <label for="total">Total</label>
                        <input type="number" class="form-control" id="total" name="total" placeholder="10,20 etc" required>
                    </div>
                    <button type="submit" class="btn btn-primary float-right ">Create UOM</button>
{{--                    <button type="button" class="btn btn-secondary float-right pr-2" data-dismiss="modal">Close</button>--}}
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changeExtraFees" tabindex="-1" role="dialog" aria-labelledby="createBoxModal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Extra Fee Dates</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('update.extra.fees.date')}}" method="POST" enctype="multipart/form-data">
                    {{csrf_field()}}

                    <div class="form-group">
                        <input type="hidden" name="range" value="" class="dateRangeVal">
                        <label for="dateRange" class="form-label mt-3">Date Range</label>
                        <div id="dateRange" class="form-control float-right dateRanges" style="cursor: pointer; ">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span>
                            &nbsp;<i class="fa fa-caret-down"></i>
                        </div>
                    </div>

                    <br/>
                    <br/>
                    <div class="form-group">
                        <label for="extraFees">Extra Fees %</label>
                        <input type="number" name="fees" min="0" value="{{$found ? $found->value : ''}}"  max="100" class="form-control" id="extraFees" placeholder="1-100">
                        <small class="text-danger">If want to reset just put 0 fees here.</small>
                    </div>
                    <br>
                    <input type="submit" value="Update Dates Fees" class="btn btn-primary btn-sm float-right">
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('scripts')

    @include('partials.toaster-js')
    <script src="{{ url('assets/plugins/daterangepicker/daterangepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/x-editable/bootstrap-editable.min.js') }}" ></script>

    <script>
        $(document).ready(function() {
            $('#createForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route("unit_of_measures.update") }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#createModal').modal('hide');
                        toastr.success('New UOM added succesfully.');
                        location.reload();
                    },
                    error: function(response) {
                        toastr.error('Something went wrong plz check with admin.');
                        console.log(response);
                    }
                });
            });
        });
    </script>

    <script>

        var start = moment('{{$selected['start']}}');
        var end = moment('{{$selected['end']}}');

        function cb(start, end) {
            $('#dateRange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('.dateRangeVal').val($("#dateRange span").html());
        }

        $('.dateRanges').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Next 6 Days': [moment(), moment().add(6, 'days')],
                'Next 7 Days': [moment(),moment().add(7, 'days')],
                'Next 15 Days': [moment(), moment().add(15, 'days'), moment()],
                'Next 30 Days': [moment(), moment().add(30, 'days'), moment()]
            }
        }, cb);

        cb(start, end);

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
