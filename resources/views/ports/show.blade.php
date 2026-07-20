@extends('layouts.app')

@section('title', 'Port ' . $port->name)

@section('content')

<div class="page-header">
    <div>
        <h1 class="dashboard-title">🚢 {{ $port->name }}</h1>
        <p class="page-subtitle">
            Port profile, operation status, and local weather forecasts.
        </p>
    </div>
    <div>
        <a href="{{ route('ports.index') }}" class="btn btn-secondary rounded-3 px-4 py-2">
            <i class="bi bi-arrow-left"></i> Back to Ports
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Port Metadata Card -->
    <div class="col-md-5">
        <div class="dashboard-card h-100">
            <div class="section-title">
                <i class="bi bi-info-circle-fill"></i> Operational Status
            </div>
            
            <div class="detail-item mt-3">
                <strong>City</strong>
                <span>{{ $port->city }}</span>
            </div>

            <div class="detail-item">
                <strong>Country</strong>
                <span>{{ $port->country->flag }} {{ $port->country->name }}</span>
            </div>

            <div class="detail-item">
                <strong>Status</strong>
                <span>
                    @if($port->status == 'Normal')
                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">Normal</span>
                    @elseif($port->status == 'Busy')
                    <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill fw-semibold">Busy</span>
                    @elseif($port->status == 'Delay')
                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-semibold">Delay</span>
                    @else
                    <span class="badge bg-danger text-white px-3 py-2 rounded-pill fw-semibold">Congested</span>
                    @endif
                </span>
            </div>

            <div class="detail-item">
                <strong>Port Risk Score</strong>
                <span><strong>{{ $port->risk_score }}/100</strong></span>
            </div>

            <div class="detail-item">
                <strong>Local Temperature</strong>
                <span>🌡️ {{ $weather['temperature'] }}°C</span>
            </div>

            <div class="detail-item">
                <strong>Wind Speed</strong>
                <span>💨 {{ $weather['wind_speed'] }} km/h</span>
            </div>

            <div class="detail-item border-bottom-0">
                <strong>Weather Condition</strong>
                <span>{{ $condition }}</span>
            </div>
        </div>
    </div>

    <!-- Map Card -->
    <div class="col-md-7">
        <div class="dashboard-card h-100">
            <div class="section-title">
                <i class="bi bi-geo-alt-fill text-primary"></i> Geographic Coordinate Location
            </div>
            <div id="portMap" style="height: 380px; border-radius: 12px; margin-top: 15px;"></div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const lat = {{ $port->latitude }};
    const lng = {{ $port->longitude }};

    const map = L.map('portMap').setView([lat, lng], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([lat, lng])
        .addTo(map)
        .bindPopup('<strong>🚢 Port: {{ $port->name }}</strong><br>Status: {{ $port->status }}')
        .openPopup();
});
</script>
@endsection