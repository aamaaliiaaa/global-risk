@extends('layouts.app')

@section('title', 'Country Comparison')

@section('content')

<div class="page-header">
    <div>
        <h1 class="dashboard-title">Country Comparison Engine</h1>
        <p class="page-subtitle">
            Compare logistics risk levels, makroekonomi indicators, and weather conditions of two countries.
        </p>
    </div>
</div>

<div class="dashboard-card mb-4">
    <form method="GET" action="{{ route('compare.index') }}" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label fw-bold">Select Country A</label>
            <select name="country_a" class="form-select py-2 rounded-3">
                <option value="">-- Choose Country A --</option>
                @foreach($countries as $c)
                <option value="{{ $c->id }}" {{ request('country_a') == $c->id ? 'selected' : '' }}>
                    {{ $c->flag }} {{ $c->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 text-center pb-2">
            <span class="fs-4 text-secondary">VS</span>
        </div>

        <div class="col-md-5">
            <label class="form-label fw-bold">Select Country B</label>
            <select name="country_b" class="form-select py-2 rounded-3">
                <option value="">-- Choose Country B --</option>
                @foreach($countries as $c)
                <option value="{{ $c->id }}" {{ request('country_b') == $c->id ? 'selected' : '' }}>
                    {{ $c->flag }} {{ $c->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-12 mt-3 text-center">
            <button type="submit" class="btn btn-primary rounded-3 px-5 py-2">
                <i class="bi bi-bar-chart-fill"></i> Compare Countries
            </button>
        </div>
    </form>
</div>

@if($comparison)
<div class="dashboard-card">
    <div class="section-title text-center fs-4 border-bottom pb-3 mb-4">
        {{ $comparison['a']['flag'] }} {{ $comparison['a']['name'] }} vs {{ $comparison['b']['flag'] }} {{ $comparison['b']['name'] }}
    </div>
    
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle fs-5">
            <thead>
                <tr class="table-light text-center">
                    <th style="width: 30%;">Indicator</th>
                    <th style="width: 35%;">{{ $comparison['a']['flag'] }} {{ $comparison['a']['name'] }}</th>
                    <th style="width: 35%;">{{ $comparison['b']['flag'] }} {{ $comparison['b']['name'] }}</th>
                </tr>
            </thead>
            <tbody>
                <!-- Risk Level -->
                <tr>
                    <td class="fw-bold">Risk Level</td>
                    <td class="text-center">
                        <span class="badge-risk {{ strtolower($comparison['a']['risk_level']) }}">
                            {{ $comparison['a']['risk_level'] }} ({{ $comparison['a']['risk_score'] }}/100)
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge-risk {{ strtolower($comparison['b']['risk_level']) }}">
                            {{ $comparison['b']['risk_level'] }} ({{ $comparison['b']['risk_score'] }}/100)
                        </span>
                    </td>
                </tr>

                <!-- Weather -->
                <tr>
                    <td class="fw-bold">Current Weather</td>
                    <td class="text-center">
                        <strong>{{ $comparison['a']['weather']['condition'] }}</strong><br>
                        🌡️ {{ $comparison['a']['weather']['temperature'] }}°C<br>
                        💨 {{ $comparison['a']['weather']['wind_speed'] }} km/h
                    </td>
                    <td class="text-center">
                        <strong>{{ $comparison['b']['weather']['condition'] }}</strong><br>
                        🌡️ {{ $comparison['b']['weather']['temperature'] }}°C<br>
                        💨 {{ $comparison['b']['weather']['wind_speed'] }} km/h
                    </td>
                </tr>

                <!-- Currency -->
                <tr>
                    <td class="fw-bold">Currency & Exchange Rate (1 USD)</td>
                    <td class="text-center">
                        {{ $comparison['a']['currency'] }} (Rate: {{ number_format($comparison['a']['exchange_rate'], 2) }})
                    </td>
                    <td class="text-center">
                        {{ $comparison['b']['currency'] }} (Rate: {{ number_format($comparison['b']['exchange_rate'], 2) }})
                    </td>
                </tr>

                <!-- GDP -->
                <tr>
                    <td class="fw-bold">GDP (USD)</td>
                    <td class="text-center">
                        {{ $comparison['a']['gdp'] ? '$' . number_format($comparison['a']['gdp']) : 'N/A' }}
                    </td>
                    <td class="text-center">
                        {{ $comparison['b']['gdp'] ? '$' . number_format($comparison['b']['gdp']) : 'N/A' }}
                    </td>
                </tr>

                <!-- Inflation -->
                <tr>
                    <td class="fw-bold">Inflation Rate</td>
                    <td class="text-center">
                        {{ $comparison['a']['inflation'] !== null ? number_format($comparison['a']['inflation'], 2) . '%' : 'N/A' }}
                    </td>
                    <td class="text-center">
                        {{ $comparison['b']['inflation'] !== null ? number_format($comparison['b']['inflation'], 2) . '%' : 'N/A' }}
                    </td>
                </tr>

                <!-- Population -->
                <tr>
                    <td class="fw-bold">Population</td>
                    <td class="text-center">
                        {{ $comparison['a']['population'] ? number_format($comparison['a']['population']) : 'N/A' }}
                    </td>
                    <td class="text-center">
                        {{ $comparison['b']['population'] ? number_format($comparison['b']['population']) : 'N/A' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@elseif(request('country_a') || request('country_b'))
<div class="alert alert-warning text-center rounded-3 fs-5">
    Please select BOTH countries to run the comparison engine.
</div>
@endif

@endsection
