@extends('layouts.app')

@section('title', 'Countries Directory')

@section('content')

<!-- Header Section -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h1 class="dashboard-title mb-1">
            <i class="bi bi-globe text-primary me-2"></i>Global Countries Directory
        </h1>
        <p class="page-subtitle mb-0">
            Monitor country risk scores, real-time weather, exchange rates, and logistics information.
        </p>
    </div>
</div>

<!-- Summary KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card primary">
            <div class="card-top">
                <span class="card-title">Total Countries</span>
                <div class="stat-icon bg-primary text-white"><i class="bi bi-globe"></i></div>
            </div>
            <h2>{{ number_format($stats['total']) }}</h2>
            <div class="card-subtitle text-success mt-2"><i class="bi bi-check-circle-fill me-1"></i> Active Profiles</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card danger">
            <div class="card-top">
                <span class="card-title">High Risk</span>
                <div class="stat-icon bg-danger text-white"><i class="bi bi-exclamation-triangle-fill"></i></div>
            </div>
            <h2 class="text-danger">{{ number_format($stats['high']) }}</h2>
            <div class="card-subtitle text-danger mt-2"><i class="bi bi-shield-exclamation me-1"></i> High Alert</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card warning">
            <div class="card-top">
                <span class="card-title">Medium Risk</span>
                <div class="stat-icon bg-warning text-white"><i class="bi bi-graph-up-arrow"></i></div>
            </div>
            <h2 class="text-warning">{{ number_format($stats['medium']) }}</h2>
            <div class="card-subtitle text-warning mt-2"><i class="bi bi-activity me-1"></i> Moderate Watch</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card success">
            <div class="card-top">
                <span class="card-title">Low Risk</span>
                <div class="stat-icon bg-success text-white"><i class="bi bi-shield-check"></i></div>
            </div>
            <h2 class="text-success">{{ number_format($stats['low']) }}</h2>
            <div class="card-subtitle text-success mt-2"><i class="bi bi-check2-circle me-1"></i> Stable State</div>
        </div>
    </div>
</div>

<div class="detail-card p-4 border-0 shadow-sm rounded-4 mb-4">
    <!-- Search & Filter Toolbar -->
    <form method="GET" action="{{ route('countries.index') }}" class="row g-2 mb-4">
        <div class="col-md-7">
            <div class="position-relative">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control ps-5 py-2.5 rounded-3 border" placeholder="Search country name...">
            </div>
        </div>
        <div class="col-md-3">
            <select name="risk" class="form-select py-2.5 rounded-3 border" onchange="this.form.submit()">
                <option value="">All Risk Levels</option>
                <option value="High" {{ request('risk') == 'High' ? 'selected' : '' }}>High Risk</option>
                <option value="Medium" {{ request('risk') == 'Medium' ? 'selected' : '' }}>Medium Risk</option>
                <option value="Low" {{ request('risk') == 'Low' ? 'selected' : '' }}>Low Risk</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3"><i class="bi bi-funnel me-1"></i> Filter</button>
            @if(request()->hasAny(['search', 'risk']))
                <a href="{{ route('countries.index') }}" class="btn btn-outline-secondary rounded-3 py-2.5 px-3" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-3">Country Name</th>
                    <th>Risk Category</th>
                    <th>Weather Overview</th>
                    <th>Currency Code</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($countries as $country)
                <tr style="cursor: pointer;" onclick="window.location='{{ route('countries.show', $country->id) }}'">
                    <td class="ps-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-4">{{ $country->flag }}</span>
                            <strong class="text-dark fw-bold" style="font-size: 14px;">{{ $country->name }}</strong>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $country->risk == 'High' ? 'bg-danger-subtle text-danger' : ($country->risk == 'Medium' ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success') }} px-3 py-1.5 rounded-pill">
                            {{ $country->risk }} Risk
                        </span>
                    </td>
                    <td>
                        <span class="text-secondary fw-medium small">{{ $country->weather }}</span>
                    </td>
                    <td>
                        <span class="badge bg-light text-secondary border fw-semibold">{{ strtoupper($country->currency) }}</span>
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('countries.show', $country->id) }}" class="btn btn-sm btn-primary rounded-3 px-3 py-1.5 fw-semibold" style="font-size: 12px;">
                            View Details &rarr;
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-search fs-1 d-block mb-2 text-muted"></i>
                        No countries matched your search criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection