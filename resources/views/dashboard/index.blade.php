@extends('layouts.app')

@section('title','Dashboard')

@section('content')

<h1 class="dashboard-title">
    Dashboard
</h1>

<div class="stats-grid">

    <!-- Total Countries -->
    <div class="stat-card primary">

        <div class="card-top">
            <span class="card-title">Total Countries</span>

            <div class="stat-icon bg-primary">
                <i class="bi bi-globe2"></i>
            </div>
        </div>

        <h2>249</h2>

        <p class="card-subtitle text-success">
            <i class="bi bi-arrow-up-short"></i>
            +3 this week
        </p>

    </div>

    <!-- High Risk -->
    <div class="stat-card danger">

        <div class="card-top">
            <span class="card-title">High Risk</span>

            <div class="stat-icon bg-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
        </div>

        <h2>15</h2>

        <p class="card-subtitle text-danger">
            Need Attention
        </p>

    </div>

    <!-- Medium Risk -->
    <div class="stat-card warning">

        <div class="card-top">
            <span class="card-title">Medium Risk</span>

            <div class="stat-icon bg-warning">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
        </div>

        <h2>38</h2>

        <p class="card-subtitle text-warning">
            Stable
        </p>

    </div>

    <!-- Low Risk -->
    <div class="stat-card success">

        <div class="card-top">
            <span class="card-title">Low Risk</span>

            <div class="stat-icon bg-success">
                <i class="bi bi-shield-check"></i>
            </div>
        </div>

        <h2>196</h2>

        <p class="card-subtitle text-success">
            Safe
        </p>

    </div>

    <!-- News -->
    <div class="stat-card info">

        <div class="card-top">
            <span class="card-title">Today's News</span>

            <div class="stat-icon bg-info">
                <i class="bi bi-newspaper"></i>
            </div>
        </div>

        <h2>124</h2>

        <p class="card-subtitle text-info">
            Updated
        </p>

    </div>

</div>

<div class="dashboard-grid">

    <!-- Heatmap -->
    <div class="dashboard-card">

        <div class="section-title">
            <i class="bi bi-globe-americas"></i>
            Global Risk Heatmap
        </div>

        <div id="worldMap"></div>

    </div>

    <!-- Risk Trend -->
    <div class="dashboard-card">

        <div class="section-title">
            <i class="bi bi-graph-up-arrow"></i>
            Risk Trend
        </div>

        <canvas id="riskChart"></canvas>

    </div>

    <!-- News -->
    <div class="dashboard-card">

        <div class="section-title">
            <i class="bi bi-newspaper"></i>
            Latest News
        </div>

        <div class="news-list">

            <div class="news-item">
                <strong>Singapore Port</strong>
                <p>Container delay due to heavy rain.</p>
            </div>

            <div class="news-item">
                <strong>China Export</strong>
                <p>Export activity increased by 12%.</p>
            </div>

            <div class="news-item">
                <strong>USA Import</strong>
                <p>Import volume remains stable.</p>
            </div>

        </div>

    </div>

    <!-- Sentiment -->
    <div class="dashboard-card">

        <div class="section-title">
            <i class="bi bi-pie-chart"></i>
            Sentiment Analysis
        </div>

        <canvas id="sentimentChart"></canvas>

    </div>

</div>


@endsection
@section('scripts')

<script>

document.addEventListener("DOMContentLoaded", function () {

    const map = L.map('worldMap').setView([20,0],2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{

        attribution:'© OpenStreetMap'

    }).addTo(map);

    L.marker([-6.2,106.8]).addTo(map)
    .bindPopup("Indonesia");

    L.marker([1.35,103.8]).addTo(map)
    .bindPopup("Singapore");

    L.marker([39.9,116.4]).addTo(map)
    .bindPopup("China");

});

const riskCtx = document.getElementById('riskChart');

new Chart(riskCtx, {

    type:'line',

    data:{

        labels:['Jan','Feb','Mar','Apr','Mei','Jun'],

        datasets:[{

            label:'Risk Score',

            data:[35,42,38,55,48,60],

            borderColor:'#2563EB',

            backgroundColor:'rgba(37,99,235,.15)',

            fill:true,

            tension:.4

        }]

    },

    options:{

        responsive:true,

        maintainAspectRatio:false,

        plugins:{

            legend:{

                display:false

            }

        }

    }

});

</script>

@endsection