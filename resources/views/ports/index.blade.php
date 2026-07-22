@extends('layouts.app')

@section('title', 'Ports')

@section('content')

<div class="page-header">
    <div>
        <h1 class="dashboard-title">Ports Directory</h1>
        <p class="page-subtitle">
            Monitor <strong>{{ number_format($totalPorts) }}</strong> international sea ports worldwide — operational status, risk score & live location map.
        </p>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
    <!-- Total Ports -->
    <div class="col-md-3">
        <div class="dashboard-card h-100 d-flex flex-column justify-content-center" style="border-top: 4px solid #2563eb;">
            <div class="d-flex justify-content-between align-items-start">
                <div class="text-muted fw-semibold">Total<br>Ports</div>
                <div class="stat-icon bg-primary text-white"><i class="bi bi-geo-alt-fill"></i></div>
            </div>
            <h2 class="mt-3 mb-1 fw-bold">{{ number_format($stats['total']) }}</h2>
            <small class="text-success"><i class="bi bi-arrow-up-short"></i> Seeded</small>
        </div>
    </div>
    <!-- Critical/Congested Ports -->
    <div class="col-md-3">
        <div class="dashboard-card h-100 d-flex flex-column justify-content-center" style="border-top: 4px solid #ef4444;">
            <div class="d-flex justify-content-between align-items-start">
                <div class="text-muted fw-semibold">Delay /<br>Congested</div>
                <div class="stat-icon bg-danger text-white"><i class="bi bi-exclamation-octagon"></i></div>
            </div>
            <h2 class="mt-3 mb-1 fw-bold">{{ number_format($stats['congested']) }}</h2>
            <small class="text-danger">Critical State</small>
        </div>
    </div>
    <!-- Busy Ports -->
    <div class="col-md-3">
        <div class="dashboard-card h-100 d-flex flex-column justify-content-center" style="border-top: 4px solid #f59e0b;">
            <div class="d-flex justify-content-between align-items-start">
                <div class="text-muted fw-semibold">Busy<br>Status</div>
                <div class="stat-icon bg-warning text-white"><i class="bi bi-cone-striped"></i></div>
            </div>
            <h2 class="mt-3 mb-1 fw-bold">{{ number_format($stats['busy']) }}</h2>
            <small class="text-warning">High Traffic</small>
        </div>
    </div>
    <!-- Normal Ports -->
    <div class="col-md-3">
        <div class="dashboard-card h-100 d-flex flex-column justify-content-center" style="border-top: 4px solid #22c55e;">
            <div class="d-flex justify-content-between align-items-start">
                <div class="text-muted fw-semibold">Normal<br>Status</div>
                <div class="stat-icon bg-success text-white"><i class="bi bi-check2-circle"></i></div>
            </div>
            <h2 class="mt-3 mb-1 fw-bold">{{ number_format($stats['normal']) }}</h2>
            <small class="text-success">Operational</small>
        </div>
    </div>
</div>

<div class="country-toolbar mb-4">
    <form method="GET" action="{{ route('ports.index') }}" class="d-flex flex-wrap gap-3 align-items-center w-100" id="port-filter-form">
        <!-- Search input -->
        <div class="search-country flex-grow-1 position-relative" style="min-width: 200px;">
            <i class="bi bi-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
            <input
                type="text"
                name="search"
                placeholder="Search port, city, or country..."
                value="{{ request('search') }}"
                class="form-control py-2 ps-5 rounded-3 border"
            >
        </div>

        <!-- Country dropdown -->
        <select name="country_id" onchange="this.form.submit()" class="form-select py-2 rounded-3 border" style="width: 220px;">
            <option value="">All Countries</option>
            @foreach($countries as $c)
                <option value="{{ $c->id }}" {{ request('country_id') == $c->id ? 'selected' : '' }}>
                    {{ $c->flag }} {{ $c->name }}
                </option>
            @endforeach
        </select>

        <!-- Status dropdown -->
        <select name="status" onchange="this.form.submit()" class="form-select py-2 rounded-3 border" style="width: 160px;">
            <option value="">All Status</option>
            <option value="Normal"    {{ request('status') == 'Normal'    ? 'selected' : '' }}>Normal</option>
            <option value="Busy"      {{ request('status') == 'Busy'      ? 'selected' : '' }}>Busy</option>
            <option value="Delay"     {{ request('status') == 'Delay'     ? 'selected' : '' }}>Delay</option>
            <option value="Congested" {{ request('status') == 'Congested' ? 'selected' : '' }}>Congested</option>
        </select>

        <button type="submit" class="btn btn-primary px-4 rounded-3">
            <i class="bi bi-search me-1"></i> Search
        </button>

        @if(request()->hasAny(['search', 'status', 'country_id']))
        <a href="{{ route('ports.index') }}" class="btn btn-outline-secondary px-3 rounded-3">
            <i class="bi bi-x-circle"></i> Clear
        </a>
        @endif
    </form>
</div>

<div class="row g-4">

    <!-- Port Table List (Left) -->
    <div class="col-lg-6">
        <div class="dashboard-card border-0 shadow-sm rounded-4 overflow-hidden p-0">
            <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                <span class="fw-semibold text-dark">
                    <i class="bi bi-list-ul me-2 text-primary"></i>
                    Ports List
                </span>
                <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2 rounded-3">
                    {{ $ports->total() }} results
                </span>
            </div>

            <div style="max-height: 520px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light sticky-top" style="top: 0;">
                        <tr>
                            <th class="ps-3" style="min-width: 200px;">Port Name</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Risk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ports as $port)
                        <tr
                            style="cursor: pointer;"
                            onclick="focusPortMap({{ $port->latitude }}, {{ $port->longitude }}, '{{ addslashes($port->name) }}')"
                            title="Click to locate on map"
                        >
                            <td class="ps-3">
                                <span class="me-1">🚢</span>
                                <strong>{{ $port->name }}</strong>
                                <div class="text-muted small">{{ $port->city }}</div>
                            </td>
                            <td>
                                <span class="me-1">{{ $port->country->flag ?? '' }}</span>
                                <span class="small">{{ $port->country->name ?? '-' }}</span>
                            </td>
                            <td>
                                @if($port->status == 'Normal')
                                    <span class="badge bg-success-subtle text-success px-2 py-1 rounded fw-semibold">Normal</span>
                                @elseif($port->status == 'Busy')
                                    <span class="badge bg-warning-subtle text-warning px-2 py-1 rounded fw-semibold">Busy</span>
                                @elseif($port->status == 'Delay')
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded fw-semibold">Delay</span>
                                @else
                                    <span class="badge bg-danger text-white px-2 py-1 rounded fw-semibold">Congested</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <strong class="text-dark">{{ $port->risk_score }}</strong>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-compass fs-2 d-block mb-2"></i>
                                No ports found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($ports->hasPages())
            <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Showing {{ $ports->firstItem() }}–{{ $ports->lastItem() }} of {{ number_format($ports->total()) }} ports
                </small>
                <div class="d-flex gap-1">
                    @if($ports->onFirstPage())
                        <span class="btn btn-sm btn-outline-secondary disabled rounded-3">
                            <i class="bi bi-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $ports->previousPageUrl() }}" class="btn btn-sm btn-outline-primary rounded-3">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    @endif

                    <span class="btn btn-sm btn-primary rounded-3 px-3">
                        {{ $ports->currentPage() }} / {{ $ports->lastPage() }}
                    </span>

                    @if($ports->hasMorePages())
                        <a href="{{ $ports->nextPageUrl() }}" class="btn btn-sm btn-outline-primary rounded-3">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    @else
                        <span class="btn btn-sm btn-outline-secondary disabled rounded-3">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Interactive Map (Right) -->
    <div class="col-lg-6">
        <div class="dashboard-card h-100" style="min-height: 600px; display: flex; flex-direction: column;">
            <div class="section-title d-flex justify-content-between align-items-center">
                <span>
                    <i class="bi bi-geo-alt-fill text-primary me-1"></i>
                    Global Ports Interactive Map
                </span>
                <small class="text-muted fw-normal">
                    <i class="bi bi-cursor me-1"></i> Click row to focus
                </small>
            </div>

            <!-- Legend -->
            <div class="d-flex gap-3 mb-3 flex-wrap">
                <span class="small"><span style="display:inline-block;width:12px;height:12px;background:#22c55e;border-radius:50%;"></span> Normal</span>
                <span class="small"><span style="display:inline-block;width:12px;height:12px;background:#f59e0b;border-radius:50%;"></span> Busy</span>
                <span class="small"><span style="display:inline-block;width:12px;height:12px;background:#ef4444;border-radius:50%;"></span> Delay / Congested</span>
            </div>

            <div id="portsMap" style="flex-grow: 1; border-radius: 16px; min-height: 520px;"></div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const map = L.map('portsMap').setView([15, 20], 2);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    setTimeout(() => map.invalidateSize(), 200);

    // All map ports (full dataset, not paginated)
    const allPorts = @json($mapPorts);
    const markerMap = {};

    allPorts.forEach(function (port) {
        if (!port.latitude || !port.longitude) return;

        let color = '#22C55E'; // Normal
        if (port.status === 'Congested' || port.status === 'Delay') {
            color = '#EF4444';
        } else if (port.status === 'Busy') {
            color = '#F59E0B';
        }

        let marker = L.circleMarker([port.latitude, port.longitude], {
            radius: 5,
            fillColor: color,
            color: '#fff',
            weight: 1,
            opacity: 1,
            fillOpacity: 0.85
        }).addTo(map)
          .bindPopup(`
            <div style="font-family: 'Poppins', sans-serif; min-width: 180px;">
                <strong>🚢 ${port.name}</strong><br>
                <span class="text-muted">${port.city}</span><br>
                Country: ${port.country ? port.country.flag + ' ' + port.country.name : '-'}<br>
                Status: <strong style="color:${color}">${port.status}</strong><br>
                Risk Score: <strong>${port.risk_score}</strong><br>
                <a href="/ports/${port.id}" style="text-decoration:none;color:#2563EB;font-weight:600;font-size:12px;">View Detail →</a>
            </div>
          `);

        markerMap[`${port.latitude},${port.longitude}`] = marker;
    });

    window.focusPortMap = function (lat, lng, name) {
        if (!lat || !lng) return;
        map.setView([lat, lng], 10, { animate: true });
        const key = `${lat},${lng}`;
        if (markerMap[key]) {
            markerMap[key].openPopup();
        }
    };
});
</script>
@endsection