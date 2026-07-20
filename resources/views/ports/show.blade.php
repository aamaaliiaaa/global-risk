@extends('layouts.app')

@section('title',$port->name)

@section('content')

<h1 class="dashboard-title">

🚢 {{ $port->name }}

</h1>

<p class="page-subtitle">

{{ $port->city }},
{{ $port->country->name }}

</p>

<div class="country-info-grid">

<div class="detail-card">

<h4>Port Information</h4>

<div class="detail-item">

<strong>Status</strong>

<span>{{ $port->status }}</span>

</div>

<div class="detail-item">

<strong>Risk Score</strong>

<span>{{ $port->risk_score }}</span>

</div>

<div class="detail-item">

<strong>Capacity</strong>

<span>

{{ number_format($port->container_capacity) }}

TEU

</span>

</div>

<div class="detail-item">

<strong>Throughput</strong>

<span>

{{ number_format($port->annual_throughput) }}

TEU/year

</span>

</div>

</div>

<div class="detail-card">

<h4>Weather</h4>

@if(isset($weather['current']))

<div class="detail-item">

🌡

{{ $weather['current']['temperature_2m'] }}

°C

</div>

<div class="detail-item">

💨

{{ $weather['current']['wind_speed_10m'] }}

km/h

</div>

<div class="detail-item">

{{ $condition }}

</div>

@endif

</div>

</div>

@endsection