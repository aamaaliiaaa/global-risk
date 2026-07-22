@extends('layouts.app')

@section('title', 'Global Weather Intelligence')

@section('content')

<!-- Header Section -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h1 class="dashboard-title mb-1">
            <i class="bi bi-cloud-sun text-primary me-2"></i>Global Weather Intelligence
        </h1>
        <p class="page-subtitle mb-0">
            Real-time meteorological monitoring & severe weather disruption risk evaluation for international trade corridors.
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('weather.index', ['refresh' => 1]) }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm d-flex align-items-center gap-2 fw-medium" title="Force refresh live weather data from Open-Meteo API">
            <i class="bi bi-arrow-repeat fs-6"></i>
            <span>Sync Live Data</span>
        </a>
    </div>
</div>

<!-- Summary Metrics Bar -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="detail-card h-100 p-3 shadow-sm border-0 rounded-4">
            <div class="d-flex align-items-center">
                <div class="rounded-3 bg-primary-subtle text-primary p-3 me-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-globe-americas fs-4"></i>
                </div>
                <div>
                    <span class="text-secondary small fw-medium text-uppercase tracking-wider">Monitored Countries</span>
                    <h3 class="mb-0 fw-bold mt-1 text-dark">{{ number_format($stats['total']) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="detail-card h-100 p-3 shadow-sm border-0 rounded-4">
            <div class="d-flex align-items-center">
                <div class="rounded-3 bg-success-subtle text-success p-3 me-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-brightness-high fs-4"></i>
                </div>
                <div>
                    <span class="text-secondary small fw-medium text-uppercase tracking-wider">Optimal Skies</span>
                    <h3 class="mb-0 fw-bold mt-1 text-success">{{ number_format($stats['clear']) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="detail-card h-100 p-3 shadow-sm border-0 rounded-4">
            <div class="d-flex align-items-center">
                <div class="rounded-3 bg-warning-subtle text-warning p-3 me-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-cloud-rain-heavy fs-4"></i>
                </div>
                <div>
                    <span class="text-secondary small fw-medium text-uppercase tracking-wider">Adverse Weather</span>
                    <h3 class="mb-0 fw-bold mt-1 text-warning">{{ number_format($stats['adverse']) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="detail-card h-100 p-3 shadow-sm border-0 rounded-4">
            <div class="d-flex align-items-center">
                <div class="rounded-3 bg-info-subtle text-info p-3 me-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-wind fs-4"></i>
                </div>
                <div>
                    <span class="text-secondary small fw-medium text-uppercase tracking-wider">High Winds (>20km/h)</span>
                    <h3 class="mb-0 fw-bold mt-1 text-info">{{ number_format($stats['windy']) }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Section: Interactive Map + Spotlight -->
<div class="row g-4 mb-4">
    <!-- Weather Map -->
    <div class="col-lg-8">
        <div class="detail-card h-100 d-flex flex-column p-4 border-0 shadow-sm rounded-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-map-fill text-primary me-2"></i> Live Weather Map Overview</h4>
                <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
                    <i class="bi bi-clock-history me-1"></i> Live Sync: {{ $stats['last_updated'] }}
                </span>
            </div>
            <div id="weatherMap" style="height: 440px; border-radius: 16px; min-height: 380px;" class="shadow-sm border"></div>
        </div>
    </div>

    <!-- Weather Spotlight Card -->
    <div class="col-lg-4">
        <div class="detail-card h-100 p-4 border-0 shadow-sm rounded-4 d-flex flex-column justify-content-between bg-gradient-subtle">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold">
                        <i class="bi bi-star-fill me-1"></i> Weather Spotlight
                    </span>
                    <span class="text-muted small"><i class="bi bi-geo-alt me-1"></i> Real-time Sensor</span>
                </div>

                @if($featuredWeather)
                <div id="spotlightCard">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="fs-1">{{ $featuredWeather['flag'] }}</span>
                        <div>
                            <h3 class="mb-0 fw-bold text-dark" id="spotlightName">{{ $featuredWeather['name'] }}</h3>
                            <span class="badge {{ $featuredWeather['badge_class'] }} rounded-pill px-3 py-1 mt-1" id="spotlightCondition">
                                {{ $featuredWeather['icon'] }} {{ $featuredWeather['condition'] }}
                            </span>
                        </div>
                    </div>

                    <div class="my-4 p-3 bg-light rounded-4 border text-center">
                        <div class="display-4 fw-bold text-primary mb-0" id="spotlightTemp">{{ $featuredWeather['temperature'] }}°C</div>
                        <span class="text-muted small" id="spotlightRisk">Logistics Risk: <strong>{{ $featuredWeather['risk'] }}</strong></span>
                    </div>

                    <div class="space-y-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary"><i class="bi bi-wind text-info me-2"></i> Wind Speed</span>
                            <strong class="text-dark" id="spotlightWind">{{ $featuredWeather['wind_speed'] }} km/h</strong>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ min(100, $featuredWeather['wind_speed'] * 2.5) }}%" id="spotlightWindBar"></div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary"><i class="bi bi-droplet-half text-primary me-2"></i> Relative Humidity</span>
                            <strong class="text-dark" id="spotlightHumidity">{{ $featuredWeather['humidity'] }}%</strong>
                        </div>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $featuredWeather['humidity'] }}%" id="spotlightHumidityBar"></div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="pt-3 border-top mt-3">
                <button onclick="focusWeatherMap({{ $featuredWeather['latitude'] ?? 0 }}, {{ $featuredWeather['longitude'] ?? 0 }})" class="btn btn-outline-primary rounded-pill w-100 py-2 fw-semibold">
                    <i class="bi bi-crosshair me-2"></i> Center Spotlight on Map
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section: Global Weather Directory Grid -->
<div class="detail-card p-4 border-0 shadow-sm rounded-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="bi bi-grid-fill text-primary me-2"></i> Country Weather Reports Directory</h4>
            <p class="text-muted small mb-0">Browse real-time weather metrics for monitored countries across global trade corridors.</p>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('weather.index') }}" class="d-flex gap-2" style="max-width: 360px; width: 100%;">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Search country name...">
                @if(request('search'))
                    <a href="{{ route('weather.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                @endif
                <button type="submit" class="btn btn-primary px-3">Search</button>
            </div>
        </form>
    </div>

    <!-- Weather Cards Grid (3 Columns) -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mb-4">
        @forelse($weatherData as $w)
        <div class="col">
            <div class="card h-100 border rounded-4 shadow-sm p-3 bg-white hover-card transition" style="border-color: #e2e8f0 !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-4">{{ $w['flag'] }}</span>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-6">{{ $w['name'] }}</h6>
                            <span class="badge {{ $w['badge_class'] }} rounded-pill px-2 py-1 mt-1" style="font-size: 11px;">
                                {{ $w['icon'] }} {{ $w['condition'] }}
                            </span>
                        </div>
                    </div>
                    <button onclick="focusWeatherMap({{ $w['latitude'] }}, {{ $w['longitude'] }}, '{{ addslashes($w['name']) }}', '{{ $w['flag'] }}', '{{ $w['icon'] }}', '{{ $w['condition'] }}', {{ $w['temperature'] }}, {{ $w['wind_speed'] }}, {{ $w['humidity'] }}, '{{ $w['risk'] }}')" 
                            class="btn btn-sm btn-light border rounded-circle p-2 text-primary shadow-sm" 
                            style="width: 36px; height: 36px;" 
                            title="Locate {{ $w['name'] }} on map">
                        <i class="bi bi-crosshair"></i>
                    </button>
                </div>

                <div class="d-flex justify-content-between align-items-center bg-light rounded-3 p-3 mb-3 border">
                    <div>
                        <div class="fs-2 fw-bold text-primary mb-0 lh-1">{{ $w['temperature'] }}°C</div>
                        <small class="text-muted" style="font-size: 11px;">Current Temp</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-xs" style="font-size: 11px;">
                            {{ $w['risk'] }}
                        </span>
                    </div>
                </div>

                <div class="row g-2 text-muted small border-top pt-3 mt-auto" style="font-size: 12px;">
                    <div class="col-6 border-end">
                        <i class="bi bi-wind text-info me-1 fs-6"></i> Wind: <strong>{{ $w['wind_speed'] }} km/h</strong>
                    </div>
                    <div class="col-6 ps-3">
                        <i class="bi bi-droplet-half text-primary me-1 fs-6"></i> Humidity: <strong>{{ $w['humidity'] }}%</strong>
                    </div>
                </div>

                <div class="pt-3 mt-2 border-top text-end">
                    <a href="{{ route('countries.show', $w['id']) }}" class="btn btn-sm btn-primary rounded-pill px-4 py-1 fw-semibold" style="font-size: 12px;">
                        Full Country Profile <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-cloud-slash fs-1 text-muted"></i>
            <h5 class="mt-3 text-secondary">No weather reports found</h5>
            <p class="text-muted small">Try searching with a different country name.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(method_exists($countries, 'hasPages') && $countries->hasPages())
        <div class="d-flex justify-content-center pt-3 border-top">
            {{ $countries->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const map = L.map('weatherMap').setView([15, 20], 2);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    setTimeout(() => map.invalidateSize(), 200);

    const mapWeatherData = @json($mapWeatherData);
    const markers = [];

    mapWeatherData.forEach(function (data) {
        if (data.latitude && data.longitude) {
            let marker = L.marker([data.latitude, data.longitude]).addTo(map)
                .bindPopup(`
                    <div style="font-family: 'Poppins', sans-serif; min-width: 170px;">
                        <h6 class="mb-1 fw-bold">${data.flag} ${data.name}</h6>
                        <div class="mb-2"><span class="badge bg-light text-dark border">${data.icon} ${data.condition}</span></div>
                        <div class="small mb-1">🌡️ Temp: <strong>${data.temperature}°C</strong></div>
                        <div class="small mb-1">💨 Wind: <strong>${data.wind_speed} km/h</strong></div>
                        <div class="small mb-2">💧 Humidity: <strong>${data.humidity}%</strong></div>
                        <div class="text-end">
                            <a href="/countries/${data.id}" class="btn btn-primary btn-sm py-1 px-3 text-white text-decoration-none rounded-pill" style="font-size:11px;">View Profile &rarr;</a>
                        </div>
                    </div>
                `);
            markers.push({
                lat: data.latitude,
                lng: data.longitude,
                data: data,
                marker: marker
            });
        }
    });

    window.focusWeatherMap = function (lat, lng, name, flag, icon, condition, temp, wind, humidity, risk) {
        if (lat && lng) {
            map.setView([lat, lng], 6);
            markers.forEach(function (m) {
                if (Math.abs(m.lat - lat) < 0.01 && Math.abs(m.lng - lng) < 0.01) {
                    m.marker.openPopup();
                }
            });

            // Update Spotlight Card
            if (name) {
                document.getElementById('spotlightName').textContent = name;
                document.getElementById('spotlightTemp').textContent = temp + '°C';
                document.getElementById('spotlightCondition').innerHTML = icon + ' ' + condition;
                document.getElementById('spotlightWind').textContent = wind + ' km/h';
                document.getElementById('spotlightHumidity').textContent = humidity + '%';
                document.getElementById('spotlightRisk').innerHTML = 'Logistics Risk: <strong>' + risk + '</strong>';
                document.getElementById('spotlightWindBar').style.width = Math.min(100, wind * 2.5) + '%';
                document.getElementById('spotlightHumidityBar').style.width = humidity + '%';
            }
        }
    };
});
</script>
@endsection
