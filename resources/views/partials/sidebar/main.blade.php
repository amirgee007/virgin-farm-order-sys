<nav class="col-md-2 sidebar custom-scroll-bar">
    <div class="user-box text-center pt-2 pb-1">
        <div class="user-img">
            <img src="{{ auth()->user()->present()->avatar }}"
                 width="75"
                 height="75"
                 alt="user-img"
                 class="rounded-circle img-thumbnail img-responsive">
        </div>

        <h5 class="my-2">
            <a href="{{ route('profile') }}">{{ auth()->user()->present()->nameOrEmail }}</a>
        </h5>

        <ul class="list-inline mb-1">
            <li class="list-inline-item">
                <a href="{{ route('log-viewer::logs.list') }}" title="Logs Viewer">
                    <i class="fas fa-history"></i>
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

            @permission(['products.manage', 'client.inventory'] ,false)
            <li class="nav-item">
                <a href="#reports-dropdown"
                   class="nav-link"
                   data-toggle="collapse"
                   aria-expanded="{{ Request::is('products*') ? 'true' : 'false' }}">
                    <i class="fa fa-chart-bar"></i>
                    <span>@lang('product.products')</span>
                </a>
                <ul class="{{ Request::is('products*')  ? '' : 'collapse' }} list-unstyled sub-menu" id="reports-dropdown">

                    @permission('client.inventory')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('products/inventory*') ? 'active' : ''  }}" href="{{ route('inventory.index') }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span>@lang('Inventory')</span>
                        </a>
                    </li>
                    @endpermission

                    @permission('products.manage')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('products/manage') ? 'active' : ''  }}" href="{{ route('products.index.manage') }}">
                            <i class="fas fa-tag"></i>
                            <span>@lang('Products')</span>
                        </a>
                    </li>
                    @endpermission

                    {{--<li class="nav-item">--}}
                        {{--<a class="nav-link {{ Request::is('reports/stock-report/*') ? 'active' : ''  }}"--}}
                           {{--href="{{route('reports.stock.report')}}">--}}
                            {{--@lang('report.stock_report')</a>--}}
                    {{--</li>--}}

                </ul>
            </li>
            @endpermission

            @permission('orders.manage')
            <li class="nav-item">
                <a href="#orders-dropdown"
                   class="nav-link"
                   data-toggle="collapse"
                   aria-expanded="{{ Request::is('orders*') ? 'true' : 'false' }}">
                    <i class="fa fa-shopping-cart"></i>
                    <span>@lang('order.orders')</span>
                </a>
                <ul class="{{ Request::is('orders*')  ? '' : 'collapse' }} list-unstyled sub-menu" id="orders-dropdown">

                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('orders') ? 'active' : ''  }}" href="{{route('orders.index')}}">
                            <i class="fas fa-car"></i>
                            <span>@lang('My Orders')</span>
                        </a>
                    </li>

                </ul>
            </li>
            @endpermission
            @permission('manage.promo.codes')
            <li class="nav-item">
                <a class="nav-link {{ Request::is('promo-codes*') ? 'active' : ''  }}" href="{{ route('promo_codes.index') }}">
                    <i class="fas fa-wine-glass"></i>
                    <span>@lang('Promo Codes')</span>
                </a>
            </li>
            @endpermission

            @permission('manage.color.class')
            <li class="nav-item">
                <a class="nav-link {{ Request::is('color-class*') ? 'active' : ''  }}" href="{{ route('colors_class.index') }}">
                    <i class="fas fa-wine-glass"></i>
                    <span>@lang('Manage Colors')</span>
                </a>
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

            @permission('help.faq.index')
                <li class="nav-item">
                    <a id="popoverButton"
                       class="nav-link {{ Request::is('help-faq*') ? 'active' : ''  }}"
                       href="{{ route('help.faq.index') }}"
                       data-placement="right"
                       data-toggle="popover"
                       title="Checkout Out Help & FAQ Page"
                       data-html="true"
                       data-content="<div>Watch tutorials for web shop navigation. See FAQs and dashboard management here.<br><a href='#' id='hidePopover' class='popover-link'>Don’t show me again</a></div>">
                      <i class="fas fa-question-circle"></i>
                      <span>@lang('Help & FAQs')</span>
                    </a>
                </li>
            @endpermission

            @permission('sales.rep.index')
            <li class="nav-item">
                <a class="nav-link {{ Request::is('sales-rep*') ? 'active' : ''  }}" href="{{ route('sales.rep.index') }}">
                    <i class="fas fa-user-alt"></i>
                    <span>@lang('Contact Sales')</span>
                </a>
            </li>
            @endpermission

            @permission('shipping.address.index')
            <li class="nav-item">
                <a class="nav-link {{ Request::is('shipping-address*') ? 'active' : ''  }}" href="{{ route('shipping.address.index') }}">
                    <i class="fas fa-address-card"></i>
                    <span>@lang('Shipping Address')</span>
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

            @permission('products.manage')
            <li class="nav-item">
                <a target="_blank" class="nav-link" href="https://scribehow.com/page/Web_Shop_Maintenance_Topics__LLhKs5_JSt-ohAt_Y0IMWA">
                    <i class="fas fa-hands-helping"></i>
                    <span>@lang('Maintenance Manual')</span>
                </a>
            </li>
            @endpermission
        </ul>
    </div>
</nav>

