@extends('layouts.app')

@section('title', 'Currency Impact & Volatility')

@section('content')

<!-- Header Section -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h1 class="dashboard-title mb-1">
            <i class="bi bi-currency-exchange text-primary me-2"></i>Currency Impact & Volatility Dashboard
        </h1>
        <p class="page-subtitle mb-0">
            Real-time exchange rates, interactive converter, and authentic 6-month historical volatility trends relative to USD.
        </p>
    </div>
    <div>
        <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
            <i class="bi bi-clock-history me-1"></i> Live Rates Updated: {{ $stats['last_updated'] }}
        </span>
    </div>
</div>


<div class="row g-4 mb-4">
    <!-- Currency Converter Card (col-lg-5) -->
    <div class="col-lg-5">
        <div class="detail-card h-100 p-4 border-0 shadow-sm rounded-4 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-calculator-fill text-primary me-2"></i> Currency Converter</h4>
                    <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill">Live FX</span>
                </div>

                <div class="converter-box p-3 rounded-4 bg-light border mb-3">
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-semibold">Amount</label>
                        <input type="number" id="convertAmount" class="form-control form-control-lg fw-bold border-2" value="1000" min="0" step="any">
                        <div class="d-flex gap-1 mt-2">
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-0" style="font-size: 11px;" onclick="setAmount(100)">100</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-0" style="font-size: 11px;" onclick="setAmount(1000)">1,000</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-0" style="font-size: 11px;" onclick="setAmount(10000)">10,000</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-0" style="font-size: 11px;" onclick="setAmount(1000000)">1,000,000</button>
                        </div>
                    </div>

                    <div class="row g-2 align-items-center mb-3 position-relative">
                        <div class="col-5">
                            <label class="form-label text-secondary small fw-semibold">From</label>
                            <select id="convertFrom" class="form-select fw-semibold">
                                <option value="USD" selected>🇺🇸 USD - United States</option>
                                @foreach($rates as $code => $rate)
                                    @if($code !== 'USD')
                                        <option value="{{ $code }}">{{ $code }} - {{ $currencyNames[$code] ?? 'Global' }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <!-- Swap Button -->
                        <div class="col-2 text-center pt-3">
                            <button type="button" id="swapBtn" class="btn btn-sm btn-primary rounded-circle shadow-sm" style="width: 36px; height: 36px;" title="Swap Currencies">
                                <i class="bi bi-arrow-left-right"></i>
                            </button>
                        </div>

                        <div class="col-5">
                            <label class="form-label text-secondary small fw-semibold">To</label>
                            <select id="convertTo" class="form-select fw-semibold">
                                <option value="USD">🇺🇸 USD - United States</option>
                                @foreach($rates as $code => $rate)
                                    @if($code !== 'USD')
                                        <option value="{{ $code }}" {{ $code == 'IDR' ? 'selected' : '' }}>{{ $code }} - {{ $currencyNames[$code] ?? 'Global' }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="text-center p-3 bg-white rounded-3 border">
                        <div class="text-muted small mb-1">Converted Value</div>
                        <h2 id="convertResult" class="fw-bold text-primary mb-1">--</h2>
                        <small class="text-muted" id="rateHint">1 USD = --</small>
                    </div>
                </div>
            </div>

            <div class="text-muted small text-center pt-2">
                <i class="bi bi-shield-check text-success me-1"></i> Exchange rates powered by ECB & Open Exchange Data.
            </div>
        </div>
    </div>

    <!-- Volatility Highlights (col-lg-7) -->
    <div class="col-lg-7">
        <div class="detail-card h-100 p-4 border-0 shadow-sm rounded-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-graph-up-arrow text-primary me-2"></i> Volatility Trends (Authentic 6-Month History)</h4>
                    <p class="text-muted small mb-0">Real monthly exchange rate movements against USD from World Exchange API.</p>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-2 g-3" style="max-height: 440px; overflow-y: auto; padding-right: 5px;">
                @foreach($chartData as $countryName => $data)
                @if($data['is_major'])
                <div class="col">
                    <div class="p-3 border rounded-4 bg-white shadow-xs">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-5">{{ $data['flag'] }}</span>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 14px;">{{ $countryName }}</h6>
                                    <span class="badge bg-light text-secondary border" style="font-size: 10px;">{{ $data['currency'] }}</span>
                                </div>
                            </div>
                            <span class="badge {{ $data['change_pct'] >= 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}" style="font-size: 11px;">
                                {{ $data['change_pct'] >= 0 ? '+' : '' }}{{ $data['change_pct'] }}% 6M
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-baseline mb-2">
                            <div class="fs-4 fw-bold text-dark">{{ number_format($data['rate'], 4) }}</div>
                            <small class="text-muted" style="font-size: 11px;">Min: {{ number_format($data['min'], 2) }} | Max: {{ number_format($data['max'], 2) }}</small>
                        </div>

                        <div style="height: 100px;">
                            <canvas id="chart-{{ $data['slug'] }}"></canvas>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- All Currencies Table Section -->
<div class="detail-card p-4 border-0 shadow-sm rounded-4 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 fw-bold text-dark"><i class="bi bi-table text-primary me-2"></i> All Global Exchange Rates (Per 1 USD)</h4>
            <p class="text-muted small mb-0">Complete list of real-time foreign exchange rates for monitored international economies.</p>
        </div>

        <div style="max-width: 320px; width: 100%;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="tableSearch" class="form-control border-start-0 ps-0" placeholder="Search currency code or country...">
            </div>
        </div>
    </div>

    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0" id="ratesTable">
            <thead class="table-light sticky-top" style="top: 0;">
                <tr>
                    <th class="ps-3">Currency Code</th>
                    <th>Country / Region</th>
                    <th>Rate against 1 USD</th>
                    <th>Inverse (1 FC = USD)</th>
                    <th class="text-end pe-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rates as $code => $rate)
                <tr>
                    <td class="ps-3">
                        <strong class="text-primary">{{ $code }}</strong>
                    </td>
                    <td>
                        <span class="fw-medium text-dark">{{ $currencyNames[$code] ?? 'Global Trade' }}</span>
                    </td>
                    <td>
                        <strong class="fs-6">{{ number_format($rate, 4) }}</strong>
                    </td>
                    <td class="text-muted">
                        ${{ number_format(1 / ($rate > 0 ? $rate : 1), 6) }}
                    </td>
                    <td class="text-end pe-3">
                        @if($code === 'USD')
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Base Currency</span>
                        @elseif($rate > 100)
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">High Denomination</span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Active FX</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const months = @json($months);
    const chartData = @json($chartData);
    const rawRates = @json($rates);
    const rates = { 'USD': 1.0, ...rawRates };

    // Render 6-Month Historical Volatility Trend Charts
    Object.keys(chartData).forEach(function (countryName) {
        const data = chartData[countryName];
        if (!data.is_major) return;

        const canvasId = 'chart-' + data.slug;
        const ctx = document.getElementById(canvasId);

        if (ctx) {
            const isPositive = data.change_pct >= 0;
            const strokeColor = isPositive ? '#10B981' : '#EF4444';
            const fillColor = isPositive ? 'rgba(16, 185, 129, 0.12)' : 'rgba(239, 68, 68, 0.12)';

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: data.currency,
                        data: data.trend,
                        borderColor: strokeColor,
                        backgroundColor: fillColor,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rate: ' + context.raw;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { display: false },
                        y: { display: false }
                    }
                }
            });
        }
    });

    // Converter Controls
    const amountInput = document.getElementById('convertAmount');
    const fromSelect = document.getElementById('convertFrom');
    const toSelect = document.getElementById('convertTo');
    const resultDisplay = document.getElementById('convertResult');
    const rateHint = document.getElementById('rateHint');
    const swapBtn = document.getElementById('swapBtn');

    window.setAmount = function(val) {
        amountInput.value = val;
        calculateConversion();
    };

    function calculateConversion() {
        let amount = parseFloat(amountInput.value) || 0;
        let fromCurr = fromSelect.value;
        let toCurr = toSelect.value;

        let rateFrom = rates[fromCurr] || 1;
        let rateTo = rates[toCurr] || 1;

        let usdVal = amount / rateFrom;
        let finalVal = usdVal * rateTo;

        let unitRate = (1 / rateFrom) * rateTo;

        let decimals = finalVal < 0.01 && finalVal > 0 ? 6 : 2;
        let formatted = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(finalVal);

        resultDisplay.textContent = toCurr + ' ' + formatted;
        rateHint.textContent = `1 ${fromCurr} = ${unitRate < 0.01 ? unitRate.toFixed(6) : unitRate.toFixed(4)} ${toCurr}`;
    }

    swapBtn.addEventListener('click', function() {
        let temp = fromSelect.value;
        fromSelect.value = toSelect.value;
        toSelect.value = temp;
        calculateConversion();
    });

    [amountInput, fromSelect, toSelect].forEach(el => {
        el.addEventListener('input', calculateConversion);
        el.addEventListener('change', calculateConversion);
    });

    calculateConversion();

    // Table Search Filter
    const tableSearch = document.getElementById('tableSearch');
    const ratesTable = document.getElementById('ratesTable');

    tableSearch.addEventListener('keyup', function() {
        let term = this.value.toLowerCase();
        let rows = ratesTable.querySelectorAll('tbody tr');

        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(term) ? '' : 'none';
        });
    });
});
</script>
@endsection
