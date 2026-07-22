@extends('layouts.app')

@section('title', 'News & Sentiment Intelligence')

@section('content')

<!-- Header Section -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h1 class="dashboard-title mb-1">
            <i class="bi bi-newspaper text-primary me-2"></i>News & Market Sentiment Intelligence
        </h1>
        <p class="page-subtitle mb-0">
            Real-time trade, logistics, and economic headlines analyzed using AI lexicon sentiment models.
        </p>
    </div>
    <div>
        <a href="{{ route('news.index', ['refresh' => 1]) }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
            <i class="bi bi-arrow-clockwise me-1"></i> Sync Live News
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="detail-card h-100 p-3 shadow-sm border-0 rounded-4">
            <div class="d-flex align-items-center">
                <div class="rounded-3 bg-primary-subtle text-primary p-3 me-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-journal-text fs-4"></i>
                </div>
                <div>
                    <span class="text-secondary small fw-medium text-uppercase tracking-wider">Total Evaluated</span>
                    <h3 class="mb-0 fw-bold mt-1 text-dark">{{ number_format($total) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="detail-card h-100 p-3 shadow-sm border-0 rounded-4">
            <div class="d-flex align-items-center">
                <div class="rounded-3 bg-success-subtle text-success p-3 me-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-hand-thumbs-up-fill fs-4"></i>
                </div>
                <div>
                    <span class="text-secondary small fw-medium text-uppercase tracking-wider">Positive Tone</span>
                    <h3 class="mb-0 fw-bold mt-1 text-success">{{ number_format($positive) }} <small class="fs-6 text-muted">({{ $total > 0 ? round(($positive/$total)*100) : 0 }}%)</small></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="detail-card h-100 p-3 shadow-sm border-0 rounded-4">
            <div class="d-flex align-items-center">
                <div class="rounded-3 bg-warning-subtle text-warning p-3 me-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-dash-circle-fill fs-4"></i>
                </div>
                <div>
                    <span class="text-secondary small fw-medium text-uppercase tracking-wider">Neutral Tone</span>
                    <h3 class="mb-0 fw-bold mt-1 text-warning">{{ number_format($neutral) }} <small class="fs-6 text-muted">({{ $total > 0 ? round(($neutral/$total)*100) : 0 }}%)</small></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="detail-card h-100 p-3 shadow-sm border-0 rounded-4">
            <div class="d-flex align-items-center">
                <div class="rounded-3 bg-danger-subtle text-danger p-3 me-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                </div>
                <div>
                    <span class="text-secondary small fw-medium text-uppercase tracking-wider">Risk / Negative</span>
                    <h3 class="mb-0 fw-bold mt-1 text-danger">{{ number_format($negative) }} <small class="fs-6 text-muted">({{ $total > 0 ? round(($negative/$total)*100) : 0 }}%)</small></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Sentiment Analytics & Quick Filters -->
    <div class="col-lg-4">
        <div class="detail-card p-4 border-0 shadow-sm rounded-4 mb-4">
            <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-pie-chart-fill text-primary me-2"></i> Overall Sentiment Distribution</h5>
            <div style="height: 240px;" class="position-relative">
                <canvas id="sentimentAggregateChart"></canvas>
            </div>
            <div class="text-center mt-3 pt-3 border-top">
                <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Sentiment is evaluated automatically using domain lexicon keyword weighting.</small>
            </div>
        </div>

        <div class="detail-card p-4 border-0 shadow-sm rounded-4">
            <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-funnel-fill text-primary me-2"></i> Sentiment Filter</h5>
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('news.index') }}" class="btn {{ !request('sentiment') ? 'btn-primary' : 'btn-light border' }} text-start rounded-3 px-3 py-2 d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-grid-fill me-2"></i> All Evaluated News</span>
                    <span class="badge bg-white text-dark border">{{ $total }}</span>
                </a>
                <a href="{{ route('news.index', ['sentiment' => 'Positive']) }}" class="btn {{ request('sentiment') == 'Positive' ? 'btn-success' : 'btn-light border' }} text-start rounded-3 px-3 py-2 d-flex justify-content-between align-items-center">
                    <span>🟢 Positive Market Tone</span>
                    <span class="badge bg-white text-success border">{{ $positive }}</span>
                </a>
                <a href="{{ route('news.index', ['sentiment' => 'Neutral']) }}" class="btn {{ request('sentiment') == 'Neutral' ? 'btn-warning' : 'btn-light border' }} text-start rounded-3 px-3 py-2 d-flex justify-content-between align-items-center">
                    <span>🟡 Neutral / Informational</span>
                    <span class="badge bg-white text-warning border">{{ $neutral }}</span>
                </a>
                <a href="{{ route('news.index', ['sentiment' => 'Negative']) }}" class="btn {{ request('sentiment') == 'Negative' ? 'btn-danger' : 'btn-light border' }} text-start rounded-3 px-3 py-2 d-flex justify-content-between align-items-center">
                    <span>🔴 Risk & Disruption Alerts</span>
                    <span class="badge bg-white text-danger border">{{ $negative }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Right Column: Search Toolbar & News Feed Cards -->
    <div class="col-lg-8">
        <div class="detail-card p-4 border-0 shadow-sm rounded-4 mb-4">
            <!-- Search & Toolbar -->
            <form method="GET" action="{{ route('news.index') }}" class="row g-2 mb-4">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Search news title, country, or source...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="sentiment" class="form-select">
                        <option value="">All Sentiments</option>
                        <option value="Positive" {{ request('sentiment') == 'Positive' ? 'selected' : '' }}>Positive</option>
                        <option value="Neutral" {{ request('sentiment') == 'Neutral' ? 'selected' : '' }}>Neutral</option>
                        <option value="Negative" {{ request('sentiment') == 'Negative' ? 'selected' : '' }}>Negative</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                    @if(request()->hasAny(['search', 'sentiment']))
                        <a href="{{ route('news.index') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>

            <!-- News Feed -->
            <div class="d-flex flex-column gap-3">
                @forelse($newsList as $index => $news)
                
                <!-- News Card -->
                <div class="p-3 border rounded-4 bg-white shadow-xs hover-card transition-all position-relative">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-5">{{ $news->country->flag }}</span>
                            <span class="fw-bold text-dark">{{ $news->country->name }}</span>
                            <span class="text-muted">•</span>
                            <span class="badge bg-light text-primary border fw-semibold">{{ $news->source }}</span>
                        </div>
                        <span class="badge {{ $news->sentiment == 'Positive' ? 'bg-success-subtle text-success border border-success-subtle' : ($news->sentiment == 'Negative' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle') }} px-3 py-1 rounded-pill">
                            {{ $news->sentiment }}
                        </span>
                    </div>

                    <h5 class="fw-bold mb-2">
                        <a href="{{ $news->url }}" target="_blank" rel="noopener noreferrer" class="text-dark text-decoration-none hover-primary-link">
                            {{ $news->title }}
                        </a>
                    </h5>

                    <p class="text-secondary small mb-3 line-clamp-2">
                        {{ $news->description }}
                    </p>

                    <!-- Card Footer Bar -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center pt-2 border-top gap-2">
                        <div class="d-flex align-items-center gap-3 small text-muted">
                            <span><i class="bi bi-clock me-1"></i> {{ $news->published_at ? $news->published_at->diffForHumans() : 'Recently' }}</span>
                            <span>🟢 Pos: <strong>{{ $news->positive_count }}</strong></span>
                            <span>🔴 Neg: <strong>{{ $news->negative_count }}</strong></span>
                        </div>

                        @if($news->url && $news->url !== '#')
                        <a href="{{ $news->url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size: 12px;">
                            Read Story on {{ $news->source }} <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                        @endif
                    </div>
                </div>

                @empty
                <div class="text-center py-5">
                    <div class="rounded-circle bg-light d-inline-flex p-4 mb-3">
                        <i class="bi bi-newspaper fs-1 text-muted"></i>
                    </div>
                    <h5 class="fw-bold text-dark">No News Articles Found</h5>
                    <p class="text-muted small">No articles matched your criteria. Try adjusting your search filters or sync live news.</p>
                    <a href="{{ route('news.index', ['refresh' => 1]) }}" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-arrow-clockwise me-1"></i> Sync Live News Now
                    </a>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if(method_exists($newsList, 'hasPages') && $newsList->hasPages())
                <div class="d-flex justify-content-center mt-4 pt-3 border-top">
                    {{ $newsList->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const sentimentCtx = document.getElementById('sentimentAggregateChart');
    if (sentimentCtx) {
        new Chart(sentimentCtx, {
            type: 'doughnut',
            data: {
                labels: ['Positive', 'Neutral', 'Negative'],
                datasets: [{
                    data: [{{ $positive }}, {{ $neutral }}, {{ $negative }}],
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
