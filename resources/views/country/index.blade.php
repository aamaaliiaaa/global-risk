@extends('layouts.app')

@section('title', 'Countries')

@section('content')

<div class="page-header">
    <div>
        <h1 class="dashboard-title">
            Countries
        </h1>
        <p class="page-subtitle">
            Monitor country risk, weather, currency and logistics information.
        </p>
    </div>
</div>

<div class="country-toolbar mb-4">
    <form method="GET" action="{{ route('countries.index') }}" class="d-flex gap-3 align-items-center w-100">
        <div class="search-country flex-grow-1 position-relative">
            <i class="bi bi-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search country..."
                class="form-control py-2 ps-5 rounded-3 border"
            >
        </div>

        <select class="form-select py-2 rounded-3 border" name="risk" onchange="this.form.submit()" style="width: 200px;">
            <option value="">All Risk</option>
            <option value="High" {{ request('risk') == 'High' ? 'selected' : '' }}>High Risk</option>
            <option value="Medium" {{ request('risk') == 'Medium' ? 'selected' : '' }}>Medium Risk</option>
            <option value="Low" {{ request('risk') == 'Low' ? 'selected' : '' }}>Low Risk</option>
        </select>
    </form>
</div>

<div class="dashboard-card border-0 p-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 py-2">
            <thead class="table-light py-3">
                <tr>
                    <th class="ps-4">Country</th>
                    <th>Risk</th>
                    <th>Weather</th>
                    <th>Currency</th>
                    <th class="text-end pe-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($countries as $country)
                <tr>
                    <td class="ps-4">
                        <span class="fs-5 me-2">{{ $country->flag }}</span>
                        <strong>{{ $country->name }}</strong>
                    </td>
                    <td>
                        <span class="badge-risk {{ strtolower($country->risk) }}">
                            {{ $country->risk }}
                        </span>
                    </td>
                    <td>{{ $country->weather }}</td>
                    <td>{{ strtoupper($country->currency) }}</td>
                    <td class="text-end pe-4">
                        <a href="{{ route('countries.show', $country->id) }}" class="btn btn-sm btn-primary rounded-3 px-3">
                            <i class="bi bi-eye"></i> Details
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No countries found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection