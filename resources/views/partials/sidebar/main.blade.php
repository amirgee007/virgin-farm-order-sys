<nav class="col-md-2 sidebar custom-scroll-bar">
    <div class="user-box text-center pt-3 pb-3">
        <div class="user-img">
            <img src="{{ auth()->user()->present()->avatar }}"
                 width="75"
                 height="75"
                 alt="user-img"
                 class="rounded-circle img-thumbnail img-responsive">
        </div>

        <h5 class="my-3">
            <a href="{{ route('profile') }}">{{ auth()->user()->present()->nameOrEmail }}</a>
        </h5>

        <ul class="list-inline mb-2">
            <li class="list-inline-item">
                <a href="{{ route('profile') }}" title="@lang('My Profile')">
                    <i class="fas fa-cog"></i>
                </a>
            </li>

            <li class="list-inline-item">
                <a href="{{ route('shipping.address.index') }}" class="text-custom" title="@lang('Shipping Addresses')">
                    <i class="fas fa-address-card"></i>
                </a>
            </li>

            <li class="list-inline-item">
                <a href="{{ route('auth.logout') }}" class="text-custom" title="@lang('Logout')">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </li>
        </ul>
    </div>


    <div class="sidebar-sticky custom-scroll-bar">
        <ul class="nav flex-column">

            <li class="nav-item">
                <a class="nav-link {{ Request::is('/') ? 'active' : ''  }}" href="{{ route('dashboard') }}">
                    <i class="fas fa-home"></i>
                    <span>@lang('Dashboard')</span>
                </a>
            </li>

            @permission(['products.manage', 'products.manage'] ,false)
            <li class="nav-item">
                <a href="#reports-dropdown"
                   class="nav-link"
                   data-toggle="collapse"
                   aria-expanded="{{ Request::is('products*') ? 'true' : 'false' }}">
                    <i class="fa fa-chart-bar"></i>
                    <span>@lang('product.products')</span>
                </a>
                <ul class="{{ Request::is('reports*')  ? '' : 'collapse' }} list-unstyled sub-menu" id="reports-dropdown">

                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('products*') ? 'active' : ''  }}" href="{{ route('products.index') }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span>@lang('Products')</span>
                        </a>
                    </li>

                    {{--<li class="nav-item">--}}
                        {{--<a class="nav-link {{ Request::is('reports/stock-report/*') ? 'active' : ''  }}"--}}
                           {{--href="{{route('reports.stock.report')}}">--}}
                            {{--@lang('report.stock_report')</a>--}}
                    {{--</li>--}}

                </ul>
            </li>
            @endpermission

            @permission('boxes.manage')
            <li class="nav-item">
                <a class="nav-link {{ Request::is('boxes*') ? 'active' : ''  }}" href="{{ route('boxes.index') }}">
                    <i class="fas fa-box"></i>
                    <span>@lang('Manage Boxes')</span>
                </a>
            </li>
            @endpermission

            @permission('carriers.manage')
            <li class="nav-item">
                <a class="nav-link {{ Request::is('carriers*') ? 'active' : ''  }}" href="{{ route('carriers.index') }}">
                    <i class="fas fa-calculator"></i>
                    <span>@lang('Manage Carriers')</span>
                </a>
            </li>
            @endpermission

            @permission('notification.index')
            <li class="nav-item">
                <a class="nav-link {{ Request::is('notifications*') ? 'active' : ''  }}" href="{{ route('notifications.index') }}">
                    <i class="fas fa-envelope"></i>
                    <span>@lang('Notifications')</span>
                </a>
            </li>
            @endpermission

            @permission('shipping.address.index')
            <li class="nav-item">
                <a class="nav-link {{ Request::is('shipping-address*') ? 'active' : ''  }}" href="{{ route('shipping.address.index') }}">
                    <i class="fas fa-address-card"></i>
                    <span>@lang('Shipping Address ')</span>
                </a>
            </li>
            @endpermission

            @permission('categories.index')
            <li class="nav-item">
                <a class="nav-link {{ Request::is('categories*') ? 'active' : ''  }}" href="{{ route('categories.index') }}">
                    <i class="fas fa-address-card"></i>
                    <span>@lang('Categories')</span>
                </a>
            </li>
            @endpermission

            @foreach (\Vanguard\Plugins\Vanguard::availablePlugins() as $plugin)
                @include('partials.sidebar.items', ['item' => $plugin->sidebar()])
            @endforeach
        </ul>
    </div>
</nav>

