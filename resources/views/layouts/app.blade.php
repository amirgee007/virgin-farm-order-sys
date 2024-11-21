<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('page-title') - {{ setting('app_name') }}</title>

    <link rel="apple-touch-icon-precomposed" sizes="144x144"
          href="{{ url('assets/img/icons/apple-touch-icon-144x144.png') }}"/>
    <link rel="apple-touch-icon-precomposed" sizes="152x152"
          href="{{ url('assets/img/icons/apple-touch-icon-152x152.png') }}"/>
    <link rel="icon" type="image/png" href="{{ url('assets/img/icons/favicon-32x32.png') }}" sizes="32x32"/>
    <link rel="icon" type="image/png" href="{{ url('assets/img/icons/favicon-16x16.png') }}" sizes="16x16"/>
    <meta name="application-name" content="{{ setting('app_name') }}"/>
    <meta name="msapplication-TileColor" content="#FFFFFF"/>
    <meta name="msapplication-TileImage" content="{{ url('assets/img/icons/mstile-144x144.png') }}"/>

    <link media="all" type="text/css" rel="stylesheet" href="{{ url(mix('assets/css/vendor.css')) }}">
    <link media="all" type="text/css" rel="stylesheet" href="{{ url(mix('assets/css/app.css')) }}">

    <style>

        .main-section {
            background-color: #F8F8F8;
        }

        .btnCart {
            border: 0px;
            margin: 10px 0px;
            box-shadow: none !important;
        }

        .dropdown .dropdown-menu {
            padding: 20px;
            top: 30px !important;
            width: 350px !important;
            left: -110px !important;
            box-shadow: 0px 5px 30px black;
        }

        .total-header-section {
            border-bottom: 1px solid #d2d2d2;
        }

        .total-section p {
            margin-bottom: 20px;
        }

        .cart-detail {
            padding: 15px 0px;
        }

        .cart-detail-img img {
            width: 100%;
            height: 100%;
            padding-left: 15px;
        }

        .cart-detail-product p {
            margin: 0px;
            color: #000;
            font-weight: 500;
        }

        .cart-detail .price {
            font-size: 12px;
            margin-right: 10px;
            font-weight: 500;
        }

        .cart-detail .count {
            color: #C2C2DC;
        }

        .checkout {
            border-top: 1px solid #d2d2d2;
            padding-top: 10px;
        }

        .checkout .btn-primary {
            border-radius: 35px;
            height: 36px;
        }

        .checkout .btn-danger {
            border-radius: 35px;
            height: 36px;
        }

        .dropdown-menu:before {
            content: " ";
            position: absolute;
            top: -20px;
            right: 50px;
            border: 10px solid transparent;
            border-bottom-color: #fff;
        }

        .popover-header {
            background-color: #e6000f;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        .popover-link {
            display: block;
            margin-top: 10px;
            color: #e6000f;
            text-align: center;
            text-decoration: none;
        }
    </style>

    @yield('styles')

    @hook('app:styles')
</head>
<body>
@include('partials.navbar')

<div class="container-fluid">
    <div class="row">
        @include('partials.sidebar.main')

        <div class="content-page">
            <main role="main" class="px-4">
                @yield('content')
            </main>
        </div>
    </div>
</div>

<script src="{{ url(mix('assets/js/vendor.js')) }}"></script>
<script src="{{ url('assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
<script src="{{ url('assets/js/as/app.js') }}"></script>

<script src="{{ url('assets/plugins/progressbar/progressbar.js') }}"></script>

{{--@env('production')--}}
{{--    <script src="https://cdn.logr-ingest.com/LogRocket.min.js" crossorigin="anonymous"></script>--}}
{{--    <script>window.LogRocket && window.LogRocket.init('bszrrw/virginfarms');</script>--}}
{{--@endenv--}}

<script>
    (function ($) {
        $(window).on("load", function () {

            var isRead = "{{!isReadFAQ()}}";
            if(isRead){
                // Show the popover initially
                $('#popoverButton').popover('show');

                // Add hover event to hide the popover
                $('#popoverButton').hover(function () {
                    $('[data-toggle="popover"]').popover('show');
                });
            }

            $('#hidePopover').click(function() {
                $('[data-toggle="popover"]').popover('hide');

                $.ajax({
                    url: '{{ route('update.faq.read.status') }}',
                    method: "get",
                    success: function (response) {}
                });
            });

            $(".custom-scroll-bar").mCustomScrollbar({
                theme: "minimal"
            });

            const size = $("#itsSizeDynamic").val();
            const percentage = $("#itsPercentageDynamic").val();

            progressBar.animate(percentage/100);

            console.log('current size before method callings is: ' + size);
            console.log('current percentage before method callings is: ' + percentage);
        });
    })(jQuery);
</script>

<script>
    const progressBar = new ProgressBar.Circle('#progress-container', {
        strokeWidth: 12,
        easing: 'easeInOut',
        duration: 1400,
        color: '#ED6A5A',
        trailColor: '#5a5a54',
        trailWidth: 5,
        svgStyle: null,
        text: {
            autoStyleContainer: false
        },
        from: {color: '#ED6A5A', width: 6},
        to: {color: '#FFEA82', width: 6},

        // Set step function to display the percentage in the middle of the circle
        step: function (state, bar) {
            bar.setText((bar.value() * 100).toFixed(0) + '%');
        }
    });

</script>

<script>
    $(document).ready(function () {

        var isPaginationClicked = false;

        // Detect pagination click
        $('.page-link').on('click', function() {
            isPaginationClicked = true;  // Set flag to true if pagination is clicked
        });

        // Capture scroll position before page refresh, only if not clicking pagination
        $(window).on("beforeunload", function() {
            if (!isPaginationClicked) {
                localStorage.setItem("scrollPosition", $(window).scrollTop());
            }
        });

        // Restore scroll position after page load
        var scrollPosition = localStorage.getItem("scrollPosition");
        if (scrollPosition && !isPaginationClicked) {
            $(window).scrollTop(scrollPosition);
            localStorage.removeItem("scrollPosition");  // Clean up after setting
        }

        var remainingSeconds = <?php echo cartTimeLeftSec(); ?>;

        function updateTimer() {
            var minutes = Math.floor(remainingSeconds / 60);
            var seconds = remainingSeconds % 60;

            // Ensure both minutes and seconds are displayed with two digits
            var displayMinutes = minutes.toString().padStart(2, '0');
            var displaySeconds = seconds.toString().padStart(2, '0');

            $('#carttimer').text('Checkout Timer: ' + displayMinutes + ':' + displaySeconds);

            if (remainingSeconds > 0) {
                remainingSeconds -= 1;
                setTimeout(updateTimer, 1000);
            } else {
                $('#carttimer').text(''); //Empty Cart. here we can add timer ETC
                $('.spinner-grow-sm').hide();
            }
        }

        updateTimer();
    });
</script>

<script>
    let isInteractionDisabled = false;
    let overlay; // Make overlay a global variable to remove it later

    function disableUserInteraction() {
        if (!isInteractionDisabled) {
            // Disable mouse clicks
            document.addEventListener('click', function preventClicks(e) {
                if (isInteractionDisabled) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }, true);

            // Disable keypresses
            document.addEventListener('keydown', function preventKeyPress(e) {
                if (isInteractionDisabled) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }, true);

            // Create and show the overlay
            overlay = document.createElement('div');
            overlay.style.position = 'fixed';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.width = '100%';
            overlay.style.height = '100%';
            overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
            overlay.style.zIndex = '10000';
            overlay.innerHTML = '<h2 style="color: white; text-align: center; margin-top: 20%;">Our web shop is being refreshed with new products. We’ll be back online shortly.</h2>';

            document.body.appendChild(overlay);
            isInteractionDisabled = true;  // Set flag to true
        }
    }

    function enableUserInteraction() {
        if (isInteractionDisabled) {
            // Remove the overlay
            if (overlay) {
                document.body.removeChild(overlay);
            }

            isInteractionDisabled = false;  // Set flag to false
        }
    }

    function checkStatus() {
        // Poll the server to check the database value
        fetch('/check-admin-uploading')
            .then(response => response.json())
            .then(data => {
                if (data.disable && !isInteractionDisabled) {
                    disableUserInteraction();  // Disable if not already disabled
                } else if (!data.disable && isInteractionDisabled) {
                    enableUserInteraction();   // Enable if it was disabled
                }
            })
            .catch(error => console.error('Error fetching status:', error));
    }

    // Check the status on page load
    window.onload = function() {
        checkStatus();
        // Keep checking the status every 5 seconds
        setInterval(checkStatus, 5000); // Every 5 seconds
    };
</script>

@stack('js')
@yield('scripts')

@hook('app:scripts')

@env('production')
    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
            var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
            s1.async=true;
            s1.src='https://embed.tawk.to/66a290a9becc2fed692b23e7/1i3lga6ak';
            s1.charset='UTF-8';
            s1.setAttribute('crossorigin','*');
            s0.parentNode.insertBefore(s1,s0);
        })();
    </script>
    <!--End of Tawk.to Script-->
@endenv

</body>
</html>
