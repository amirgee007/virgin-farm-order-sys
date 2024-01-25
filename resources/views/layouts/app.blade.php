<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('page-title') - {{ setting('app_name') }}</title>

    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{ url('assets/img/icons/apple-touch-icon-144x144.png') }}" />
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="{{ url('assets/img/icons/apple-touch-icon-152x152.png') }}" />
    <link rel="icon" type="image/png" href="{{ url('assets/img/icons/favicon-32x32.png') }}" sizes="32x32" />
    <link rel="icon" type="image/png" href="{{ url('assets/img/icons/favicon-16x16.png') }}" sizes="16x16" />
    <meta name="application-name" content="{{ setting('app_name') }}"/>
    <meta name="msapplication-TileColor" content="#FFFFFF" />
    <meta name="msapplication-TileImage" content="{{ url('assets/img/icons/mstile-144x144.png') }}" />

    <link media="all" type="text/css" rel="stylesheet" href="{{ url(mix('assets/css/vendor.css')) }}">
    <link media="all" type="text/css" rel="stylesheet" href="{{ url(mix('assets/css/app.css')) }}">

    <style>

        .main-section{
            background-color: #F8F8F8;
        }
        .btnCart{
            border:0px;
            margin:10px 0px;
            box-shadow:none !important;
        }
        .dropdown .dropdown-menu{
            padding:20px;
            top:30px !important;
            width:350px !important;
            left:-110px !important;
            box-shadow:0px 5px 30px black;
        }
        .total-header-section{
            border-bottom:1px solid #d2d2d2;
        }
        .total-section p{
            margin-bottom:20px;
        }
        .cart-detail{
            padding:15px 0px;
        }
        .cart-detail-img img{
            width:100%;
            height:100%;
            padding-left:15px;
        }
        .cart-detail-product p{
            margin:0px;
            color:#000;
            font-weight:500;
        }
        .cart-detail .price{
            font-size:12px;
            margin-right:10px;
            font-weight:500;
        }
        .cart-detail .count{
            color:#C2C2DC;
        }
        .checkout{
            border-top:1px solid #d2d2d2;
            padding-top: 10px;
        }
        .checkout .btn-primary{
            border-radius:35px;
            height:42px;
        }
        .checkout .btn-danger{
            border-radius:35px;
            height:42px;
        }
        .dropdown-menu:before{
            content: " ";
            position:absolute;
            top:-20px;
            right:50px;
            border:10px solid transparent;
            border-bottom-color:#fff;
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

    <script>
        (function($){
            $(window).on("load",function(){
                $(".custom-scroll-bar").mCustomScrollbar({
                    theme:"minimal"
                });
            });
        })(jQuery);
    </script>

    @stack('js')
    @yield('scripts')

    @hook('app:scripts')
</body>
</html>
