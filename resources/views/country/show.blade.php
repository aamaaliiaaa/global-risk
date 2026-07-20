@extends('layouts.app')

@section('title', $country->name)

@section('content')

<h1 class="dashboard-title">

    {{ $country->flag }} {{ $country->name }}

</h1>

<div class="country-info-grid">

    <div class="detail-card">

        <h4>Country Information</h4>

        <div class="detail-item">
            <strong>Risk</strong>
            <span>{{ $country->risk }}</span>
        </div>

        <div class="detail-item">
            <strong>Weather</strong>
            <span>
                {{ $condition }}
                <br>
                🌡️ {{ $weather['current']['temperature_2m'] }}°C
                <br>
                💨 {{ $weather['current']['wind_speed_10m'] }} km/h
            </span>
        </div>

        <div class="detail-item">
            <strong>Currency</strong>
            <span>{{ $country->currency }}
            </span>
        </div>

    </div>

    <div class="detail-card">

        <h4>Risk Overview</h4>

        <canvas id="riskChart" height="220"></canvas>

    </div>

</div>

<div class="detail-card mt-4">

    <h4>
        <i class="bi bi-geo-alt-fill text-primary"></i>
        Country Location
    </h4>

    <div id="countryMap"></div>

    <h4>Latest News</h4>

    @if(isset($news['articles']) && count($news['articles']) > 0)

    <ul class="news-list">

    @foreach($news['articles'] as $article)

    <li>

        <strong>{{ $article['title'] }}</strong>

        <br>

        <small>{{ $article['source']['name'] }}</small>

    </li>

    @endforeach

    </ul>

    @else

    <p>No news available.</p>

    @endif

</div>

@endsection

@section('scripts')

<script>

const ctx = document.getElementById('riskChart');

new Chart(ctx, {

    type: 'line',

    data: {

        labels: ['Jan','Feb','Mar','Apr','May','Jun'],

        datasets: [{

            label: 'Risk Score',

            data: [25,40,35,60,50,70],

            borderColor:'#2563eb',

            backgroundColor:'rgba(37,99,235,.15)',

            fill:true,

            tension:.4

        }]

    },

    options:{

        plugins:{

            legend:{
                display:false
            }

        },

        scales:{

            y:{
                beginAtZero:true
            }

        }

    }

});

const lat = {{ $country->latitude }};
const lng = {{ $country->longitude }};

const map = L.map('countryMap').setView([lat, lng], 4);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
}).addTo(map);

L.marker([lat, lng])
    .addTo(map)
    .bindPopup('{{ $country->name }}')
    .openPopup();

</script>

@endsection