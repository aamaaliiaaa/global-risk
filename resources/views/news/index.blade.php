@extends('layouts.app')

@section('title', 'News Intelligence')

@section('content')

<div class="page-header">
    <div>
        <h1 class="dashboard-title">News Intelligence</h1>
        <p class="page-subtitle">
            Logistics, trade, and economic news analyzed using lexicon-based sentiment models.
        </p>
    </div>
</div>

<div class="row g-4">
    <!-- Aggregated Sentiment Stats -->
    <div class="col-md-4">
        <div class="dashboard-card h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="section-title">
                    <i class="bi bi-pie-chart-fill text-primary"></i> Sentiment Overview
                </div>
                <div class="d-flex justify-content-around text-center my-4">
                    <div>
                        <h3 class="text-success">{{ $positive }}</h3>
                        <small class="text-muted">Positive</small>
                    </div>
                    <div>
                        <h3 class="text-warning">{{ $neutral }}</h3>
                        <small class="text-muted">Neutral</small>
                    </div>
                    <div>
                        <h3 class="text-danger">{{ $negative }}</h3>
                        <small class="text-muted">Negative</small>
                    </div>
                </div>
            </div>
            <div style="height: 250px;">
                <canvas id="sentimentAggregateChart"></canvas>
            </div>
        </div>
    </div>

    <!-- News List with Sentiment Breakdown -->
    <div class="col-md-8">
        <div class="dashboard-card h-100">
            <div class="section-title mb-4">
                <i class="bi bi-newspaper"></i> Evaluated News (Total: {{ $total }})
            </div>
            <div class="news-list" style="max-height: 480px; overflow-y: auto; padding-right: 5px;">
                @forelse($newsList as $news)
                <div class="news-item pb-4 mb-4 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="fs-6 me-2">{{ $news->country->flag }}</span>
                            <span class="fw-semibold text-secondary">{{ $news->country->name }}</span>
                            <span class="text-black-50 mx-2">|</span>
                            <span class="small text-muted">{{ $news->source }}</span>
                        </div>
                        <span class="risk {{ strtolower($news->sentiment) }}">{{ $news->sentiment }}</span>
                    </div>
                    
                    <h5 class="mb-2"><a href="{{ $news->url }}" target="_blank" class="text-decoration-none text-dark fw-bold">{{ $news->title }}</a></h5>
                    <p class="text-secondary small mb-3">{{ $news->description }}</p>

                    <!-- Sentiment Breakdown Details -->
                    <div class="bg-light p-2 rounded d-flex justify-content-between align-items-center small">
                        <span><strong>Lexicon Match:</strong></span>
                        <span>
                            🟢 Positive words: <span class="badge bg-success">{{ $news->positive_count }}</span>
                            🔴 Negative words: <span class="badge bg-danger">{{ $news->negative_count }}</span>
                        </span>
                        <span class="text-muted small">
                            <i class="bi bi-clock"></i> {{ $news->published_at ? $news->published_at->diffForHumans() : 'Recently' }}
                        </span>
                    </div>
                </div>
                @empty
                <p>No news logs available. Make sure countries are configured properly.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const sentimentCtx = document.getElementById('sentimentAggregateChart');
    new Chart(sentimentCtx, {
        type: 'doughnut',
        data: {
            labels: ['Positive', 'Neutral', 'Negative'],
            datasets: [{
                data: [{{ $positive }}, {{ $neutral }}, {{ $negative }}],
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
