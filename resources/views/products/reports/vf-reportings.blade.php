@extends('layouts.app')

@section('page-title', __('VF Reportings'))
@section('page-heading', __('VF Reportings'))

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">
        @lang('Sold Items Reporting')
    </li>
@stop

@section('styles')
    <style>
        .vf-reportings-table th,
        .vf-reportings-table td {
            padding: 0.35rem !important;
            vertical-align: middle !important;
            font-size: 12px !important;
        }

        .vf-reportings-table {
            line-height: 1.35 !important;
        }

        .reporting-filter .form-control,
        .reporting-filter .btn {
            height: 36px;
        }

        .min-width-120 {
            min-width: 120px;
        }

        .min-width-180 {
            min-width: 180px;
        }
    </style>
@endsection

@section('content')
    @include('partials.messages')

    @php
        $queryForExport = request()->except('export', 'page');
        $pdfUrl = route('vf-reportings.index', array_merge($queryForExport, ['export' => 'pdf']));
        $excelUrl = route('vf-reportings.index', array_merge($queryForExport, ['export' => 'excel']));
    @endphp

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body mt-0 p-3">
                    <form action="{{ route('vf-reportings.index') }}" method="GET" class="reporting-filter pb-3 mb-3 border-bottom-light">
                        <div class="row">
                            <div class="col-md-3 mt-2">
                                <div class="input-group custom-search-form">
                                    <input type="text"
                                           class="form-control input-solid"
                                           name="search"
                                           value="{{ $filters['search'] }}"
                                           placeholder="Search item, product, category">

                                    <span class="input-group-append">
                                        @if ($filters['search'])
                                            <a href="{{ route('vf-reportings.index') }}"
                                               class="btn btn-light d-flex align-items-center text-muted"
                                               role="button">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        @endif
                                        <button class="btn btn-light" type="submit">
                                            <i class="fas fa-search text-muted"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-2 mt-2">
                                <select name="sales_rep" class="form-control input-solid">
                                    <option value="">All Sales Reps</option>
                                    @foreach($salesReps as $key => $name)
                                        <option value="{{ $key }}" {{ (string) $filters['salesRep'] === (string) $key ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mt-2">
                                <select name="period" id="report-period" class="form-control input-solid">
                                    @foreach($periods as $key => $name)
                                        <option value="{{ $key }}" {{ $filters['period'] === $key ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mt-2">
                                <select name="sort" class="form-control input-solid">
                                    @foreach($sortOptions as $key => $name)
                                        <option value="{{ $key }}" {{ $filters['sort'] === $key ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mt-2">
                                <div class="btn-group w-100">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <a href="{{ $pdfUrl }}" class="btn btn-danger">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                    <a href="{{ $excelUrl }}" class="btn btn-success">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2" id="custom-date-row">
                            <div class="col-md-2">
                                <label class="small mb-1" for="date_in">Custom Date From</label>
                                <input type="date"
                                       class="form-control input-solid"
                                       id="date_in"
                                       name="date_in"
                                       value="{{ $filters['dateIn'] }}">
                            </div>

                            <div class="col-md-2">
                                <label class="small mb-1" for="date_out">Custom Date To</label>
                                <input type="date"
                                       class="form-control input-solid"
                                       id="date_out"
                                       name="date_out"
                                       value="{{ $filters['dateOut'] }}">
                            </div>

                            <div class="col-md-8 d-flex align-items-end">
                                <small class="text-muted">
                                    Showing orders shipped from
                                    <b>{{ dateFormatMy($filters['dateIn']) }}</b>
                                    to
                                    <b>{{ dateFormatMy($filters['dateOut']) }}</b> ({{ucfirst($filters['period'])}})
                                </small>
                            </div>
                        </div>
                    </form>

                    <div class="notes-success">
                        <p>
                            Total Items: <b>{{ $reportItems->count() }}</b> |
                            Total Sales: <b>${{ number_format($reportItems->sum('total_sales'), 2) }}</b>
                        </p>
                    </div>

                    <div class="table-responsive mt-2">
                        @include('products.reports._vf-reportings-table', [
                            'reportItems' => $reportItems,
                            'filters' => $filters,
                            'suppliers' => $suppliers,
                            'isExport' => false,
                        ])
                    </div>

                    {!! $reportItems->render() !!}
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#report-period').change(function () {
                if ($(this).val() !== 'custom') {
                    $('#date_in, #date_out').prop('readonly', true);
                } else {
                    $('#date_in, #date_out').prop('readonly', false);
                }
            }).trigger('change');
        });
    </script>
@endsection
