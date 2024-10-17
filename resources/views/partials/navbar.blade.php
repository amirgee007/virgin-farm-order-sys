<nav class="navbar fixed-top align-items-start navbar-expand-lg pl-0 pr-0 py-0" >

    <div class="navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand mr-0" href="{{ url('/') }}">
            <img src="{{ url('assets/img/vanguard-logo.png') }}" class="logo-lg" height="60" alt="{{ setting('app_name') }}">
            <img src="{{ url('assets/img/vanguard-logo-no-text.png') }}" class="logo-sm" height="35" alt="{{ setting('app_name') }}">
        </a>
    </div>

    <div>
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

        <ul class="navbar-nav ml-auto pr-3 flex-row" >

            <div class="dropdown" style="padding-right: 40px; ">
                <span class="spinner-grow spinner-grow-sm text-danger" role="status" aria-hidden="true"></span>
                <b><span id="carttimer">Loading timer...!</span></b>
                @php $myCarts = getMyCart(); @endphp
                <button type="button" class="btn btn-light btnCart" data-toggle="dropdown">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i> &nbsp;Cart
                    <span class="badge badge-pill badge-danger">{{ count($myCarts) }}</span>
                </button>

                <div class="dropdown-menu" style="max-height: 600px;   overflow:auto;">
                    <div class="row total-header-section">
                        <div class="col-lg-4 col-sm-6 col-6">
                            <div id="progress-container"
                                 title="You are close to meeting the size criteria."
                                 data-toggle="tooltip"
                                 data-placement="top"
                                 style="position: relative; width: 50px; height: 50px;"></div>
                        </div>
                        @php $total = 0;  $totalQty = 0; $size = 0;@endphp
                        @foreach($myCarts as $details)

                            @php
                                $total += ($details->price * $details->quantity * $details->stems);
                                $totalQty =+ $totalQty + $details->quantity;
                                $size += $details->size * $details->quantity;
                            @endphp

                        @endforeach

                        @php
                            $boxeInfo = getCubeRangesV2($size);
                        @endphp

                        <input type="hidden" id="itsSizeDynamic" value="{{$size}}">
                        <input type="hidden" id="itsPercentageDynamic" value="{{@$boxeInfo['percentage']}}">
                        <div class="col-lg-4 col-sm-6 col-6 total-section text-right">
                            <p>Boxes: <b class="text-primary">{{@$boxeInfo['countBoxes']}}</b></p>
                        </div>

                        <div class="col-lg-4 col-sm-6 col-6 total-section text-right">
                            <p>Total Units:<b class="text-info"> {{$totalQty}}</b></p>
                        </div>
                    </div>
                    @if($myCarts)
                        @foreach($myCarts as $details)
                            <div class="row cart-detail">
                                <div class="col-lg-4 col-sm-4 col-4 cart-detail-img">
                                    <img src="{{ $details->image }}" />
                                </div>
                                <div class="col-lg-8 col-sm-8 col-8 cart-detail-product">
                                    <p>{{ $details->name }}  <small>({{$details->stems}})</small></p>
                                    <span class="price text-info"> ${{ $details->price * $details->stems }}</span> <span class="count"> Quantity:{{ $details->quantity }}</span>
                                </div>
                            </div>
                        @endforeach
                        <div class="row">
                            <div class="col center-block text-center mb-2">
                                @if($boxeInfo['boxMatched'])
                                    <small class="text-primary"><b>Box capacity met. Review my order!</b></small>
                                @else
                                    <small class="text-danger"><b>Box capacity not met. </b></small>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col checkout">
                                <a href="{{ route('view.cart') }}"
                                   title="@lang('View all items in cart')"
                                   data-toggle="tooltip"
                                   data-placement="top"
                                   class="btn btn-primary btn-block p-2">
                                    <i class="fas fa-shopping-cart"></i> &nbsp;View Cart
                                </a>
                            </div>

                            <div class="col checkout">
                                <a href="{{ route('empty.cart') }}"
                                   class="btn btn-danger btn-block p-2"
                                   title="@lang('Delete all items from cart?')"
                                   data-toggle="tooltip"
                                   data-placement="top"
                                   data-method="GET"
                                   data-confirm-title="@lang('Please Confirm')"
                                   data-confirm-text="@lang('Are you sure that you want to remove all items from cart?')"
                                   data-confirm-delete="@lang('Yes, delete all!')">
                                    <i class="fas fa-shopping-cart"></i> &nbsp;Empty Cart
                                </a>
                            </div>
                        </div>
                    @endif
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
