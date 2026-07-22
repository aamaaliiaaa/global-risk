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
    <div class="col-sm-6 col-xl-3">
        <div class="detail-card h-100 p-3 shadow-sm border-0 rounded-4">
            <div class="d-flex align-items-center">
                <div class="rounded-3 bg-primary-subtle text-primary p-3 me-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-globe fs-4"></i>
                </div>
                <div>
                    <span class="text-secondary small fw-medium text-uppercase tracking-wider">Total Countries</span>
                    <h3 class="mb-0 fw-bold mt-1 text-dark">{{ number_format($stats['total']) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="detail-card h-100 p-3 shadow-sm border-0 rounded-4">
            <div class="d-flex align-items-center">
                <div class="rounded-3 bg-danger-subtle text-danger p-3 me-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                </div>
                <div>
                    <span class="text-secondary small fw-medium text-uppercase tracking-wider">High Risk</span>
                    <h3 class="mb-0 fw-bold mt-1 text-danger">{{ number_format($stats['high']) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="detail-card h-100 p-3 shadow-sm border-0 rounded-4">
            <div class="d-flex align-items-center">
                <div class="rounded-3 bg-warning-subtle text-warning p-3 me-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-graph-up-arrow fs-4"></i>
                </div>
                <div>
                    <span class="text-secondary small fw-medium text-uppercase tracking-wider">Medium Risk</span>
                    <h3 class="mb-0 fw-bold mt-1 text-warning">{{ number_format($stats['medium']) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="detail-card h-100 p-3 shadow-sm border-0 rounded-4">
            <div class="d-flex align-items-center">
                <div class="rounded-3 bg-success-subtle text-success p-3 me-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-shield-check fs-4"></i>
                </div>
                <div>
                    <span class="text-secondary small fw-medium text-uppercase tracking-wider">Low Risk</span>
                    <h3 class="mb-0 fw-bold mt-1 text-success">{{ number_format($stats['low']) }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="detail-card p-4 border-0 shadow-sm rounded-4 mb-4">
    <!-- Search & Filter Toolbar -->
    <form method="GET" action="{{ route('countries.index') }}" class="row g-2 mb-4">
        <div class="col-md-7">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Search country name...">
            </div>
        </div>
        <div class="col-md-3">
            <select name="risk" class="form-select" onchange="this.form.submit()">
                <option value="">All Risk Levels</option>
                <option value="High" {{ request('risk') == 'High' ? 'selected' : '' }}>High Risk</option>
                <option value="Medium" {{ request('risk') == 'Medium' ? 'selected' : '' }}>Medium Risk</option>
                <option value="Low" {{ request('risk') == 'Low' ? 'selected' : '' }}>Low Risk</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
            @if(request()->hasAny(['search', 'risk']))
                <a href="{{ route('countries.index') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Country</th>
                    <th>Risk Category</th>
                    <th>Weather Overview</th>
                    <th>Currency Code</th>
                    <th class="text-end pe-3">Profile Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($countries as $country)
                <tr>
                    <td class="ps-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-4">{{ $country->flag }}</span>
                            <span class="fw-bold text-dark fs-6">{{ $country->name }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $country->risk == 'High' ? 'bg-danger-subtle text-danger border border-danger-subtle' : ($country->risk == 'Medium' ? 'bg-warning-subtle text-warning border border-warning-subtle' : 'bg-success-subtle text-success border border-success-subtle') }} px-3 py-1 rounded-pill">
                            {{ $country->risk }} Risk
                        </span>
                    </td>
                    <td>
                        <span class="text-secondary fw-medium">{{ $country->weather }}</span>
                    </td>
                    <td>
                        <span class="badge bg-light text-secondary border fw-semibold">{{ strtoupper($country->currency) }}</span>
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('countries.show', $country->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                            View Profile &rarr;
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