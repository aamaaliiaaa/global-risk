@extends('layouts.app')

@section('title','Dashboard')

@section('content')

<h1 class="dashboard-title">
    Dashboard
</h1>

<div class="stats-grid">

    <!-- Total Countries -->
    <div class="stat-card primary">
        <div class="card-top">
            <span class="card-title">Total Countries</span>
            <div class="stat-icon bg-primary">
                <i class="bi bi-globe2"></i>
            </div>
        </div>
        <h2>{{ $totalCountries }}</h2>
        <p class="card-subtitle text-success">
            <i class="bi bi-arrow-up-short"></i> Active
        </p>
    </div>

    <!-- High Risk -->
    <div class="stat-card danger">
        <div class="card-top">
            <span class="card-title">High Risk</span>
            <div class="stat-icon bg-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
        </div>
        <h2>{{ $highRisk }}</h2>
        <p class="card-subtitle text-danger">Need Attention</p>
    </div>

    <!-- Medium Risk -->
    <div class="stat-card warning">
        <div class="card-top">
            <span class="card-title">Medium Risk</span>
            <div class="stat-icon bg-warning">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
        </div>
        <h2>{{ $mediumRisk }}</h2>
        <p class="card-subtitle text-warning">Stable</p>
    </div>

    <!-- Low Risk -->
    <div class="stat-card success">
        <div class="card-top">
            <span class="card-title">Low Risk</span>
            <div class="stat-icon bg-success">
                <i class="bi bi-shield-check"></i>
            </div>
        </div>
        <h2>{{ $lowRisk }}</h2>
        <p class="card-subtitle text-success">Safe</p>
    </div>

    <!-- News -->
    <div class="stat-card info">
        <div class="card-top">
            <span class="card-title">Today's News</span>
            <div class="stat-icon bg-info">
                <i class="bi bi-newspaper"></i>
            </div>
        </div>
        <h2>{{ $totalNewsToday }}</h2>
        <p class="card-subtitle text-info">Updated</p>
    </div>

</div>

<div class="dashboard-grid">

    <!-- Heatmap -->
    <div class="dashboard-card">
        <div class="section-title">
            <i class="bi bi-globe-americas"></i>
            Global Risk Heatmap
        </div>
        <div id="worldMap"></div>
    </div>

    <!-- Risk Trend -->
    <div class="dashboard-card">
        <div class="section-title">
            <i class="bi bi-graph-up-arrow"></i>
            Average Risk Trend
        </div>
        <div class="trend-card">
            <canvas id="riskChart"></canvas>
        </div>
    </div>

    <!-- News -->
    <div class="dashboard-card">
        <div class="section-title">
            <i class="bi bi-newspaper"></i>
            Latest News <small class="text-muted fs-6 font-normal ms-2">(Click news to locate on map)</small>
        </div>
        <div class="news-list">
            @forelse($newsList as $news)
            <div class="news-item" style="cursor: pointer;" onclick="focusDashboardMap({{ $news->country->latitude ?? 0 }}, {{ $news->country->longitude ?? 0 }})" title="Click to locate on map">
                <div class="news-country text-primary">
                    {{ $news->country->flag }} {{ $news->country->name }}
                    <span class="risk {{ strtolower($news->sentiment) }}">{{ $news->sentiment }}</span>
                </div>
                <p><strong>{{ $news->title }}</strong></p>
                <p>{{ Str::limit($news->description, 100) }}</p>
                <small class="text-secondary">
                    <i class="bi bi-clock"></i> {{ $news->published_at ? $news->published_at->diffForHumans() : 'Recently' }}
                </small>
            </div>
            @empty
            <p>No news available.</p>
            @endforelse
        </div>
        <div class="view-all">
            <a href="{{ route('news.index') }}">
                View All <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Sentiment -->
    <div class="dashboard-card sentiment-card">
        <div class="section-title">
            <i class="bi bi-pie-chart"></i>
            News Sentiment Analysis
        </div>
        <canvas id="sentimentChart"></canvas>
    </div>

</div>

@endsection

@section('scripts')

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Leaflet World Map
    const map = L.map('worldMap').setView([15, 20], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Heatmap data
    const heatmapData = @json($heatmapData);
    const markers = [];

    heatmapData.forEach(function (data) {
        if (data.latitude && data.longitude) {
            let color = '#22C55E'; // Low Risk
            if (data.risk_level === 'High') {
                color = '#EF4444'; // High Risk
            } else if (data.risk_level === 'Medium') {
                color = '#F59E0B'; // Medium Risk
            }

            // Create a custom colored circle marker
            let marker = L.circleMarker([data.latitude, data.longitude], {
                radius: 10,
                fillColor: color,
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map)
              .bindPopup(`
                  <div style="font-family: 'Poppins', sans-serif;">
                      <strong>${data.flag} ${data.name}</strong><br>
                      Risk Level: <span style="color:${color}; font-weight:bold;">${data.risk_level} (${data.risk_score})</span><br>
                      Weather: ${data.weather}<br>
                      <a href="/countries/${data.name.toLowerCase()}" style="text-decoration:none; color:#2563EB; font-weight:600; font-size:12px;">Detail View &rarr;</a>
                  </div>
              `);
            markers.push({
                lat: data.latitude,
                lng: data.longitude,
                marker: marker
            });
        }
    });

    window.focusDashboardMap = function (lat, lng) {
        if (lat && lng) {
            map.setView([lat, lng], 5);
            markers.forEach(function (m) {
                // Approximate matching for float values
                if (Math.abs(m.lat - lat) < 0.01 && Math.abs(m.lng - lng) < 0.01) {
                    m.marker.openPopup();
                }
            });
        }
    };

    // Chart.js Risk Trend
    const riskCtx = document.getElementById('riskChart');
    new Chart(riskCtx, {
        type: 'line',
        data: {
            labels: @json($months),
            datasets: [{
                label: 'Global Avg Risk Score',
                data: @json($scores),
                borderColor: '#2563EB',
                backgroundColor: 'rgba(37,99,235,.15)',
                fill: true,
                tension: .4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // Chart.js News Sentiment Doughnut
    const sentimentCtx = document.getElementById('sentimentChart');
    new Chart(sentimentCtx, {
        type: 'doughnut',
        data: {
            labels: ['Positive', 'Neutral', 'Negative'],
            datasets: [{
                data: [{{ $positiveSentiment }}, {{ $neutralSentiment }}, {{ $negativeSentiment }}],
                backgroundColor: [
                    '#22C55E',
                    '#F59E0B',
                    '#EF4444'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            cutout: '70%'
        }
    });
});
</script>

@endsection