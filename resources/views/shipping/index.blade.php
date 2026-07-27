@extends('layouts.app')

@section('title', 'Estimasi Pengiriman')

@section('content')

<!-- Header Section -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h1 class="dashboard-title mb-1 d-flex align-items-center gap-2">
            <i class="bi bi-box-seam-fill text-primary"></i> Shipping Arrival Estimator & Risk Factor Analysis
        </h1>
        <p class="page-subtitle mb-0">
            Kalkulasi estimasi waktu kedatangan (ETA) kargo maritim dari Pelabuhan Asal ke Pelabuhan Tujuan beserta transparansi alasan & faktor risiko.
        </p>
    </div>
    <div>
        <span class="badge bg-primary-subtle text-primary border px-3 py-2 rounded-pill">
            <i class="bi bi-broadcast text-primary me-1"></i> AIS & Weather Live Feeds
        </span>
    </div>
</div>

@if($errorMsg)
    <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $errorMsg }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Quick Popular Route Presets Bar -->
@if(!empty($popularPresets))
<div class="d-flex align-items-center flex-wrap gap-2 mb-3">
    <span class="text-muted fw-bold small me-1">
        <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick Popular Routes:
    </span>
    @foreach($popularPresets as $preset)
        @if($preset['origin_id'] && $preset['dest_id'])
            <a href="{{ route('shipping.index', ['origin_id' => $preset['origin_id'], 'destination_id' => $preset['dest_id'], 'vessel_speed' => $vesselSpeed]) }}" 
               class="badge bg-white text-secondary border px-3 py-2 rounded-pill text-decoration-none shadow-xs d-flex align-items-center gap-1 hover-lift">
                <span>{{ $preset['name'] }}</span>
                <span class="badge bg-light text-primary border ms-1" style="font-size: 10px;">{{ $preset['badge'] }}</span>
            </a>
        @endif
    @endforeach
</div>
@endif

<!-- Shipping Calculator Form Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-header bg-white py-3 px-4 border-bottom border-light">
        <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 16px;">
            <i class="bi bi-sliders text-primary"></i> Parameter Pengiriman Kapal Laut
        </h5>
    </div>
    <div class="card-body p-4">
        <form method="GET" action="{{ route('shipping.index') }}" id="shipping-calc-form">
            <div class="row g-3">
                <!-- Port A (Origin) -->
                <div class="col-md-5">
                    <label class="form-label fw-semibold text-secondary small mb-1">
                        <i class="bi bi-geo-alt-fill text-success me-1"></i> Pelabuhan Asal (Port A)
                    </label>
                    <select name="origin_id" class="form-select py-2.5 rounded-3 border" required>
                        <option value="">-- Pilih Pelabuhan Asal --</option>
                        @foreach($ports as $p)
                            <option value="{{ $p->id }}" {{ ((string)$originId === (string)$p->id || (!$originId && $loop->first)) ? 'selected' : '' }}>
                                {{ $p->country->flag ?? '' }} {{ $p->name }} ({{ $p->country->name ?? 'N/A' }}) - Status: {{ $p->status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Direction Swap / Icon -->
                <div class="col-md-2 d-flex align-items-end justify-content-center pb-1">
                    <div class="badge bg-primary-subtle text-primary border rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-arrow-right d-none d-md-inline fs-5"></i>
                        <i class="bi bi-arrow-down d-inline d-md-none fs-5"></i>
                    </div>
                </div>

                <!-- Port B (Destination) -->
                <div class="col-md-5">
                    <label class="form-label fw-semibold text-secondary small mb-1">
                        <i class="bi bi-flag-fill text-danger me-1"></i> Pelabuhan Tujuan (Port B)
                    </label>
                    <select name="destination_id" class="form-select py-2.5 rounded-3 border" required>
                        <option value="">-- Pilih Pelabuhan Tujuan --</option>
                        @foreach($ports as $p)
                            <option value="{{ $p->id }}" {{ ((string)$destinationId === (string)$p->id || (!$destinationId && $loop->index === 1)) ? 'selected' : '' }}>
                                {{ $p->country->flag ?? '' }} {{ $p->name }} ({{ $p->country->name ?? 'N/A' }}) - Status: {{ $p->status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Vessel Speed -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-secondary small mb-1">
                        <i class="bi bi-speedometer2 text-info me-1"></i> Kecepatan Kapal (Knots / Mil Laut per Jam)
                    </label>
                    <select name="vessel_speed" class="form-select py-2.5 rounded-3 border">
                        <option value="14.0" {{ (float)$vesselSpeed === 14.0 ? 'selected' : '' }}>Slow Steaming (14 Knots - Hemat Bahan Bakar)</option>
                        <option value="18.0" {{ (float)$vesselSpeed === 18.0 || !$vesselSpeed ? 'selected' : '' }}>Standard Cargo Vessel (18 Knots - Standar)</option>
                        <option value="22.0" {{ (float)$vesselSpeed === 22.0 ? 'selected' : '' }}>Express Container Ship (22 Knots - Cepat)</option>
                        <option value="25.0" {{ (float)$vesselSpeed === 25.0 ? 'selected' : '' }}>High-Speed Vessel (25 Knots - Maksimal)</option>
                    </select>
                </div>

                <!-- Departure Datetime -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-secondary small mb-1">
                        <i class="bi bi-calendar-event text-warning me-1"></i> Waktu Keberangkatan (Departure Date & Time)
                    </label>
                    <input type="datetime-local" name="departure_date" class="form-control py-2.5 rounded-3 border" value="{{ $departureDate }}" required>
                </div>

                <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4 py-2.5 rounded-3 fw-bold shadow-sm">
                        <i class="bi bi-calculator me-1"></i> Hitung Estimasi & Analisis Penyebab
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($estimation)

<!-- Main Estimation Overview Cards -->
<div class="row g-3 mb-4">
    <!-- ETA Card -->
    <div class="col-lg-4 col-md-6">
        <div class="card card-primary-gradient border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-white text-primary fw-bold px-3 py-1 rounded-pill">Estimasi Tiba (ETA)</span>
                        <i class="bi bi-clock-history fs-3 opacity-75"></i>
                    </div>
                    <h2 class="fw-bold mt-2 mb-0" style="font-size: 1.6rem; color: #ffffff;">
                        {{ $estimation['eta_date']->translatedFormat('d F Y, H:i') }}
                    </h2>
                    <p class="text-white-50 small mt-1 mb-0">
                        Waktu Berangkat: {{ $estimation['departure_date']->translatedFormat('d M Y, H:i') }}
                    </p>
                </div>

                <div class="mt-4 pt-3 border-top border-white-10 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-white-50">Total Waktu Perjalanan</div>
                        <div class="fw-bold fs-5">{{ $estimation['formatted_duration'] }}</div>
                    </div>
                    <div class="text-end">
                        <div class="small text-white-50">Total Durasi</div>
                        <div class="fw-bold fs-5">{{ number_format($estimation['total_days'], 1) }} Hari</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distance & Speed Card -->
    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-top border-4 border-info">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted fw-bold">Jarak Maritim Laut</span>
                        <div class="stat-icon bg-info text-white rounded-circle p-2">
                            <i class="bi bi-compass-fill fs-4"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold text-dark mb-1">
                        {{ number_format($estimation['distance_nm'], 0) }} <span class="fs-5 text-muted fw-normal">NM</span>
                    </h2>
                    <p class="text-muted small mb-0">
                        Setara dengan {{ number_format($estimation['distance_km'], 0) }} km (Jarak Great Circle)
                    </p>
                </div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Waktu Layar Murni</span>
                        <div class="fw-bold text-dark">{{ number_format($estimation['base_days'], 1) }} Hari</div>
                    </div>
                    <div class="text-end">
                        <span class="text-muted small">Kecepatan Layar</span>
                        <div class="fw-bold text-dark">{{ $estimation['vessel_speed_knots'] }} Knots</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delay Risk Summary Card -->
    <div class="col-lg-4 col-md-12">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-top border-4 {{ $estimation['overall_delay_risk'] === 'High' ? 'border-danger' : ($estimation['overall_delay_risk'] === 'Medium' ? 'border-warning' : 'border-success') }}">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted fw-bold">Total Tambahan Delay Risk</span>
                        @if($estimation['overall_delay_risk'] === 'High')
                            <span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill fw-bold">Risiko Tinggi</span>
                        @elseif($estimation['overall_delay_risk'] === 'Medium')
                            <span class="badge bg-warning-subtle text-warning px-3 py-1 rounded-pill fw-bold">Risiko Sedang</span>
                        @else
                            <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill fw-bold">Risiko Rendah</span>
                        @endif
                    </div>
                    <h2 class="fw-bold text-dark mb-1">
                        +{{ number_format($estimation['total_delay_days'], 1) }} <span class="fs-5 text-muted fw-normal">Hari Buffer</span>
                    </h2>
                    <p class="text-muted small mb-0">
                        Penambahan waktu akibat akumulasi kemacetan, cuaca & keamanan.
                    </p>
                </div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Port A Status</span>
                        <div class="fw-semibold text-dark">{{ $estimation['origin_port']->status }}</div>
                    </div>
                    <div class="text-end">
                        <span class="text-muted small">Port B Status</span>
                        <div class="fw-semibold text-dark">{{ $estimation['destination_port']->status }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Factors & Reasons Breakdown -->
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 px-4 border-bottom border-light d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 16px;">
                    <i class="bi bi-diagram-3-fill text-primary"></i> Rincian Penyebab & Alasan Durasi Estimasi
                </h5>
                <span class="badge bg-light text-secondary border">Transparansi Faktor</span>
            </div>
            <div class="card-body p-4">
                <div class="timeline-factors">
                    <!-- Base Transit Row -->
                    <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-light mb-3">
                        <div class="rounded-circle bg-primary text-white p-2.5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                            <i class="bi bi-ship fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-1 text-dark">Waktu Transit Layar Dasar (Base Transit)</h6>
                                <span class="badge bg-primary px-2.5 py-1 rounded-pill">{{ number_format($estimation['base_days'], 1) }} Hari</span>
                            </div>
                            <p class="text-muted small mb-0">
                                Waktu berlayar murni berdasarkan jarak maritim <strong>{{ number_format($estimation['distance_nm'], 0) }} NM</strong> dengan kecepatan rata-rata <strong>{{ $estimation['vessel_speed_knots'] }} knots</strong> tanpa kendala luar.
                            </p>
                        </div>
                    </div>

                    <!-- Dynamic Delay Factors -->
                    @foreach($estimation['delay_factors'] as $factor)
                        <div class="d-flex align-items-start gap-3 p-3 rounded-3 border border-light mb-3 bg-white shadow-xs">
                            <div class="rounded-circle 
                                {{ $factor['severity'] === 'danger' ? 'bg-danger text-white' : ($factor['severity'] === 'warning' ? 'bg-warning text-dark' : ($factor['severity'] === 'info' ? 'bg-info text-white' : 'bg-success text-white')) }} 
                                p-2.5 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                @if($factor['type'] === 'port_origin' || $factor['type'] === 'port_dest')
                                    <i class="bi bi-buildings fs-5"></i>
                                @elseif($factor['type'] === 'weather')
                                    <i class="bi bi-cloud-lightning-rain fs-5"></i>
                                @elseif($factor['type'] === 'risk')
                                    <i class="bi bi-shield-exclamation fs-5"></i>
                                @else
                                    <i class="bi bi-signpost-split fs-5"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-light text-secondary me-1">{{ $factor['category'] }}</span>
                                        <strong class="text-dark">{{ $factor['title'] }}</strong>
                                    </div>
                                    <span class="badge 
                                        {{ $factor['severity'] === 'danger' ? 'bg-danger-subtle text-danger' : ($factor['severity'] === 'warning' ? 'bg-warning-subtle text-dark' : ($factor['severity'] === 'info' ? 'bg-info-subtle text-info' : 'bg-success-subtle text-success')) }} 
                                        px-2.5 py-1 rounded-pill fw-bold">
                                        {{ $factor['impact_days'] > 0 ? '+' . number_format($factor['impact_days'], 1) . ' Hari' : '0 Hari Delay' }}
                                    </span>
                                </div>
                                <p class="text-secondary small mt-2 mb-0">
                                    {{ $factor['explanation'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Port & Route Overview Sidebar Info -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 px-4 border-bottom border-light">
                <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 16px;">
                    <i class="bi bi-info-circle-fill text-info"></i> Ringkasan Kondisi Rute & Pelabuhan
                </h5>
            </div>
            <div class="card-body p-4">
                <!-- Origin Port Details -->
                <div class="p-3 rounded-3 bg-light mb-3 border">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="text-uppercase text-muted fw-bold small">Pelabuhan Asal</span>
                            <h6 class="fw-bold text-dark mb-0">
                                {{ $estimation['origin_port']->country->flag ?? '' }} {{ $estimation['origin_port']->name }}
                            </h6>
                            <small class="text-muted">{{ $estimation['origin_port']->city }}, {{ $estimation['origin_port']->country->name ?? '' }}</small>
                        </div>
                        <span class="badge {{ $estimation['origin_port']->status === 'Normal' ? 'bg-success-subtle text-success' : ($estimation['origin_port']->status === 'Busy' ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') }}">
                            {{ $estimation['origin_port']->status }}
                        </span>
                    </div>
                    <hr class="my-2 border-secondary-subtle">
                    <div class="row g-2 text-center small mt-1">
                        <div class="col-4">
                            <div class="text-muted">Cuaca</div>
                            <div class="fw-semibold text-dark">{{ $estimation['origin_weather']['condition'] ?? 'Unknown' }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted">Suhu</div>
                            <div class="fw-semibold text-dark">{{ $estimation['origin_weather']['temp'] ?? '-' }}°C</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted">Risk Score</div>
                            <div class="fw-semibold text-dark">{{ $estimation['origin_port']->risk_score ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Destination Port Details -->
                <div class="p-3 rounded-3 bg-light mb-3 border">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="text-uppercase text-muted fw-bold small">Pelabuhan Tujuan</span>
                            <h6 class="fw-bold text-dark mb-0">
                                {{ $estimation['destination_port']->country->flag ?? '' }} {{ $estimation['destination_port']->name }}
                            </h6>
                            <small class="text-muted">{{ $estimation['destination_port']->city }}, {{ $estimation['destination_port']->country->name ?? '' }}</small>
                        </div>
                        <span class="badge {{ $estimation['destination_port']->status === 'Normal' ? 'bg-success-subtle text-success' : ($estimation['destination_port']->status === 'Busy' ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') }}">
                            {{ $estimation['destination_port']->status }}
                        </span>
                    </div>
                    <hr class="my-2 border-secondary-subtle">
                    <div class="row g-2 text-center small mt-1">
                        <div class="col-4">
                            <div class="text-muted">Cuaca</div>
                            <div class="fw-semibold text-dark">{{ $estimation['dest_weather']['condition'] ?? 'Unknown' }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted">Suhu</div>
                            <div class="fw-semibold text-dark">{{ $estimation['dest_weather']['temp'] ?? '-' }}°C</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted">Risk Score</div>
                            <div class="fw-semibold text-dark">{{ $estimation['destination_port']->risk_score ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Action links -->
                <div class="d-grid gap-2">
                    <a href="{{ route('ports.show', $estimation['origin_port']->id) }}" class="btn btn-outline-primary btn-sm rounded-3">
                        <i class="bi bi-eye me-1"></i> Detail Pelabuhan Asal ({{ $estimation['origin_port']->name }})
                    </a>
                    <a href="{{ route('ports.show', $estimation['destination_port']->id) }}" class="btn btn-outline-primary btn-sm rounded-3">
                        <i class="bi bi-eye me-1"></i> Detail Pelabuhan Tujuan ({{ $estimation['destination_port']->name }})
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Map Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white py-3 px-4 border-bottom border-light d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 16px;">
            <i class="bi bi-map-fill text-success"></i> Visualisasi Peta Jalur Maritim
        </h5>
        <span class="badge bg-light text-dark border">
            {{ $estimation['origin_port']->name }} &rarr; {{ $estimation['destination_port']->name }}
        </span>
    </div>
    <div class="card-body p-0">
        <div id="shipping-map" style="height: 440px; width: 100%;"></div>
    </div>
</div>

@endif

@endsection

@push('scripts')
@if($estimation)
<script>
document.addEventListener("DOMContentLoaded", function () {
    const originLat = {{ $estimation['origin_port']->latitude ?? 0 }};
    const originLng = {{ $estimation['origin_port']->longitude ?? 0 }};
    const destLat = {{ $estimation['destination_port']->latitude ?? 0 }};
    const destLng = {{ $estimation['destination_port']->longitude ?? 0 }};

    if (originLat && originLng && destLat && destLng) {
        const map = L.map('shipping-map').setView([(originLat + destLat) / 2, (originLng + destLng) / 2], 3);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Custom Icons
        const originIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background-color:#16a34a;color:white;width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,0.3);'><i class='bi bi-geo-alt-fill fs-5'></i></div>",
            iconSize: [34, 34],
            iconAnchor: [17, 34]
        });

        const destIcon = L.divIcon({
            className: 'custom-div-icon',
            html: "<div style='background-color:#dc2626;color:white;width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,0.3);'><i class='bi bi-flag-fill fs-5'></i></div>",
            iconSize: [34, 34],
            iconAnchor: [17, 34]
        });

        // Origin Marker
        L.marker([originLat, originLng], { icon: originIcon })
            .addTo(map)
            .bindPopup(`
                <div style="font-family: Poppins, sans-serif;">
                    <span class="badge bg-success">Pelabuhan Asal</span>
                    <h6 class="fw-bold mt-1 mb-1">{{ $estimation['origin_port']->name }}</h6>
                    <p class="small text-muted mb-0">Status: <strong>{{ $estimation['origin_port']->status }}</strong></p>
                    <p class="small text-muted mb-0">Cuaca: {{ $estimation['origin_weather']['condition'] ?? 'Unknown' }}</p>
                </div>
            `);

        // Destination Marker
        L.marker([destLat, destLng], { icon: destIcon })
            .addTo(map)
            .bindPopup(`
                <div style="font-family: Poppins, sans-serif;">
                    <span class="badge bg-danger">Pelabuhan Tujuan</span>
                    <h6 class="fw-bold mt-1 mb-1">{{ $estimation['destination_port']->name }}</h6>
                    <p class="small text-muted mb-0">Status: <strong>{{ $estimation['destination_port']->status }}</strong></p>
                    <p class="small text-muted mb-0">Cuaca: {{ $estimation['dest_weather']['condition'] ?? 'Unknown' }}</p>
                </div>
            `);

        // Draw curved/dashed maritime line
        const latlngs = [
            [originLat, originLng],
            [destLat, destLng]
        ];

        const polyline = L.polyline(latlngs, {
            color: '#2563eb',
            weight: 4,
            opacity: 0.8,
            dashArray: '8, 8'
        }).addTo(map);

        map.fitBounds(polyline.getBounds(), { padding: [50, 50] });
    }
});
</script>
@endif
@endpush
