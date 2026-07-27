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

<!-- Top Section: Converter (Left) + Pair Volatility Analytics Hero (Right) -->
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
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-0.5" style="font-size: 11px;" onclick="setAmount(100)">100</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-0.5" style="font-size: 11px;" onclick="setAmount(1000)">1,000</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-0.5" style="font-size: 11px;" onclick="setAmount(10000)">10,000</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-0.5" style="font-size: 11px;" onclick="setAmount(1000000)">1,000,000</button>
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
                        <h2 id="convertResult" class="fw-bold text-primary mb-1" style="font-size: 1.8rem;">--</h2>
                        <small class="text-muted d-block" id="rateHint">1 USD = --</small>
                    </div>
                </div>
            </div>

            <div class="text-muted small text-center pt-2">
                <i class="bi bi-shield-check text-success me-1"></i> Exchange rates powered by ECB & Open Exchange Data.
            </div>
        </div>
    </div>

    <!-- Pair Volatility Analytics Hero Panel (col-lg-7) -->
    <div class="col-lg-7">
        <div class="detail-card h-100 p-4 border-0 shadow-sm rounded-4 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-graph-up-arrow text-primary"></i>
                            <span id="chartPairTitle">Historical Volatility Analytics (USD ➔ IDR)</span>
                        </h4>
                        <p class="text-muted small mb-0">6-month monthly exchange rate trend & volatility range for selected currency pair.</p>
                    </div>
                    <span id="chartPairBadge" class="badge bg-success-subtle text-success border px-3 py-1.5 rounded-pill fs-6">+0.00% 6M</span>
                </div>

                <!-- Min / Max Stat Pills -->
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 bg-light border text-center">
                            <small class="text-muted d-block" style="font-size: 11px;">6-Month Min Rate</small>
                            <strong class="text-dark fs-6" id="chartPairMin">--</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 bg-light border text-center">
                            <small class="text-muted d-block" style="font-size: 11px;">6-Month Max Rate</small>
                            <strong class="text-dark fs-6" id="chartPairMax">--</strong>
                        </div>
                    </div>
                </div>

                <!-- Spacious Canvas -->
                <div style="height: 250px; position: relative;">
                    <canvas id="converterChartCanvas"></canvas>
                </div>
            </div>

            <div class="text-end text-muted small pt-2">
                <i class="bi bi-info-circle me-1"></i> Chart automatically updates when converter inputs or dropdowns change.
            </div>
        </div>
    </div>
</div>

<!-- Major Currencies Volatility Highlights Section -->
<div class="detail-card p-4 border-0 shadow-sm rounded-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 fw-bold text-dark"><i class="bi bi-globe-americas text-primary me-2"></i> Major Currencies Volatility Grid</h4>
            <p class="text-muted small mb-0">Real monthly exchange rate movements against USD for major world currencies.</p>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-3">
        @foreach($chartData as $countryName => $data)
        @if($data['is_major'])
        <div class="col">
            <div class="p-3 border rounded-4 bg-white shadow-xs hover-lift" style="cursor: pointer;" onclick="selectCurrencyPair('USD', '{{ $data['currency'] }}')">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-5">{{ $data['flag'] }}</span>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark" style="font-size: 13px;">{{ $countryName }}</h6>
                            <span class="badge bg-light text-secondary border" style="font-size: 10px;">{{ $data['currency'] }}</span>
                        </div>
                    </div>
                    <span class="badge {{ $data['change_pct'] >= 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}" style="font-size: 10px;">
                        {{ $data['change_pct'] >= 0 ? '+' : '' }}{{ $data['change_pct'] }}% 6M
                    </span>
                </div>

                <div class="d-flex justify-content-between align-items-baseline mb-2">
                    <div class="fs-5 fw-bold text-dark">{{ number_format($data['rate'], 4) }}</div>
                    <small class="text-muted" style="font-size: 10px;">Min: {{ number_format($data['min'], 2) }} | Max: {{ number_format($data['max'], 2) }}</small>
                </div>

                <div style="height: 80px;">
                    <canvas id="chart-{{ $data['slug'] }}"></canvas>
                </div>
            </div>
        </div>
        @endif
        @endforeach
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
            <div class="position-relative">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="tableSearch" class="form-control ps-5 py-2 rounded-pill border" placeholder="Search currency code or country...">
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
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rates as $code => $rate)
                <tr style="cursor: pointer;" onclick="selectCurrencyPair('USD', '{{ $code }}')">
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
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" onclick="event.stopPropagation(); selectCurrencyPair('USD', '{{ $code }}');" style="font-size: 11px;">
                            Analyze Volatility &rarr;
                        </button>
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
    const rates = @json($rates);
    const chartData = @json($chartData);
    const historicalRatesMap = @json($historicalRates ?? []);
    const monthsLabels = @json($months ?? []);

    // Render Mini Trend Charts for Major Currencies
    Object.keys(chartData).forEach(countryName => {
        const item = chartData[countryName];
        const ctx = document.getElementById('chart-' + item.slug);

        if (ctx) {
            let strokeColor = item.change_pct >= 0 ? '#10b981' : '#ef4444';
            let fillColor = item.change_pct >= 0 ? 'rgba(16, 185, 129, 0.12)' : 'rgba(239, 68, 68, 0.12)';

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthsLabels,
                    datasets: [{
                        data: item.trend,
                        borderColor: strokeColor,
                        backgroundColor: fillColor,
                        borderWidth: 2,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 2,
                        pointBackgroundColor: strokeColor
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

    // Hero Converter Chart Control
    let converterChart = null;

    function updateConverterChart(fromCurr, toCurr) {
        const ctxCanvas = document.getElementById('converterChartCanvas');
        if (!ctxCanvas) return;

        // Calculate 6-month historical series for (fromCurr -> toCurr)
        let trendSeries = [];
        monthsLabels.forEach(function (mLabel) {
            let rFrom = 1.0;
            let rTo = 1.0;

            if (fromCurr !== 'USD') {
                rFrom = (historicalRatesMap[fromCurr] && historicalRatesMap[fromCurr][mLabel]) 
                    ? historicalRatesMap[fromCurr][mLabel] 
                    : (rates[fromCurr] || 1.0);
            }

            if (toCurr !== 'USD') {
                rTo = (historicalRatesMap[toCurr] && historicalRatesMap[toCurr][mLabel]) 
                    ? historicalRatesMap[toCurr][mLabel] 
                    : (rates[toCurr] || 1.0);
            }

            let uRate = (1 / rFrom) * rTo;
            let decs = uRate < 0.01 ? 6 : 4;
            trendSeries.push(parseFloat(uRate.toFixed(decs)));
        });

        // Live point
        let liveFrom = rates[fromCurr] || 1.0;
        let liveTo = rates[toCurr] || 1.0;
        let liveUnit = (1 / liveFrom) * liveTo;
        let liveDecs = liveUnit < 0.01 ? 6 : 4;
        trendSeries[trendSeries.length - 1] = parseFloat(liveUnit.toFixed(liveDecs));

        // Stats
        let firstVal = trendSeries[0] || liveUnit;
        let lastVal = trendSeries[trendSeries.length - 1];
        let changeVal = lastVal - firstVal;
        let changePct = firstVal > 0 ? ((changeVal / firstVal) * 100).toFixed(2) : 0;
        let minVal = Math.min(...trendSeries);
        let maxVal = Math.max(...trendSeries);

        // Update DOM Header & Badges
        document.getElementById('chartPairTitle').textContent = `Historical Volatility Analytics (${fromCurr} ➔ ${toCurr})`;
        
        const badgeEl = document.getElementById('chartPairBadge');
        if (changePct >= 0) {
            badgeEl.className = 'badge bg-success-subtle text-success border px-3 py-1.5 rounded-pill fs-6';
            badgeEl.textContent = `+${changePct}% 6M`;
        } else {
            badgeEl.className = 'badge bg-danger-subtle text-danger border px-3 py-1.5 rounded-pill fs-6';
            badgeEl.textContent = `${changePct}% 6M`;
        }

        document.getElementById('chartPairMin').textContent = minVal < 0.01 ? minVal.toFixed(6) : minVal.toFixed(4);
        document.getElementById('chartPairMax').textContent = maxVal < 0.01 ? maxVal.toFixed(6) : maxVal.toFixed(4);

        // Render / Update Chart.js Instance
        let strokeColor = changePct >= 0 ? '#10b981' : '#ef4444';
        let fillColor = changePct >= 0 ? 'rgba(16, 185, 129, 0.12)' : 'rgba(239, 68, 68, 0.12)';

        if (converterChart) {
            converterChart.data.labels = monthsLabels;
            converterChart.data.datasets[0].label = `${fromCurr} to ${toCurr}`;
            converterChart.data.datasets[0].data = trendSeries;
            converterChart.data.datasets[0].borderColor = strokeColor;
            converterChart.data.datasets[0].backgroundColor = fillColor;
            converterChart.data.datasets[0].pointBackgroundColor = strokeColor;
            converterChart.update();
        } else {
            converterChart = new Chart(ctxCanvas, {
                type: 'line',
                data: {
                    labels: monthsLabels,
                    datasets: [{
                        label: `${fromCurr} to ${toCurr}`,
                        data: trendSeries,
                        borderColor: strokeColor,
                        backgroundColor: fillColor,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointBackgroundColor: strokeColor
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return `1 ${fromCurr} = ${ctx.raw} ${toCurr}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11, weight: '500' } }
                        },
                        y: {
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { size: 11 } }
                        }
                    }
                }
            });
        }
    }

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

    window.selectCurrencyPair = function(fromCurr, toCurr) {
        fromSelect.value = fromCurr;
        toSelect.value = toCurr;
        calculateConversion();
        window.scrollTo({ top: 0, behavior: 'smooth' });
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

        // Update Hero Volatility Analytics Chart
        updateConverterChart(fromCurr, toCurr);
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

    if (tableSearch && ratesTable) {
        tableSearch.addEventListener('keyup', function() {
            let term = this.value.toLowerCase();
            let rows = ratesTable.querySelectorAll('tbody tr');

            rows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }
});
</script>
@endsection
