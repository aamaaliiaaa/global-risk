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
    <div class="col-6 col-xl-3">
        <div class="stat-card primary">
            <div class="card-top">
                <span class="card-title">Monitored Countries</span>
                <div class="stat-icon bg-primary text-white"><i class="bi bi-globe-americas"></i></div>
            </div>
            <h2>{{ number_format($stats['total']) }}</h2>
            <div class="card-subtitle text-success mt-2"><i class="bi bi-check-circle-fill me-1"></i> Active Satellites</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card success">
            <div class="card-top">
                <span class="card-title">Optimal Skies</span>
                <div class="stat-icon bg-success text-white"><i class="bi bi-brightness-high"></i></div>
            </div>
            <h2 class="text-success">{{ number_format($stats['clear']) }}</h2>
            <div class="card-subtitle text-success mt-2"><i class="bi bi-sun me-1"></i> Clear Navigation</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card warning">
            <div class="card-top">
                <span class="card-title">Adverse Weather</span>
                <div class="stat-icon bg-warning text-white"><i class="bi bi-cloud-rain-heavy"></i></div>
            </div>
            <h2 class="text-warning">{{ number_format($stats['adverse']) }}</h2>
            <div class="card-subtitle text-warning mt-2"><i class="bi bi-cloud-drizzle me-1"></i> Rain / Storm Buffer</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card info">
            <div class="card-top">
                <span class="card-title">High Winds (>20km/h)</span>
                <div class="stat-icon bg-info text-white"><i class="bi bi-wind"></i></div>
            </div>
            <h2 class="text-info">{{ number_format($stats['windy']) }}</h2>
            <div class="card-subtitle text-info mt-2"><i class="bi bi-speedometer2 me-1"></i> Wind Warning</div>
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
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success-subtle text-success border">● Optimal</span>
                    <span class="badge bg-warning-subtle text-warning border">● Rain</span>
                    <span class="badge bg-danger-subtle text-danger border">● Storm</span>
                </div>
            </div>
            <div id="weatherMap" style="height: 440px; border-radius: 16px; min-height: 380px;" class="shadow-sm border"></div>
        </div>
    </div>

    <!-- Weather Spotlight Card -->
    <div class="col-lg-4">
        <div class="detail-card h-100 p-4 border-0 shadow-sm rounded-4 d-flex flex-column justify-content-between bg-gradient-subtle">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-primary text-white px-3 py-1.5 rounded-pill fw-semibold" style="font-size: 11px;">
                        <i class="bi bi-star-fill me-1"></i> Weather Spotlight
                    </span>
                    <small class="text-muted"><i class="bi bi-geo me-1"></i> Real-time Sensor</small>
                </div>

                @if(!empty($mapWeatherData[0]))
                @php $spot = $mapWeatherData[0]; @endphp
                <div class="text-center py-3">
                    <div class="display-3 mb-2">{{ $spot['flag'] }}</div>
                    <h3 class="fw-bold text-dark mb-1" id="spotlightName">{{ $spot['name'] }}</h3>
                    <div class="mb-3" id="spotlightCondition">
                        <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill fs-6">
                            {{ $spot['icon'] }} {{ $spot['condition'] }}
                        </span>
                    </div>

                    <div class="my-4">
                        <span class="display-4 fw-bold text-primary" id="spotlightTemp">{{ $spot['temperature'] }}°C</span>
                        <div class="small text-muted mt-1" id="spotlightRisk">
                            Logistics Risk: <strong class="text-dark">{{ $spot['risk'] }}</strong>
                        </div>
                    </div>
                </div>

                <div class="border-top pt-3 mt-2">
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="p-2 rounded-3 bg-light border">
                                <small class="text-muted d-block">Wind Speed</small>
                                <strong class="text-dark fs-6" id="spotlightWind">{{ $spot['wind_speed'] }} km/h</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded-3 bg-light border">
                                <small class="text-muted d-block">Humidity</small>
                                <strong class="text-dark fs-6" id="spotlightHumidity">{{ $spot['humidity'] }}%</strong>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="mt-4 text-center">
                <small class="text-muted">Click any marker or table row to focus location.</small>
            </div>
        </div>
    </div>
</div>

<!-- Weather Directory Table -->
<div class="detail-card p-4 border-0 shadow-sm rounded-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="bi bi-table text-primary me-2"></i> Weather Intelligence Directory</h4>
            <p class="text-muted small mb-0">Showing {{ $countries->firstItem() ?? 0 }} to {{ $countries->lastItem() ?? 0 }} of {{ number_format($countries->total()) }} countries</p>
        </div>

        <form method="GET" action="{{ route('weather.index') }}" class="d-flex gap-2" style="max-width: 400px;">
            <div class="position-relative flex-grow-1">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search country weather..." class="form-control ps-5 py-2 rounded-pill border">
            </div>
            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2">Search</button>
        </form>
    </div>

    <!-- Weather Grid / Table Cards -->
    <div class="row g-3 mb-3">
        @forelse($weatherData as $w)
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="card h-100 border shadow-xs rounded-4 p-3 hover-lift" 
                 style="cursor: pointer;"
                 onclick="focusWeatherMap({{ $w['latitude'] }}, {{ $w['longitude'] }}, '{{ addslashes($w['name']) }}', '{{ $w['flag'] }}', '{{ $w['icon'] }}', '{{ addslashes($w['condition']) }}', {{ $w['temperature'] }}, {{ $w['wind_speed'] }}, {{ $w['humidity'] }}, '{{ $w['risk'] }}')">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="fs-4 me-1">{{ $w['flag'] }}</span>
                        <strong class="text-dark">{{ $w['name'] }}</strong>
                    </div>
                    <span class="badge {{ $w['badge_class'] }}">{{ $w['icon'] }} {{ $w['condition'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-end mt-3 pt-2 border-top">
                    <div>
                        <span class="display-6 fw-bold text-primary" style="font-size: 1.5rem;">{{ $w['temperature'] }}°C</span>
                        <small class="text-muted d-block" style="font-size: 11px;">Wind: {{ $w['wind_speed'] }} km/h</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-light text-secondary border mb-1" style="font-size: 10px;">{{ $w['risk'] }}</span>
                        <a href="{{ route('countries.show', $w['id']) }}" class="d-block small text-primary text-decoration-none fw-semibold">Profile &rarr;</a>
                    </div>
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
    const map = L.map('weatherMap').setView([20, 10], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    setTimeout(() => map.invalidateSize(), 200);

    const mapWeatherData = @json($mapWeatherData);
    const markers = [];

    mapWeatherData.forEach(function (data) {
        if (data.latitude && data.longitude) {
            let markerColor = '#10b981'; // green optimal
            if (data.condition.includes('Rain') || data.condition.includes('Showers') || data.condition.includes('Drizzle') || data.condition.includes('Fog')) {
                markerColor = '#f59e0b'; // yellow rain/fog
            } else if (data.condition.includes('Thunderstorm') || data.condition.includes('Snow') || data.condition.includes('Severe')) {
                markerColor = '#ef4444'; // red severe
            }

            let customIcon = L.divIcon({
                className: 'custom-weather-marker',
                html: `<div style="background:${markerColor}; width:16px; height:16px; border-radius:50%; border:2px solid #ffffff; box-shadow:0 2px 6px rgba(0,0,0,0.35);"></div>`,
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            });

            let marker = L.marker([data.latitude, data.longitude], { icon: customIcon }).addTo(map)
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
            map.setView([lat, lng], 5);
            markers.forEach(function (m) {
                if (Math.abs(m.lat - lat) < 0.01 && Math.abs(m.lng - lng) < 0.01) {
                    m.marker.openPopup();
                }
            });

            // Update Spotlight Card
            if (name) {
                document.getElementById('spotlightName').textContent = name;
                document.getElementById('spotlightTemp').textContent = temp + '°C';
                document.getElementById('spotlightCondition').innerHTML = '<span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill fs-6">' + icon + ' ' + condition + '</span>';
                document.getElementById('spotlightWind').textContent = wind + ' km/h';
                document.getElementById('spotlightHumidity').textContent = humidity + '%';
            }
        }
    };
});
</script>
@endsection
