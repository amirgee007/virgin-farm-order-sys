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

        <h6>Enter A Client Size</h6>
        <input type="number"  class="input" id="numberInput" placeholder="Enter a box size to check">
        <button class="btn btn-primary btn-sm" onclick="sendNumber()">Check Results</button>

        <div id="response">
            <!-- The response will be displayed here -->
        </div>
    </div>
</div>


@stop

@section('scripts')

    <script>
        function sendNumber() {
            const number = document.getElementById('numberInput').value;

            $.ajax({
                url: '{{ route('test-amir') }}',
                type: 'POST',
                dataType: 'json',
                data: JSON.stringify({ number: number }),
                contentType: 'application/json; charset=utf-8',
                success: function(response) {
                    document.getElementById('response').innerHTML = `
                        <p>Size: <b>${response.size}</b></p>
                        <p>Boxes: <b>${response.boxes}</b></p>
                        <p>Next Size: <b>${response.next_size}</b></p>
                        <p>Percentage: <b>${response.percentage}%</b></p>
                    `;
                }
            });
        }
    </script>
@stop
