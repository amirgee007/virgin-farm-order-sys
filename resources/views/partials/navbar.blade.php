<nav class="navbar fixed-top align-items-start navbar-expand-lg pl-0 pr-0 py-0" >

    <div class="navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand mr-0" href="{{ url('/') }}">
            <img src="{{ url('assets/img/vanguard-logo.png') }}" class="logo-lg" height="60" alt="{{ setting('app_name') }}">
            <img src="{{ url('assets/img/vanguard-logo-no-text.png') }}" class="logo-sm" height="35" alt="{{ setting('app_name') }}">
        </a>
    </div>

    <div>
        @if (app('impersonate')->isImpersonating())
            <a href="{{ route('impersonate.leave') }}" class="navbar-toggler text-danger hidden-md">
                <i class="fas fa-user-secret"></i>
            </a>
        @endif

        <button class="navbar-toggler" type="button" id="sidebar-toggle">
            <i class="fas fa-align-right text-muted"></i>
        </button>

        <button class="navbar-toggler mr-3"
                type="button"
                data-toggle="collapse"
                data-target="#top-navigation"
                aria-controls="top-navigation"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <i class="fas fa-bars text-muted"></i>
        </button>
    </div>

    <div class="collapse navbar-collapse" id="top-navigation">
        <div class="row ml-2">
            <div class="col-lg-12 d-flex align-items-left align-items-md-center flex-column flex-md-row py-3">
                <h4 class="page-header mb-0">
                    @yield('page-heading')
                </h4>

                <ol class="breadcrumb mb-0 font-weight-light">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/') }}" class="text-muted">
                            <i class="fa fa-home"></i>
                        </a>
                    </li>

                    @yield('breadcrumbs')
                </ol>
            </div>
        </div>

        <ul class="navbar-nav ml-auto pr-3 flex-row">

            <div class="dropdown" style="padding-right: 40px;">
                <button type="button" class="btn btn-light btnCart" data-toggle="dropdown">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i> &nbsp;Cart
                    <span class="badge badge-pill badge-danger">{{ count((array) session('cart')) }}</span>
                </button>

                <div class="dropdown-menu">
                    <div class="row total-header-section">
                        <div class="col-lg-3 col-sm-6 col-6">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                            <span class="badge badge-pill badge-danger">{{ count((array) session('cart')) }}</span>
                        </div>
                        @php $total = 0;  $totalQty = 0; @endphp
                        @foreach((array) session('cart') as $id => $details)
                            @php
                                $total += $details['price'] * $details['quantity'];
                                $totalQty =+ $totalQty + $details['quantity'];
                            @endphp

                        @endforeach

                        <div class="col-lg-9 col-sm-6 col-6 total-section text-right">
                            <p>Total Units:<b class="text-info"> {{$totalQty}}</b> &nbsp; Total: <span class="text-info"><b>${{$total}}</b></span></p>
                        </div>
                    </div>
                    @if(session('cart'))
                        @foreach(session('cart') as $id => $details)
                            <div class="row cart-detail">
                                <div class="col-lg-4 col-sm-4 col-4 cart-detail-img">
                                    <img src="{{ $details['image'] }}" />
                                </div>
                                <div class="col-lg-8 col-sm-8 col-8 cart-detail-product">
                                    <p>{{ $details['name'] }}</p>
                                    <span class="price text-info"> ${{ $details['price'] }}</span> <span class="count"> Quantity:{{ $details['quantity'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <div class="row">
                        <div class="col checkout">
                            <a href="{{ route('cart') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-shopping-cart"></i> &nbsp; View all
                            </a>
                        </div>

                        <div class="col checkout">

                            <a href="{{ route('remove.from.cart') }}"
                               class="btn btn-danger btn-blockn"
                               title="@lang('Delete all items from cart?')"
                               data-toggle="tooltip"
                               data-placement="top"
                               data-method="DELETE"
                               data-confirm-title="@lang('Please Confirm')"
                               data-confirm-text="@lang('Are you sure that you want to remove all items from cart?')"
                               data-confirm-delete="@lang('Yes, delete all!')">
                                <i class="fas fa-shopping-cart"></i> &nbsp; Empty Cart
                            </a>
                        </div>
                    </div>
                </div>

                Order Total: <span class="text-danger"><b>${{$total}}</b></span>
            </div>


            @hook('navbar:items')

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle"
                   href="#"
                   id="navbarDropdown"
                   role="button"
                   data-toggle="dropdown"
                   aria-haspopup="true"
                   aria-expanded="false">
                    <img src="{{ auth()->user()->present()->avatar }}"
                         width="50"
                         height="50"
                         class="rounded-circle img-thumbnail img-responsive">
                </a>
                <div class="dropdown-menu dropdown-menu-right position-absolute p-0" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item py-2" href="{{ route('profile') }}">
                        <i class="fas fa-user text-muted mr-2"></i>
                        @lang('My Profile')
                    </a>

                    @if (config('session.driver') == 'database')
                        <a href="{{ route('profile.sessions') }}" class="dropdown-item py-2">
                            <i class="fas fa-list text-muted mr-2"></i>
                            @lang('Active Sessions')
                        </a>
                    @endif

                    @hook('navbar:dropdown')

                    <div class="dropdown-divider m-0"></div>

                    <a class="dropdown-item py-2" href="{{ route('auth.logout') }}">
                        <i class="fas fa-sign-out-alt text-muted mr-2"></i>
                        @lang('Logout')
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
