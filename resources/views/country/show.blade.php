@extends('layouts.app')

@section('title', $country->name)

@section('content')

<!-- Header Section -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="display-5 me-1">{{ $country->flag }}</span>
            <h1 class="dashboard-title mb-0">{{ $country->name }}</h1>
        </div>
        <p class="page-subtitle mb-0">
            Country Intelligence Profile • Real-time Risk Assessment & Macroeconomic Indicators
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('countries.index') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-medium">
            <i class="bi bi-arrow-left me-1"></i> Back to Countries
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Left Column: Country Metrics Grid -->
    <div class="col-lg-6">
        <div class="detail-card h-100 p-4 border-0 shadow-sm rounded-4">
            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill text-primary"></i> General Information & Key Metrics
            </h5>

            <!-- Grid Sub-Cards -->
            <div class="row g-3">
                <!-- Risk Level Card -->
                <div class="col-12">
                    <div class="p-3 rounded-3 border bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 11px;">Overall Country Risk</small>
                            <h4 class="fw-bold mb-0 text-dark">
                                {{ $riskDetails['risk_level'] }} Risk
                                <span class="fs-6 text-muted font-monospace ms-1">({{ $riskDetails['total_risk'] }}/100)</span>
                            </h4>
                        </div>
                        <span class="badge {{ strtolower($riskDetails['risk_level']) === 'high' ? 'bg-danger-subtle text-danger' : (strtolower($riskDetails['risk_level']) === 'medium' ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success') }} px-3 py-2 rounded-pill fs-6 fw-bold">
                            {{ $riskDetails['risk_level'] }}
                        </span>
                    </div>
                </div>

                <!-- Weather Card -->
                <div class="col-12">
                    <div class="p-3 rounded-3 border bg-white shadow-xs">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted fw-bold text-uppercase" style="font-size: 11px;">Weather Condition</small>
                            <span class="badge {{ $weather['badge_class'] ?? 'bg-light text-dark border' }}">
                                {{ $weather['icon'] ?? '☀️' }} {{ $weather['condition'] }}
                            </span>
                        </div>
                        <div class="row g-2 text-center mt-1">
                            <div class="col-4">
                                <div class="p-2 rounded-3 bg-light border">
                                    <small class="text-muted d-block" style="font-size: 10px;">Temperature</small>
                                    <strong class="text-dark fs-6">{{ $weather['temperature'] }}°C</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded-3 bg-light border">
                                    <small class="text-muted d-block" style="font-size: 10px;">Wind Speed</small>
                                    <strong class="text-dark fs-6">{{ $weather['wind_speed'] }} km/h</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 rounded-3 bg-light border">
                                    <small class="text-muted d-block" style="font-size: 10px;">Humidity</small>
                                    <strong class="text-dark fs-6">{{ $weather['humidity'] ?? 60 }}%</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Currency & FX Card -->
                <div class="col-sm-6">
                    <div class="p-3 rounded-3 border bg-white shadow-xs h-100">
                        <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 11px;">Currency & Exchange</small>
                        <h5 class="fw-bold text-dark mb-1">{{ strtoupper($country->currency) }}</h5>
                        <small class="text-secondary d-block">1 USD = {{ number_format($rate ?? 1.0, 2) }} {{ strtoupper($country->currency) }}</small>
                    </div>
                </div>

                <!-- Population & GDP Card -->
                <div class="col-sm-6">
                    <div class="p-3 rounded-3 border bg-white shadow-xs h-100">
                        <small class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 11px;">Macro Indicators</small>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">Population:</span>
                            <strong class="text-dark small">{{ $population ? number_format($population) : 'N/A' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Latest GDP:</span>
                            <strong class="text-dark small">{{ $gdp ? '$' . number_format($gdp) : 'N/A' }}</strong>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Right Column: Risk Score Composition & Trend Chart -->
    <div class="col-lg-6">
        <div class="detail-card h-100 p-4 border-0 shadow-sm rounded-4 d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-shield-shaded text-primary"></i> Risk Score Composition
                </h5>

                <div class="mb-4">
                    <!-- Weather Risk -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-medium text-secondary">Weather Risk (20%)</span>
                            <strong class="small text-dark">{{ $riskDetails['weather_risk'] }}/100</strong>
                        </div>
                        <div class="progress rounded-pill" style="height: 7px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $riskDetails['weather_risk'] }}%"></div>
                        </div>
                    </div>

                    <!-- Inflation Risk -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-medium text-secondary">Inflation Risk (30%)</span>
                            <strong class="small text-dark">{{ $riskDetails['inflation_risk'] }}/100</strong>
                        </div>
                        <div class="progress rounded-pill" style="height: 7px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $riskDetails['inflation_risk'] }}%"></div>
                        </div>
                    </div>

                    <!-- Currency Risk -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-medium text-secondary">Currency Volatility Risk (20%)</span>
                            <strong class="small text-dark">{{ $riskDetails['currency_risk'] }}/100</strong>
                        </div>
                        <div class="progress rounded-pill" style="height: 7px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $riskDetails['currency_risk'] }}%"></div>
                        </div>
                    </div>

                    <!-- Sentiment Risk -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-medium text-secondary">News Sentiment Risk (30%)</span>
                            <strong class="small text-dark">{{ $riskDetails['news_sentiment_risk'] }}/100</strong>
                        </div>
                        <div class="progress rounded-pill" style="height: 7px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $riskDetails['news_sentiment_risk'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h6 class="fw-bold text-dark mb-2">Risk Score Historical Trend</h6>
                <div style="height: 160px;">
                    <canvas id="riskChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Macroeconomic Indicators History Table -->
<div class="detail-card p-4 border-0 shadow-sm rounded-4 mb-4">
    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-graph-up text-success me-2"></i> Macroeconomic History (World Bank Indicators)</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle text-center mb-0">
            <thead>
                <tr>
                    <th>Year</th>
                    <th>GDP (USD)</th>
                    <th>Inflation Rate</th>
                    <th>Population</th>
                    <th>Exports (USD)</th>
                    <th>Imports (USD)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($indicators as $ind)
                <tr>
                    <td><strong class="text-dark">{{ $ind['year'] }}</strong></td>
                    <td>${{ number_format($ind['gdp'] ?? 0) }}</td>
                    <td>
                        <span class="badge {{ ($ind['inflation'] ?? 0) > 5 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                            {{ number_format($ind['inflation'] ?? 0, 2) }}%
                        </span>
                    </td>
                    <td>{{ number_format($ind['population'] ?? 0) }}</td>
                    <td>${{ number_format($ind['exports'] ?? 0) }}</td>
                    <td>${{ number_format($ind['imports'] ?? 0) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-muted py-4">No World Bank indicator data available for this country.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Location Map & News Section -->
<div class="row g-4">
    <!-- Location Map -->
    <div class="col-lg-7">
        <div class="detail-card h-100 p-4 border-0 shadow-sm rounded-4">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-geo-alt-fill text-primary me-2"></i> Geographic Location</h5>
            <div id="countryMap" style="height: 380px; border-radius: 14px; overflow: hidden; background-color: #f8fafc;" class="border shadow-xs">
                <iframe 
                    width="100%" 
                    height="380" 
                    style="border:0; border-radius: 14px; width:100%; height:380px;" 
                    loading="lazy" 
                    allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade"
                    src="https://maps.google.com/maps?q={{ urlencode($country->name) }}&t=&z=5&ie=UTF8&iwloc=&output=embed">
                </iframe>
            </div>
        </div>
    </div>

    <!-- News List -->
    <div class="col-lg-5">
        <div class="detail-card h-100 p-4 border-0 shadow-sm rounded-4">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-newspaper text-warning me-2"></i> Latest Logistical News</h5>
            <div class="news-list" style="max-height: 380px; overflow-y: auto; padding-right: 5px;">
                @forelse($news as $article)
                <div class="news-item pb-3 mb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-secondary small fw-semibold">{{ $article['source'] }}</span>
                        <span class="badge {{ strtolower($article['sentiment']) === 'negative' ? 'bg-danger-subtle text-danger' : (strtolower($article['sentiment']) === 'positive' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary') }}">
                            {{ $article['sentiment'] }}
                        </span>
                    </div>
                    <h6 class="mb-1">
                        <a href="{{ $article['url'] }}" target="_blank" rel="noopener noreferrer" class="text-dark text-decoration-none fw-semibold fs-6">
                            {{ $article['title'] }}
                        </a>
                    </h6>
                    <p class="text-muted small mb-2">{{ Str::limit($article['description'], 110) }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted"><i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::parse($article['published_at'])->format('d M Y') }}</small>
                        @if(isset($article['url']) && $article['url'] !== '#')
                        <a href="{{ $article['url'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill py-0.5 px-2.5" style="font-size: 11px;">
                            Read <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-muted small py-4 text-center">No logistical news available for this country.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('riskChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($historyMonths),
                datasets: [{
                    label: 'Risk Score',
                    data: @json($historyScores),
                    borderColor: '#2563eb',
                    borderWidth: 3,
                    backgroundColor: 'rgba(37,99,235,.12)',
                    fill: true,
                    tension: .4,
                    pointBackgroundColor: '#2563eb'
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
                        max: 100,
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
</script>

@endsection