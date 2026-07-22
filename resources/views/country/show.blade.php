@extends('layouts.app')

@section('title', $country->name)

@section('content')

<div class="page-header">
    <div>
        <h1 class="dashboard-title">
            {{ $country->flag }} {{ $country->name }}
        </h1>
        <p class="page-subtitle">
            Country profile, real-time risk scores, weather, exchange rates, and macroeconomic indicators.
        </p>
    </div>
    <div>
        <a href="{{ route('countries.index') }}" class="btn btn-secondary rounded-3 px-4 py-2">
            <i class="bi bi-arrow-left"></i> Back to Countries
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Country Information Card -->
    <div class="col-md-6">
        <div class="detail-card h-100">
            <h4>General Information</h4>
            <div class="detail-item">
                <strong>Risk Level</strong>
                <span class="badge-risk {{ strtolower($riskDetails['risk_level']) }}">{{ $riskDetails['risk_level'] }} (Score: {{ $riskDetails['total_risk'] }}/100)</span>
            </div>
            <div class="detail-item">
                <strong>Weather Condition</strong>
                <span>
                    <span class="badge {{ $weather['badge_class'] ?? 'bg-light text-dark' }} px-2 py-1 mb-1">
                        {{ $weather['icon'] ?? '☀️' }} {{ $weather['condition'] }}
                    </span><br>
                    🌡️ Temperature: <strong>{{ $weather['temperature'] }}°C</strong><br>
                    💨 Wind Speed: <strong>{{ $weather['wind_speed'] }} km/h</strong><br>
                    💧 Humidity: <strong>{{ $weather['humidity'] ?? 60 }}%</strong>
                </span>
            </div>
            <div class="detail-item">
                <strong>Currency</strong>
                <span>{{ strtoupper($country->currency) }}</span>
            </div>
            <div class="detail-item">
                <strong>Exchange Rate (to USD)</strong>
                <span>1 USD = {{ number_format($rate ?? 1.0, 2) }} {{ strtoupper($country->currency) }}</span>
            </div>
            <div class="detail-item">
                <strong>Latest Population</strong>
                <span>{{ $population ? number_format($population) : 'N/A' }}</span>
            </div>
            <div class="detail-item">
                <strong>Latest GDP (USD)</strong>
                <span>{{ $gdp ? '$' . number_format($gdp) : 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Risk Score Details & Chart -->
    <div class="col-md-6">
        <div class="detail-card h-100">
            <h4>Risk Score Composition</h4>
            <div class="mb-4">
                <div class="mb-2">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Weather Risk (20%)</span>
                        <strong>{{ $riskDetails['weather_risk'] }}/100</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $riskDetails['weather_risk'] }}%"></div>
                    </div>
                </div>

                <div class="mb-2">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Inflation Risk (30%)</span>
                        <strong>{{ $riskDetails['inflation_risk'] }}/100</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $riskDetails['inflation_risk'] }}%"></div>
                    </div>
                </div>

                <div class="mb-2">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Currency Risk (20%)</span>
                        <strong>{{ $riskDetails['currency_risk'] }}/100</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $riskDetails['currency_risk'] }}%"></div>
                    </div>
                </div>

                <div class="mb-2">
                    <div class="d-flex justify-content-between mb-1">
                        <span>News Sentiment Risk (30%)</span>
                        <strong>{{ $riskDetails['news_sentiment_risk'] }}/100</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $riskDetails['news_sentiment_risk'] }}%"></div>
                    </div>
                </div>
            </div>

            <h5 class="mt-3">Risk Trend History</h5>
            <div style="height: 180px;">
                <canvas id="riskChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Macroeconomic Indicators World Bank Chart -->
    <div class="col-md-12">
        <div class="detail-card">
            <h4>Macroeconomic History (World Bank Data)</h4>
            <div class="table-responsive">
                <table class="table table-hover mt-3 align-middle text-center">
                    <thead class="table-light">
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
                            <td><strong>{{ $ind['year'] }}</strong></td>
                            <td>${{ number_format($ind['gdp'] ?? 0) }}</td>
                            <td>{{ number_format($ind['inflation'] ?? 0, 2) }}%</td>
                            <td>{{ number_format($ind['population'] ?? 0) }}</td>
                            <td>${{ number_format($ind['exports'] ?? 0) }}</td>
                            <td>${{ number_format($ind['imports'] ?? 0) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">No World Bank indicator data available.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <!-- Location Map -->
    <div class="col-md-7">
        <div class="detail-card h-100">
            <h4><i class="bi bi-geo-alt-fill text-primary"></i> Geographic Location</h4>
            <div id="countryMap" class="mt-3" style="height: 380px; border-radius: 12px; overflow: hidden; background-color: #f8fafc;">
                <iframe 
                    width="100%" 
                    height="380" 
                    style="border:0; border-radius: 12px; width:100%; height:380px;" 
                    loading="lazy" 
                    allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade"
                    src="https://maps.google.com/maps?q={{ urlencode($country->name) }}&t=&z=5&ie=UTF8&iwloc=&output=embed">
                </iframe>
            </div>
        </div>
    </div>

    <!-- News List -->
    <div class="col-md-5">
        <div class="detail-card h-100">
            <h4>Latest Logistical News</h4>
            <div class="news-list mt-3" style="max-height: 380px; overflow-y: auto; padding-right: 5px;">
                @forelse($news as $article)
                <div class="news-item pb-3 mb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-secondary small fw-semibold">{{ $article['source'] }}</span>
                        <span class="risk {{ strtolower($article['sentiment']) }}">{{ $article['sentiment'] }}</span>
                    </div>
                    <h6 class="mb-1">
                        <a href="{{ $article['url'] }}" target="_blank" rel="noopener noreferrer" class="text-dark text-decoration-none fw-semibold">
                            {{ $article['title'] }}
                        </a>
                    </h6>
                    <p class="text-muted small mb-2">{{ Str::limit($article['description'], 120) }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-black-50"><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($article['published_at'])->format('d M Y') }}</small>
                        @if(isset($article['url']) && $article['url'] !== '#')
                        <a href="{{ $article['url'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-xs btn-outline-primary rounded-pill py-0 px-2" style="font-size: 11px;">
                            Read Original <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-muted small py-3">No logistical news available for this country.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Risk Trend Line Chart
    const ctx = document.getElementById('riskChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($historyMonths),
            datasets: [{
                label: 'Risk Score',
                data: @json($historyScores),
                borderColor: '#2563eb',
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
});
</script>

@endsection