@extends('layouts.app')

@section('title','Countries')

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

    <a href="{{ route('countries.create') }}" class="btn-add">

        <i class="bi bi-plus-lg"></i>

        Add Country

    </a>

</div>

<p class="page-subtitle">
    Monitor country risk, weather, currency and logistics information.
</p>

<div class="country-toolbar">

    <div class="search-country">
        <i class="bi bi-search"></i>
        <form method="GET" action="{{ route('countries.index') }}" class="country-toolbar">

            <div class="search-country">
                <i class="bi bi-search"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search country...">
            </div>

            <select class="risk-filter" name="risk" onchange="this.form.submit()">

                <option value="">All Risk</option>

                <option value="High"
                    {{ request('risk')=='High' ? 'selected':'' }}>
                    High Risk
                </option>

                <option value="Medium"
                    {{ request('risk')=='Medium' ? 'selected':'' }}>
                    Medium Risk
                </option>

                <option value="Low"
                    {{ request('risk')=='Low' ? 'selected':'' }}>
                    Low Risk
                </option>

            </select>

        </form>
        <button type="submit" style="display:none;"></button>
    </div>

    <select class="risk-filter">
        <option>All Risk</option>
        <option>High Risk</option>
        <option>Medium Risk</option>
        <option>Low Risk</option>
    </select>

</div>

<div class="country-table">

    <table>

        <thead>

            <tr>

                <th>Country</th>

                <th>Risk</th>

                <th>Weather</th>

                <th>Currency</th>

                <th>Action</th>

            </tr>

        </thead>

        <tbody>

        @foreach($countries as $country)

        <tr>

            <td>{{ $country->flag }} {{ $country->name }}</td>

            <td>
                <span class="badge-risk {{ strtolower($country->risk) }}">
                    {{ $country->risk }}
                </span>
            </td>

            <td>{{ $country->weather }}</td>

            <td>{{ $country->currency }}</td>

            <td class="action-buttons">

                <a href="{{ route('countries.show',$country->id) }}"
                class="btn-view">
                    <i class="bi bi-eye"></i>
                </a>

                <a href="{{ route('countries.edit',$country->id) }}"
                class="btn-edit">
                    <i class="bi bi-pencil"></i>
                </a>

                <form action="{{ route('countries.destroy',$country->id) }}"
                    method="POST"
                    style="display:inline;">

                    @csrf
                    @method('DELETE')

                    <button class="btn-delete"
                            onclick="return confirm('Delete this country?')">

                        <i class="bi bi-trash"></i>

                    </button>

                </form>

            </td>

        </tr>

        @endforeach

        </tbody>

    </table>

</div>

@endsection