@extends('layouts.app')

@section('title', 'Currency Impact')

@section('content')

<div class="page-header">
    <div>
        <h1 class="dashboard-title">Currency Impact Dashboard</h1>
        <p class="page-subtitle">
            Monitor real-time exchange rates and historical fluctuations relative to USD.
        </p>
    </div>
</div>

<div class="row g-4">
    <!-- Live Rates Table -->
    <div class="col-lg-5">
        <div class="dashboard-card h-100">
            <div class="section-title">
                <i class="bi bi-currency-exchange"></i> Exchange Rates (1 USD)
            </div>
            <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Currency Code</th>
                            <th>Current Rate</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rates as $code => $rate)
                        <tr>
                            <td><strong>{{ $code }}</strong></td>
                            <td>{{ number_format($rate, 4) }}</td>
                            <td>
                                @if($rate > 1)
                                <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                <span class="badge bg-secondary-subtle text-secondary">Base / Pegged</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Fluctuation Trends -->
    <div class="col-lg-7">
        <div class="dashboard-card h-100">
            <div class="section-title">
                <i class="bi bi-graph-up"></i> Currency Volatility Trends
            </div>
            <div class="row g-3">
                @foreach($chartData as $countryName => $data)
                <div class="col-md-6">
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold text-secondary">{{ $countryName }}</span>
                            <span class="text-primary fw-bold">{{ $data['currency'] }}</span>
                        </div>
                        <div class="fs-4 fw-bold mb-3">{{ number_format($data['rate'], 4) }}</div>
                        <div style="height: 120px;">
                            <canvas id="chart-{{ Str::slug($countryName) }}"></canvas>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const months = @json($months);
    const chartData = @json($chartData);

    Object.keys(chartData).forEach(function (countryName) {
        const data = chartData[countryName];
        const canvasId = 'chart-' + countryName.toLowerCase().replace(/\s+/g, '-');
        const ctx = document.getElementById(canvasId);

        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: data.currency,
                        data: data.trend,
                        borderColor: '#2563EB',
                        backgroundColor: 'rgba(37,99,235,.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: 9
                                }
                            }
                        }
                    }
                }
            });
        }
    });
});
</script>
@endsection
