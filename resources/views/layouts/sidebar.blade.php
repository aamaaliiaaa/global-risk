<div class="sidebar">

    <div class="logo">

        <div class="logo-icon">
            <i class="bi bi-globe2"></i>
        </div>

        <div class="logo-text">

            <h2>GlobalRisk</h2>

            <span>Risk Intelligence</span>

        </div>

    </div>

    <ul>

        <li>

            <a href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">

                <i class="bi bi-grid-fill"></i>

                Dashboard

            </a>

        </li>

        <li>

            <a href="{{ route('countries.index') }}"
                class="{{ request()->routeIs('countries.*') ? 'active' : '' }}">
                <i class="bi bi-globe"></i>
                Countries
            </a>

        </li>

        <li>

            <a href="{{ route('weather.index') }}"
                class="{{ request()->routeIs('weather.*') ? 'active' : '' }}">

                <i class="bi bi-cloud-rain"></i>

                Weather

            </a>

        </li>

        <li>

            <a href="{{ route('currency.index') }}"
                class="{{ request()->routeIs('currency.*') ? 'active' : '' }}">

                <i class="bi bi-currency-exchange"></i>

                Currency

            </a>

        </li>

        <li>

            <a href="{{ route('news.index') }}"
                class="{{ request()->routeIs('news.*') ? 'active' : '' }}">

                <i class="bi bi-newspaper"></i>

                News

            </a>

        </li>

        <li>

            <a href="{{ route('ports.index') }}"
                class="{{ request()->routeIs('ports.*') ? 'active' : '' }}">

                <i class="bi bi-geo-alt-fill"></i>

                Ports

            </a>

        </li>

        <li>

            <a href="{{ route('compare.index') }}"
                class="{{ request()->routeIs('compare.*') ? 'active' : '' }}">

                <i class="bi bi-bar-chart"></i>

                Comparison

            </a>

        </li>

        <li>

            <a href="{{ route('watchlist.index') }}"
                class="{{ request()->routeIs('watchlist.*') ? 'active' : '' }}">

                <i class="bi bi-star-fill"></i>

                Watchlist

            </a>

        </li>

        @if(Auth::check() && Auth::user()->is_admin)
        <li>
            <a href="{{ route('admin.index') }}"
                class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock-fill"></i>
                Admin Panel
            </a>
        </li>
        @endif

        <li class="mt-3 pt-3 border-top">
            <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form" class="d-none">
                @csrf
            </form>
            <a href="#" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();" class="text-danger fw-semibold">
                <i class="bi bi-box-arrow-right text-danger"></i>
                Log Out
            </a>
        </li>

    </ul>

</div>