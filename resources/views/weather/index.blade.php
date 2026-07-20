@extends('layouts.app')

@section('title', 'Weather Monitoring')

@section('content')

<div class="page-header">
    <div>
        <h1 class="dashboard-title">Global Weather Monitoring</h1>
        <p class="page-subtitle">
            Real-time weather reports for monitored countries to identify logistics weather disruption risk.
        </p>
    </div>
</div>

<div class="row g-4">
    <!-- Weather Map -->
    <div class="col-lg-8">
        <div class="dashboard-card h-100">
            <div class="section-title">
                <i class="bi bi-cloud-sun-fill"></i> Interactive Weather Map
            </div>
            <div id="weatherMap" style="height: 480px; border-radius: 16px;"></div>
        </div>
    </div>

    <!-- Weather Conditions Side Card -->
    <div class="col-lg-4">
        <div class="dashboard-card h-100" style="display: flex; flex-direction: column;">
            <div class="section-title">
                <i class="bi bi-list-stars"></i> Current Reports <small class="text-muted fs-6 font-normal ms-1">(Click to locate)</small>
            </div>
            <div class="news-list" style="overflow-y: auto; flex-grow: 1; max-height: 440px; padding-right: 5px;">
                @foreach($weatherData as $w)
                <div class="news-item pb-3 mb-3 border-bottom d-flex justify-content-between align-items-center" style="cursor: pointer;" onclick="focusWeatherMap({{ $w['latitude'] }}, {{ $w['longitude'] }})" title="Click to locate on map">
                    <div>
                        <span class="fs-5">{{ $w['flag'] }}</span> 
                        <strong class="text-dark">{{ $w['name'] }}</strong>
                        <div class="text-secondary small">{{ $w['condition'] }}</div>
                    </div>
                    <div class="text-end">
                        <h4 class="mb-0 text-primary">{{ $w['temperature'] }}°C</h4>
                        <small class="text-muted"><i class="bi bi-wind"></i> {{ $w['wind_speed'] }} km/h</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const map = L.map('weatherMap').setView([15, 20], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const weatherData = @json($weatherData);
    const markers = [];

    weatherData.forEach(function (data) {
        if (data.latitude && data.longitude) {
            let marker = L.marker([data.latitude, data.longitude]).addTo(map)
                .bindPopup(`
                    <div style="font-family: 'Poppins', sans-serif;">
                        <strong>${data.flag} ${data.name}</strong><br>
                        Condition: <strong>${data.condition}</strong><br>
                        Temperature: ${data.temperature}°C<br>
                        Wind Speed: ${data.wind_speed} km/h
                    </div>
                `);
            markers.push({
                lat: data.latitude,
                lng: data.longitude,
                marker: marker
            });
        }
    });

    window.focusWeatherMap = function (lat, lng) {
        if (lat && lng) {
            map.setView([lat, lng], 5);
            markers.forEach(function (m) {
                if (Math.abs(m.lat - lat) < 0.01 && Math.abs(m.lng - lng) < 0.01) {
                    m.marker.openPopup();
                }
            });
        }
    };
});
</script>
@endsection
