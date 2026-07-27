<div class="sidebar">

    <!-- Logo & Brand Header -->
    <div class="logo">
        <div class="logo-icon">
            <i class="bi bi-shield-check"></i>
        </div>
        <div class="logo-text">
            <h2>GlobalRisk</h2>
            <span>Enterprise Intelligence</span>
        </div>
    </div>

    <!-- Navigation Menu -->
    <div class="sidebar-menu">
        
        <!-- Section: Overview -->
        <div class="menu-header">OVERVIEW</div>
        <ul>
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('countries.index') }}" class="{{ request()->routeIs('countries.*') ? 'active' : '' }}">
                    <i class="bi bi-globe2"></i>
                    <span>Countries Directory</span>
                </a>
            </li>
        </ul>

        <!-- Section: Logistics & Maritime -->
        <div class="menu-header">MARITIME & LOGISTICS</div>
        <ul>
            <li>
                <a href="{{ route('ports.index') }}" class="{{ request()->routeIs('ports.*') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>Ports Intelligence</span>
                </a>
            </li>
            <li>
                <a href="{{ route('shipping.index') }}" class="{{ request()->routeIs('shipping.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam-fill"></i>
                    <span>Shipping Estimator</span>
                    <span class="badge bg-primary-subtle text-primary ms-auto me-1 px-2 py-0.5 rounded-pill" style="font-size: 10px;">Pro</span>
                </a>
            </li>
        </ul>

        <!-- Section: Analytics & Intelligence -->
        <div class="menu-header">ANALYTICS & FEEDS</div>
        <ul>
            <li>
                <a href="{{ route('weather.index') }}" class="{{ request()->routeIs('weather.*') ? 'active' : '' }}">
                    <i class="bi bi-cloud-rain-fill"></i>
                    <span>Weather Feeds</span>
                </a>
            </li>
            <li>
                <a href="{{ route('currency.index') }}" class="{{ request()->routeIs('currency.*') ? 'active' : '' }}">
                    <i class="bi bi-currency-exchange"></i>
                    <span>Exchange Rates</span>
                </a>
            </li>
            <li>
                <a href="{{ route('news.index') }}" class="{{ request()->routeIs('news.*') ? 'active' : '' }}">
                    <i class="bi bi-newspaper"></i>
                    <span>Market News</span>
                </a>
            </li>
            <li>
                <a href="{{ route('compare.index') }}" class="{{ request()->routeIs('compare.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    <span>Country Comparison</span>
                </a>
            </li>
            <li>
                <a href="{{ route('watchlist.index') }}" class="{{ request()->routeIs('watchlist.*') ? 'active' : '' }}">
                    <i class="bi bi-star-fill"></i>
                    <span>My Watchlist</span>
                </a>
            </li>
        </ul>

        <!-- Section: System & Admin -->
        @if(Auth::check() && Auth::user()->is_admin)
        <div class="menu-header">ADMINISTRATION</div>
        <ul>
            <li>
                <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span>Admin Panel</span>
                </a>
            </li>
        </ul>
        @endif

        <div class="menu-header">ACCOUNT</div>
        <ul>
            <li>
                <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form" class="d-none">
                    @csrf
                </form>
                <a href="#" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();" class="text-danger fw-semibold">
                    <i class="bi bi-box-arrow-right text-danger"></i>
                    <span>Log Out</span>
                </a>
            </li>
        </ul>

    </div>

</div>