@extends('layouts.app')

@section('title', 'Watchlist')

@section('content')

<div class="page-header">
    <div>
        <h1 class="dashboard-title">My Monitoring Watchlist</h1>
        <p class="page-subtitle">
            Pin and closely monitor risks for specific countries in your logistic supply chains.
        </p>
    </div>
</div>

<div class="dashboard-card mb-4">
    <form method="POST" action="{{ route('watchlist.store') }}" class="row g-3 align-items-end">
        @csrf
        <div class="col-md-9">
            <label class="form-label fw-bold">Select Country to Monitor</label>
            <select name="country_id" class="form-select py-2 rounded-3" required>
                <option value="">-- Select Country --</option>
                @foreach($allCountries as $country)
                <option value="{{ $country->id }}">{{ $country->flag }} {{ $country->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                <i class="bi bi-pin-angle-fill"></i> Add to Watchlist
            </button>
        </div>
    </form>
</div>

@if(session('success'))
<div class="alert alert-success rounded-3 mb-4">{{ session('success') }}</div>
@endif

<div class="row g-4">
    @forelse($watchlistData as $item)
    <div class="col-md-4">
        <div class="stat-card border-top-0 shadow-sm bg-white p-4 rounded-4 position-relative h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fs-4">{{ $item['flag'] }}</span>
                    <span class="badge-risk {{ strtolower($item['risk_level']) }}">{{ $item['risk_level'] }}</span>
                </div>
                
                <h3 class="fw-bold mb-1"><a href="{{ route('countries.show', $item['country_id']) }}" class="text-dark text-decoration-none">{{ $item['name'] }}</a></h3>
                <p class="text-muted small mb-3">Currency: {{ $item['currency'] }}</p>
                
                <div class="bg-light p-2 rounded mb-3 small text-secondary">
                    <div>Weather: <strong>{{ $item['weather'] }}</strong></div>
                    <div>Risk Score: <strong>{{ $item['risk_score'] }}/100</strong></div>
                </div>
            </div>

            <div class="text-end">
                <form action="{{ route('watchlist.destroy', $item['id']) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Remove this country from watchlist?')">
                        <i class="bi bi-trash"></i> Unpin
                    </button>
                </form>
                <a href="{{ route('countries.show', $item['country_id']) }}" class="btn btn-sm btn-primary rounded-3 text-white px-3">
                    <i class="bi bi-eye"></i> Details
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info text-center py-5 rounded-4">
            <i class="bi bi-star-fill display-4 text-warning mb-3 d-block"></i>
            <h4>No Monitored Countries Yet</h4>
            <p class="text-secondary">Select a country from the menu above to start pinning logistics profiles.</p>
        </div>
    </div>
    @endforelse
</div>

@endsection
