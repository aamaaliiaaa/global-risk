@extends('layouts.app')

@section('title', 'Global Intelligence Dashboard')

@section('content')

<!-- Header Section -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h1 class="dashboard-title mb-1">
            <i class="bi bi-shield-check text-primary me-2"></i>Global Risk Intelligence Command Center
        </h1>
        <p class="page-subtitle mb-0">
            Real-time risk scoring, maritime logistics monitoring, market news sentiment, and exchange volatility.
        </p>
    </div>
    <div>
        <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
            <i class="bi bi-broadcast text-success me-1"></i> System Status: Live & Operational
        </span>
    </div>
</div>

<!-- KPI Metric Cards Grid -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="detail-card h-100 p-3 shadow-sm border-0 rounded-4">
            <div class="d-flex align-items-center mb-2">
                <div class="rounded-3 bg-primary-subtle text-primary p-2 me-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-globe2 fs-5"></i>
                </div>
                <span class="text-secondary small fw-medium">Countries</span>
            </div>
            <h3 class="mb-0 fw-bold text-dark">{{ number_format($totalCountries) }}</h3>
            <small class="text-success" style="font-size: 11px;"><i class="bi bi-check-circle-fill me-1"></i> Monitored</small>
        </div>
    </div>
    
    <div class="col-6 col-md-4 col-xl-2">
        <div class="detail-card h-100 p-3 shadow-sm border-0 rounded-4">
            <div class="d-flex align-items-center mb-2">
                <div class="rounded-3 bg-danger-subtle text-danger p-2 me-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                </div>
                <span class="text-secondary small fw-medium">High Risk</span>
            </div>
            <h3 class="mb-0 fw-bold text-danger">{{ number_format($highRisk) }}</h3>
            <small class="text-danger" style="font-size: 11px;"><i class="bi bi-shield-exclamation me-1"></i> Action Needed</small>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="detail-card h-100 p-3 shadow-sm border-0 rounded-4">
            <div class="d-flex align-items-center mb-2">
                <div class="rounded-3 bg-warning-subtle text-warning p-2 me-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-graph-up-arrow fs-5"></i>
                </div>
                <span class="text-secondary small fw-medium">Medium Risk</span>
            </div>
            <h3 class="mb-0 fw-bold text-warning">{{ number_format($mediumRisk) }}</h3>
            <small class="text-warning" style="font-size: 11px;"><i class="bi bi-eye-fill me-1"></i> Watchlist</small>
        </div>
    </div>

    <div class="col-6 col-md-4 col-xl-2">
        <div class="detail-card h-100 p-3 shadow-sm border-0 rounded-4">
            <div class="d-flex align-items-center mb-2">
                <div class="rounded-3 bg-success-subtle text-success p-2 me-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-shield-check fs-5"></i>
                </div>
                <span class="text-secondary small fw-medium">Low Risk</span>
            </div>
            <h3 class="mb-0 fw-bold text-success">{{ number_format($lowRisk) }}</h3>
            <small class="text-success" style="font-size: 11px;"><i class="bi bi-check2-all me-1"></i> Stable</small>
        </div>
    </div>

    <div class="col-12 col-md-8 col-xl-4">
        <div class="detail-card h-100 p-3 shadow-sm border-0 rounded-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="rounded-3 bg-info-subtle text-info p-3 me-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-newspaper fs-4"></i>
                </div>
                <div>
                    <span class="text-secondary small fw-medium text-uppercase tracking-wider">Evaluated News Today</span>
                    <h3 class="mb-0 fw-bold text-dark">{{ number_format($totalNewsToday) }}</h3>
                </div>
            </div>
            <a href="{{ route('news.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View News &rarr;</a>
        </div>
    </div>
</div>

<!-- Interactive Heatmap Section -->
<div class="detail-card p-4 border-0 shadow-sm rounded-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-globe-americas text-primary me-2"></i> Global Risk Heatmap & Coordinates</h4>
            <p class="text-muted small mb-0">Interactive world map displaying real-time risk scores for monitored countries.</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> High Risk</span>
            <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> Medium Risk</span>
            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> Low Risk</span>
        </div>
    </div>
    <div id="worldMap" style="height: 480px; border-radius: 16px; overflow: hidden; background-color: #f8fafc;" class="shadow-xs"></div>
</div>

<!-- Secondary Analytics Section -->
<div class="row g-4 mb-4">
    <!-- Left Column: Risk Trend & News Stream -->
    <div class="col-lg-7">
        <!-- 6-Month Risk Trend -->
        <div class="detail-card p-4 border-0 shadow-sm rounded-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-graph-up-arrow text-primary me-2"></i> 6-Month Average Risk Trend</h5>
                <span class="badge bg-light text-secondary border">Global Average</span>
            </div>
            <div style="height: 220px;">
                <canvas id="riskChart"></canvas>
            </div>
        </div>

        <!-- Latest News Stream -->
        <div class="detail-card p-4 border-0 shadow-sm rounded-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-newspaper text-primary me-2"></i> Latest Trade News Stream</h5>
                <a href="{{ route('news.index') }}" class="btn btn-sm btn-link text-decoration-none">View All News &rarr;</a>
            </div>

            <div class="d-flex flex-column gap-3" style="max-height: 360px; overflow-y: auto; padding-right: 5px;">
                @forelse($newsList as $news)
                <div class="p-3 border rounded-3 bg-white shadow-xs hover-card transition-all d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="fs-6">{{ $news->country->flag ?? '🌐' }}</span>
                            <span class="fw-bold text-dark small">{{ $news->country->name ?? 'Global' }}</span>
                            <span class="text-muted">•</span>
                            <span class="small text-primary fw-semibold">{{ $news->source }}</span>
                            <span class="badge {{ $news->sentiment == 'Positive' ? 'bg-success-subtle text-success border border-success-subtle' : ($news->sentiment == 'Negative' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle') }}" style="font-size: 10px;">
                                {{ $news->sentiment }}
                            </span>
                        </div>
                        <h6 class="fw-bold mb-1" style="font-size: 14px;">
                            <a href="{{ $news->url }}" target="_blank" rel="noopener noreferrer" class="text-dark text-decoration-none hover-primary-link">
                                {{ $news->title }}
                            </a>
                        </h6>
                        <p class="text-muted small mb-0 line-clamp-2" style="font-size: 12px;">{{ Str::limit($news->description, 110) }}</p>
                    </div>

                    @if(isset($news->country->latitude) && isset($news->country->longitude))
                    <button type="button" onclick="focusDashboardMap({{ $news->country->latitude }}, {{ $news->country->longitude }})" class="btn btn-xs btn-outline-primary rounded-circle p-2 flex-shrink-0" style="width: 34px; height: 34px;" title="Focus country on map">
                        <i class="bi bi-geo-alt-fill"></i>
                    </button>
                    @endif
                </div>
                @empty
                <p class="text-muted text-center py-4">No recent news available.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: Sentiment Analysis & Shortcuts -->
    <div class="col-lg-5">
        <!-- Sentiment Doughnut Card -->
        <div class="detail-card p-4 border-0 shadow-sm rounded-4 mb-4">
            <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-pie-chart-fill text-primary me-2"></i> News Sentiment Ratio</h5>
            <div style="height: 240px;" class="position-relative">
                <canvas id="sentimentChart"></canvas>
            </div>
            <div class="text-center mt-3 pt-3 border-top">
                <small class="text-muted"><i class="bi bi-shield-check text-success me-1"></i> Sentiment analyzed using domain lexicon keyword weighting.</small>
            </div>
        </div>

        <!-- Quick Navigation Panel -->
        <div class="detail-card p-4 border-0 shadow-sm rounded-4">
            <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-compass-fill text-primary me-2"></i> Quick Module Shortcuts</h5>
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('countries.index') }}" class="btn btn-light border text-start rounded-3 p-3 d-flex align-items-center justify-content-between hover-card">
                    <div class="d-flex align-items-center">
                        <div class="rounded-3 bg-primary-subtle text-primary p-2 me-3">
                            <i class="bi bi-globe fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Countries Directory</h6>
                            <small class="text-muted">Explore profiles, GDP & risk scores</small>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>

                <a href="{{ route('weather.index') }}" class="btn btn-light border text-start rounded-3 p-3 d-flex align-items-center justify-content-between hover-card">
                    <div class="d-flex align-items-center">
                        <div class="rounded-3 bg-info-subtle text-info p-2 me-3">
                            <i class="bi bi-cloud-sun fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Weather Monitoring</h6>
                            <small class="text-muted">Live weather & adverse condition alerts</small>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>

                <a href="{{ route('currency.index') }}" class="btn btn-light border text-start rounded-3 p-3 d-flex align-items-center justify-content-between hover-card">
                    <div class="d-flex align-items-center">
                        <div class="rounded-3 bg-success-subtle text-success p-2 me-3">
                            <i class="bi bi-currency-exchange fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Currency Volatility</h6>
                            <small class="text-muted">6-Month historical FX trends & converter</small>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>

                <a href="{{ route('ports.index') }}" class="btn btn-light border text-start rounded-3 p-3 d-flex align-items-center justify-content-between hover-card">
                    <div class="d-flex align-items-center">
                        <div class="rounded-3 bg-warning-subtle text-warning p-2 me-3">
                            <i class="bi bi-anchor fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Ports Intelligence</h6>
                            <small class="text-muted">Global port congestion & status</small>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Leaflet World Map
    const map = L.map('worldMap').setView([15, 20], 2);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);

    setTimeout(() => map.invalidateSize(), 200);

    const heatmapData = @json($heatmapData);
    const markers = [];

    heatmapData.forEach(function (data) {
        if (data.latitude && data.longitude) {
            let color = '#10B981'; // Low Risk
            if (data.risk_level === 'High') {
                color = '#EF4444'; // High Risk
            } else if (data.risk_level === 'Medium') {
                color = '#F59E0B'; // Medium Risk
            }

            let marker = L.circleMarker([data.latitude, data.longitude], {
                radius: 9,
                fillColor: color,
                color: '#ffffff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.85
            }).addTo(map)
              .bindPopup(`
                  <div style="font-family: 'Poppins', sans-serif; min-width: 170px;">
                      <h6 class="mb-1 fw-bold">${data.flag} ${data.name}</h6>
                      <div class="mb-2"><span class="badge" style="background-color:${color}; color:#fff;">${data.risk_level} Risk (${data.risk_score}/100)</span></div>
                      <div class="small mb-2">🌤️ Weather: <strong>${data.weather}</strong></div>
                      <div class="text-end">
                          <a href="/countries/${data.id || data.name.toLowerCase()}" class="btn btn-primary btn-sm py-1 px-3 text-white text-decoration-none rounded-pill" style="font-size:11px;">View Profile &rarr;</a>
                      </div>
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
            map.setView([lat, lng], 6);
            markers.forEach(function (m) {
                if (Math.abs(m.lat - lat) < 0.01 && Math.abs(m.lng - lng) < 0.01) {
                    m.marker.openPopup();
                }
            });
        }
    };

    // Chart.js Risk Trend
    const riskCtx = document.getElementById('riskChart');
    if (riskCtx) {
        new Chart(riskCtx, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [{
                    label: 'Global Avg Risk Index',
                    data: @json($scores),
                    borderColor: '#2563EB',
                    backgroundColor: 'rgba(37,99,235,.12)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { font: { size: 10 } }
                    },
                    x: {
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    }

    // Chart.js News Sentiment Doughnut
    const sentimentCtx = document.getElementById('sentimentChart');
    if (sentimentCtx) {
        new Chart(sentimentCtx, {
            type: 'doughnut',
            data: {
                labels: ['Positive', 'Neutral', 'Negative'],
                datasets: [{
                    data: [{{ $positiveSentiment }}, {{ $neutralSentiment }}, {{ $negativeSentiment }}],
                    backgroundColor: [
                        '#10B981',
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
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    }
                },
                cutout: '72%'
            }
        });
    }
});
</script>
@endsection