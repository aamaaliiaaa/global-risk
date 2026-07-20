@extends('layouts.app')

@section('title','Ports')

@section('content')

<h1 class="dashboard-title">
    Ports
</h1>

<p class="page-subtitle">
    Monitor global ports and shipping activities.
</p>

<div class="country-toolbar">

    <form method="GET" class="country-toolbar">

        <div class="search-country">

            <i class="bi bi-search"></i>

            <input
                type="text"
                name="search"
                placeholder="Search port..."
                value="{{ request('search') }}"
            >

        </div>

        <select name="status" onchange="this.form.submit()">

            <option value="">All Status</option>

            <option value="Normal">Normal</option>

            <option value="Busy">Busy</option>

            <option value="Delay">Delay</option>

            <option value="Congested">Congested</option>

        </select>

    </form>

</div>

<div class="table-card">

<table class="custom-table">

<thead>
    <tr>

        <th>Port</th>

        <th>Country</th>

        <th>Status</th>

        <th>Risk</th>

        <th>Action</th>

    </tr>

</thead>

<tbody>

@foreach($ports as $port)

    <tr>
        <td>

            🚢 {{ $port->name }}

            <br>

            <small>{{ $port->city }}</small>

        </td>

        <td>

            {{ $port->country->name }}

        </td>

        <td>

            @if($port->status=="Normal")

                <span class="badge-success">Normal</span>

            @elseif($port->status=="Busy")

                <span class="badge-warning">Busy</span>

            @elseif($port->status=="Delay")

                <span class="badge-orange">Delay</span>

            @else

                <span class="badge-danger">Congested</span>

            @endif

        </td>

        <td>

            {{ $port->risk_score }}

        </td>

        <td>

            <a href="{{ route('ports.show',$port->id) }}" class="btn-view">

                View

            </a>

        </td>

    </tr>

    @endforeach

</tbody>

</table>

</div>

@endsection