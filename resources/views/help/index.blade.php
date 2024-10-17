@extends('layouts.app')

@section('page-title', __('Help & FAQ'))
@section('page-heading', __('Help & FAQ'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('Help & FAQ')
    </li>
@stop

@section ('styles')
    <style>
        .help-search-container {
            background: url('/assets/img/virgin-farms.png') no-repeat center center;
            background-size: cover;
            color: #333; /* Adjust text color for visibility */
            padding: 100px 0;
            text-align: center;
        }
        .search-bar {
            max-width: 700px; /* Adjust width as needed */
            margin: auto;
        }
        .search-input {
            border-radius: 20px;
            padding: 10px 20px;
            width: 100%;
        }
        .search-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25); /* Bootstrap blue glow */
        }

        .content-box {
            margin-top: 17px;
            background: #ffffff; /* Background color */
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Shadow for the box */
        }
        .rounded{
            border-radius: 35px !important;
        }

        .active-btn {
            background-color: #28a745 !important; /* Green background for the active button */
            color: white !important; /* White text color for better contrast */
        }
    </style>
@stop

@section('content')

@include('partials.messages')

<div class="card">

    @if(myRoleName() == 'Admin')
        <a target="_blank" href="{{route('help.faq.edit')}}" class="btn btn-icon"
           title="@lang('Click To Edit Help and FAQs page')" data-toggle="tooltip" data-placement="top">
            <i class="fas fa-edit text-danger fa-2x"></i>
        </a>
    @endif

    <div class="card-body">

        <div class="container-fluid help-search-container">
            <h1>Help & Frequently Asked Questions (FAQs)</h1>

            <!-- Search Bar -->
            <div class="search-bar mb-3">
                <input type="text" id="searchInput" class="form-control search-input" placeholder="Search for any help, question etc">
            </div>

            <!-- Buttons to Open Tabs -->
            <div class="row" style="margin: 0px">
                <div class="btn-group btn-group-toggle d-flex flex-wrap" role="group">
                    @foreach($tutorials as $index => $tutorial)
                        <button type="button" class="btn btn-danger m-1 p-3 rounded btn-md @if($index === 0) active-btn @endif" data-tab-index="{{ $index }}">
                            {{ $tutorial['title'] }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>



        <div class="container mt-5">
            <!-- Tab Content -->
            <div class="tab-content mt-3" id="myTabContent">
                @foreach($tutorials as $index => $tutorial)
                    <div class="tab-pane fade @if($index === 0) show active @endif" id="content-{{ $index }}" role="tabpanel">
                        <h3>{{ $tutorial['title'] }}</h3>
                        {!! $tutorial['content'] !!}
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@stop

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @include('partials.toaster-js')
    <script>
        $(document).ready(function() {
            // Handle button click to show corresponding tab content
            $('.btn[data-tab-index]').on('click', function() {
                const tabIndex = $(this).data('tab-index');
                // Remove active state from all buttons
                $('.btn[data-tab-index]').removeClass('active-btn');

                // Deactivate all tabs
                $('.tab-pane').removeClass('show active');

                // Activate the selected tab
                $(`#content-${tabIndex}`).addClass('show active');

                // Add active state to the clicked button
                $(this).addClass('active-btn');
            });

            // Search functionality with button activation
            $('#searchInput').on('input', function() {
                const searchTerm = $(this).val().toLowerCase().trim();
                let found = false;

                // Remove previous highlights and active states
                $('.btn[data-tab-index]').removeClass('active-btn');
                $('.tab-pane').removeClass('show active');

                if (searchTerm) {
                    $('.tab-pane').each(function(index) {
                        const contentText = $(this).text().toLowerCase();

                        if (contentText.includes(searchTerm)) {
                            // Activate the corresponding tab and button if found
                            $(`#content-${index}`).addClass('show active');
                            $(`.btn[data-tab-index="${index}"]`).addClass('active-btn');

                            found = true;
                            return false; // Exit loop after first match
                        }
                    });
                } else {
                    // If search is cleared, reset the first tab and button to active
                    $('.btn[data-tab-index="0"]').addClass('active-btn');
                    $('#content-0').addClass('show active');
                }
            });
        });
    </script>
@stop
