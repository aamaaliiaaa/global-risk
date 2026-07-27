@extends('layouts.app')

@section('title', 'Port ' . $port->name)

@section('content')

<!-- Header Section -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="fs-2 me-1">{{ $port->country->flag ?? '🚢' }}</span>
            <h1 class="dashboard-title mb-0">Port {{ $port->name }}</h1>
        </div>
        <p class="page-subtitle mb-0">
            Port profile, operation status, congestion evaluation, and live local weather conditions.
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('shipping.index', ['origin_id' => $port->id]) }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm fw-bold d-flex align-items-center gap-2">
            <i class="bi bi-calculator-fill fs-6"></i>
            <span>Hitung Estimasi Pengiriman</span>
        </a>
        <a href="{{ route('ports.index') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-medium">
            <i class="bi bi-arrow-left me-1"></i> Back to Ports
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Structured Port Metadata Sub-Cards -->
    <div class="col-lg-5">
        <div class="detail-card h-100 p-4 border-0 shadow-sm rounded-4 d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle-fill text-primary"></i> Operational Status & Weather
                </h5>

                <div class="row g-3">
                    <!-- Status & Congestion Risk Box -->
                    <div class="col-12">
                        <div class="p-3 rounded-3 border bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted fw-bold text-uppercase" style="font-size: 11px;">Current Port Status</small>
                                @if($port->status == 'Normal')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fs-6 fw-bold">Normal Operation</span>
                                @elseif($port->status == 'Busy')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1.5 rounded-pill fs-6 fw-bold">Busy Queue</span>
                                @elseif($port->status == 'Delay')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fs-6 fw-bold">Delayed Schedule</span>
                                @else
                                    <span class="badge bg-danger text-white px-3 py-1.5 rounded-pill fs-6 fw-bold">Congested Terminal</span>
                                @endif
                            </div>

                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-semibold text-secondary">Port Risk & Congestion Index</span>
                                    <strong class="small text-dark">{{ $port->risk_score }}/100</strong>
                                </div>
                                <div class="progress rounded-pill" style="height: 8px;">
                                    <div class="progress-bar {{ $port->risk_score > 70 ? 'bg-danger' : ($port->risk_score > 40 ? 'bg-warning' : 'bg-success') }}" 
                                         role="progressbar" 
                                         style="width: {{ $port->risk_score }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Specs Box -->
                    <div class="col-12">
                        <div class="p-3 rounded-3 border bg-white shadow-xs">
                            <small class="text-muted fw-bold text-uppercase d-block mb-2" style="font-size: 11px;">Location Details</small>
                            <div class="row g-2">
                                <div class="col-6">
                                    <span class="small text-muted d-block">City Name</span>
                                    <strong class="text-dark fs-6">{{ $port->city }}</strong>
                                </div>
                                <div class="col-6">
                                    <span class="small text-muted d-block">Country Jurisdiction</span>
                                    <strong class="text-dark fs-6">{{ $port->country->flag ?? '' }} {{ $port->country->name ?? 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Weather Conditions Box -->
                    <div class="col-12">
                        <div class="p-3 rounded-3 border bg-white shadow-xs">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted fw-bold text-uppercase" style="font-size: 11px;">Local Maritime Weather</small>
                                <span class="badge bg-light text-dark border px-2.5 py-1">
                                    {{ $condition }}
                                </span>
                            </div>

                            <div class="row g-2 text-center mt-1">
                                <div class="col-6">
                                    <div class="p-2 rounded-3 bg-light border">
                                        <small class="text-muted d-block" style="font-size: 10px;">Temperature</small>
                                        <strong class="text-dark fs-6">🌡️ {{ $weather['temperature'] }}°C</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded-3 bg-light border">
                                        <small class="text-muted d-block" style="font-size: 10px;">Wind Velocity</small>
                                        <strong class="text-dark fs-6">💨 {{ $weather['wind_speed'] }} km/h</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shortcut Link -->
            <div class="pt-3 text-center border-top mt-3">
                <a href="{{ route('shipping.index', ['origin_id' => $port->id]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-semibold">
                    <i class="bi bi-signpost-split me-1"></i> Use this port as origin in Shipping Estimator &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Right Column: Interactive Map Card -->
    <div class="col-lg-7">
        <div class="detail-card h-100 p-4 border-0 shadow-sm rounded-4">
            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-geo-alt-fill text-primary"></i> Geographic Coordinate Location
            </h5>
            <div id="portMap" style="height: 420px; border-radius: 14px; min-height: 350px;" class="shadow-xs border"></div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const lat = {{ $port->latitude ?? 'null' }};
    const lng = {{ $port->longitude ?? 'null' }};

    if (lat !== null && lng !== null && (lat !== 0 || lng !== 0)) {
        const map = L.map('portMap').setView([lat, lng], 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([lat, lng])
            .addTo(map)
            .bindPopup(`
                <div style="font-family: 'Poppins', sans-serif; padding: 4px;">
                    <h6 class="fw-bold mb-1">🚢 Port: ${@json($port->name)}</h6>
                    <span class="badge bg-primary text-white mb-1">Status: ${@json($port->status)}</span>
                    <div class="small text-muted">City: ${@json($port->city)}</div>
                </div>
            `)
            .openPopup();

        setTimeout(() => map.invalidateSize(), 200);
    } else {
        document.getElementById('portMap').innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted bg-light rounded-3" style="min-height:250px;">Coordinates not available for this port.</div>';
    }
});
</script>
@endsection