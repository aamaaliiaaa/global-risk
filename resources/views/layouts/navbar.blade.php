<nav class="navbar-custom">

    <!-- Search Box -->
    <div class="search-box">
        <i class="bi bi-search"></i>
        <form action="{{ route('countries.index') }}" method="GET" class="w-100 m-0">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari negara, pelabuhan, berita..." class="border-0 bg-transparent w-100">
        </form>
    </div>

    <!-- Right Controls: Notification & User Profile -->
    <div class="navbar-right d-flex align-items-center gap-3">

        <!-- Notification Bell Dropdown -->
        <div class="dropdown">
            <button class="btn btn-light bg-white border rounded-circle p-2 d-flex align-items-center justify-content-center position-relative shadow-xs" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="System Notifications" style="width: 38px; height: 38px;">
                <i class="bi bi-bell text-secondary fs-6"></i>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger rounded-circle border border-white" style="width: 10px; height: 10px;"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-3 rounded-4 mt-2" style="width: 280px;">
                <li class="fw-bold text-dark mb-2 border-bottom pb-2" style="font-size: 13px;">System Notifications</li>
                <li class="small text-muted mb-2"><span class="text-success me-1">●</span> Real-time weather feeds active</li>
                <li class="small text-muted mb-2"><span class="text-success me-1">●</span> Exchange rates updated from ECB</li>
                <li class="small text-muted"><span class="text-danger me-1">●</span> High risk maritime alerts active</li>
            </ul>
        </div>

        <!-- User Profile Pill Dropdown -->
        <div class="dropdown">
            <div class="profile-card d-flex align-items-center gap-2.5 p-1.5 pe-2.5 rounded-pill border bg-white shadow-xs" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; min-width: 34px; font-size: 13px;">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                </div>
                <div class="d-none d-md-block text-start" style="line-height: 1.15;">
                    <div class="fw-bold text-dark" style="font-size: 13px;">{{ Auth::user()->name ?? 'User' }}</div>
                    <small class="text-muted" style="font-size: 10.5px;">
                        {{ Auth::user() && (Auth::user()->is_admin || Auth::user()->email === 'admin@globalrisk.com') ? 'Administrator' : 'Importer' }}
                    </small>
                </div>
                <i class="bi bi-chevron-down text-muted ms-1" style="font-size: 11px;"></i>
            </div>

            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-4 mt-2 p-2" style="min-width: 220px;">
                <li class="px-3 py-2 border-bottom mb-1">
                    <div class="fw-bold text-dark" style="font-size: 13px;">{{ Auth::user()->name ?? 'User' }}</div>
                    <div class="text-muted small" style="font-size: 11px;">{{ Auth::user()->email ?? '' }}</div>
                </li>
                @if(Auth::user() && (Auth::user()->is_admin || Auth::user()->email === 'admin@globalrisk.com'))
                <li>
                    <a class="dropdown-item rounded-3 py-2 text-primary fw-medium" href="{{ route('admin.index') }}">
                        <i class="bi bi-shield-lock me-2"></i> Admin Panel
                    </a>
                </li>
                @endif
                <li>
                    <a class="dropdown-item rounded-3 py-2 text-secondary" href="{{ route('watchlist.index') }}">
                        <i class="bi bi-star me-2"></i> My Watchlist
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item rounded-3 py-2 text-danger fw-semibold w-100 text-start border-0 bg-transparent">
                            <i class="bi bi-box-arrow-right me-2"></i> Log Out
                        </button>
                    </form>
                </li>
            </ul>
        </div>

    </div>

</nav>