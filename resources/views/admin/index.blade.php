@extends('layouts.app')

@section('title', 'Admin Control Panel')

@section('content')

<!-- Header Section -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h1 class="dashboard-title mb-1">
            <i class="bi bi-shield-lock text-primary me-2"></i>Admin Control & Dataset Management
        </h1>
        <p class="page-subtitle mb-0">
            Manage system users, maritime port datasets, AI sentiment lexicon dictionaries, and published risk articles.
        </p>
    </div>
    <div>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
            <i class="bi bi-person-badge-fill me-1"></i> Administrator Access
        </span>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success border-0 shadow-xs rounded-4 mb-4 d-flex align-items-center">
    <i class="bi bi-check-circle-fill fs-5 me-2"></i>
    <div>{{ session('success') }}</div>
</div>
@endif

<div class="detail-card p-4 border-0 shadow-sm rounded-4">
    <!-- Nav Tabs -->
    <ul class="nav nav-pills nav-justified mb-4 gap-2 bg-light p-2 rounded-4" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-3 py-2 fw-semibold" id="ports-tab" data-bs-toggle="tab" data-bs-target="#ports" type="button" role="tab">
                <i class="bi bi-anchor me-1"></i> Ports Dataset ({{ count($ports) }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 py-2 fw-semibold" id="lexicon-tab" data-bs-toggle="tab" data-bs-target="#lexicon" type="button" role="tab">
                <i class="bi bi-spellcheck me-1"></i> Lexicon Dictionary ({{ count($posWords) + count($negWords) }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 py-2 fw-semibold" id="articles-tab" data-bs-toggle="tab" data-bs-target="#articles" type="button" role="tab">
                <i class="bi bi-journal-text me-1"></i> Articles ({{ count($articles) }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 py-2 fw-semibold" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                <i class="bi bi-people me-1"></i> Registered Users ({{ count($users) }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 py-2 fw-semibold" id="ai-tab" data-bs-toggle="tab" data-bs-target="#ai" type="button" role="tab">
                <i class="bi bi-robot me-1 text-primary"></i> AI Risk Analyst
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content pt-2" id="adminTabsContent">
        
        <!-- Ports Management -->
        <div class="tab-pane fade show active" id="ports" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-8">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-table me-2 text-primary"></i> Registered Maritime Ports</h5>
                    <div class="table-responsive" style="max-height: 440px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Port Name</th>
                                    <th>City & Country</th>
                                    <th>Status</th>
                                    <th>Risk Score</th>
                                    <th class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ports as $port)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold text-dark"><i class="bi bi-anchor text-primary me-1"></i> {{ $port->name }}</div>
                                    </td>
                                    <td>
                                        <span class="text-secondary small">{{ $port->city }}, {{ $port->country->name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $port->status === 'Normal' ? 'bg-success-subtle text-success border border-success-subtle' : ($port->status === 'Busy' ? 'bg-warning-subtle text-warning border border-warning-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle') }} px-3 py-1 rounded-pill">
                                            {{ $port->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $port->risk_score }}</span><span class="text-muted small">/100</span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <form action="{{ route('admin.ports.destroy', $port->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width: 32px; height: 32px;" onclick="return confirm('Delete this port entry?')" title="Delete Port">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="p-3 bg-light rounded-4 border">
                        <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-plus-circle-fill me-1 text-primary"></i> Add New Maritime Port</h6>
                        <form method="POST" action="{{ route('admin.ports.store') }}">
                            @csrf
                            <div class="mb-2">
                                <label class="small fw-semibold text-secondary">Country</label>
                                <select name="country_id" class="form-select form-select-sm" required>
                                    @foreach($countries as $c)
                                    <option value="{{ $c->id }}">{{ $c->flag }} {{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-semibold text-secondary">Port Name</label>
                                <input type="text" name="name" class="form-control form-control-sm" required placeholder="e.g. Port of Rotterdam">
                            </div>
                            <div class="mb-2">
                                <label class="small fw-semibold text-secondary">City</label>
                                <input type="text" name="city" class="form-control form-control-sm" required placeholder="e.g. Rotterdam">
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="small fw-semibold text-secondary">Latitude</label>
                                    <input type="number" step="any" name="latitude" class="form-control form-control-sm" required placeholder="e.g. 51.95">
                                </div>
                                <div class="col-6">
                                    <label class="small fw-semibold text-secondary">Longitude</label>
                                    <input type="number" step="any" name="longitude" class="form-control form-control-sm" required placeholder="e.g. 4.12">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-semibold text-secondary">Congestion Status</label>
                                <select name="status" class="form-select form-select-sm" required>
                                    <option>Normal</option>
                                    <option>Busy</option>
                                    <option>Delay</option>
                                    <option>Congested</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-semibold text-secondary">Base Risk Score (0-100)</label>
                                <input type="number" name="risk_score" class="form-control form-control-sm" value="20" min="0" max="100" required>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill py-2 fw-semibold">
                                <i class="bi bi-check-lg me-1"></i> Save New Port
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sentiment Lexicon Management -->
        <div class="tab-pane fade" id="lexicon" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="p-3 bg-light rounded-4 border">
                        <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-plus-circle-fill me-1 text-primary"></i> Add Lexicon Word</h6>
                        <form method="POST" action="{{ route('admin.words.store') }}">
                            @csrf
                            <div class="mb-2">
                                <label class="small fw-semibold text-secondary">Word (English)</label>
                                <input type="text" name="word" class="form-control form-control-sm" required placeholder="e.g. bottleneck">
                            </div>
                            <div class="mb-3">
                                <label class="small fw-semibold text-secondary">Lexicon Sentiment Type</label>
                                <select name="type" class="form-select form-select-sm" required>
                                    <option value="positive">🟢 Positive Lexicon Word</option>
                                    <option value="negative">🔴 Negative Lexicon Word</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill py-2 fw-semibold">
                                <i class="bi bi-plus-lg me-1"></i> Add Word to Dictionary
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <h6 class="fw-bold mb-3 text-success"><i class="bi bi-hand-thumbs-up-fill me-1"></i> Positive Words ({{ count($posWords) }})</h6>
                    <div style="max-height: 420px; overflow-y: auto;" class="border p-3 rounded-4 bg-white">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($posWords as $w)
                            <span class="badge bg-success-subtle text-success border border-success-subtle p-2 d-flex align-items-center rounded-pill">
                                {{ $w->word }}
                                <form action="{{ route('admin.words.destroy', ['type' => 'positive', 'id' => $w->id]) }}" method="POST" class="d-inline ms-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="border-0 bg-transparent text-danger p-0" onclick="return confirm('Delete word?')" title="Remove">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </form>
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <h6 class="fw-bold mb-3 text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> Negative Words ({{ count($negWords) }})</h6>
                    <div style="max-height: 420px; overflow-y: auto;" class="border p-3 rounded-4 bg-white">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($negWords as $w)
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle p-2 d-flex align-items-center rounded-pill">
                                {{ $w->word }}
                                <form action="{{ route('admin.words.destroy', ['type' => 'negative', 'id' => $w->id]) }}" method="POST" class="d-inline ms-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="border-0 bg-transparent text-danger p-0" onclick="return confirm('Delete word?')" title="Remove">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </form>
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Articles Management -->
        <div class="tab-pane fade" id="articles" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-8">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-newspaper me-2 text-primary"></i> Published Risk & Analysis Articles</h5>
                    <div style="max-height: 440px; overflow-y: auto;" class="d-flex flex-column gap-3 pe-2">
                        @foreach($articles as $art)
                        <div class="p-3 border rounded-4 bg-white shadow-xs position-relative">
                            <h6 class="fw-bold text-dark mb-1 me-4">{{ $art->title }}</h6>
                            <p class="text-secondary small mb-2 line-clamp-2">{{ Str::limit($art->content, 180) }}</p>
                            <div class="d-flex align-items-center gap-3 small text-muted">
                                <span><i class="bi bi-person me-1"></i> By {{ $art->author }}</span>
                                <span><i class="bi bi-calendar3 me-1"></i> {{ $art->published_at ? $art->published_at->format('d M Y H:i') : 'N/A' }}</span>
                            </div>
                            
                            <form action="{{ route('admin.articles.destroy', $art->id) }}" method="POST" class="position-absolute top-0 end-0 mt-3 me-3">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width: 32px; height: 32px;" onclick="return confirm('Delete article?')" title="Delete Article">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="p-3 bg-light rounded-4 border">
                        <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-pencil-square me-1 text-primary"></i> Publish Analysis Article</h6>
                        <form method="POST" action="{{ route('admin.articles.store') }}">
                            @csrf
                            <div class="mb-2">
                                <label class="small fw-semibold text-secondary">Article Title</label>
                                <input type="text" name="title" class="form-control form-control-sm" required placeholder="e.g. Red Sea Shipping Route Analysis">
                            </div>
                            <div class="mb-2">
                                <label class="small fw-semibold text-secondary">Author</label>
                                <input type="text" name="author" class="form-control form-control-sm" value="System Administrator" required>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-semibold text-secondary">Article Content</label>
                                <textarea name="content" rows="6" class="form-control form-control-sm" required placeholder="Write detailed risk analysis content..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill py-2 fw-semibold">
                                <i class="bi bi-send me-1"></i> Publish Article
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users List -->
        <div class="tab-pane fade" id="users" role="tabpanel">
            <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-people me-2 text-primary"></i> Registered Platform Accounts</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">User Name</th>
                            <th>Email Address</th>
                            <th>Account Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-bold text-dark">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-secondary">{{ $user->email }}</span>
                            </td>
                            <td>
                                <span class="text-muted small"><i class="bi bi-clock me-1"></i> {{ $user->created_at ? $user->created_at->format('d M Y H:i') : 'N/A' }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- AI Analyst Console -->
        <div class="tab-pane fade" id="ai" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="p-4 rounded-4 bg-light border shadow-xs">
                        <h5 class="fw-bold mb-2 text-dark"><i class="bi bi-cpu-fill text-primary me-2"></i>AI Report Generator</h5>
                        <p class="small text-muted mb-3">Pilih negara untuk menghasilkan ringkasan eksekutif analisis risiko maritim & komoditas menggunakan AI Neural Engine.</p>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Target Country Jurisdiction</label>
                            <select id="aiCountrySelect" class="form-select rounded-3">
                                @foreach($countries as $c)
                                    <option value="{{ $c->id }}">{{ $c->flag }} {{ $c->name }} (Risk: {{ $c->risk_score }}/100)</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" id="btnGenerateAiReport" onclick="generateAiReport()" class="btn btn-primary rounded-3 w-100 py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-magic fs-5"></i>
                            <span>Generate AI Executive Report</span>
                        </button>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div id="aiReportCard" class="p-4 rounded-4 bg-white border shadow-sm">
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-robot fs-1 d-block mb-2 text-primary"></i>
                            <h6 class="fw-semibold">AI Risk Executive Analyst Console</h6>
                            <p class="small mb-0">Pilih negara di panel sebelah kiri lalu klik "Generate AI Executive Report" untuk membuat laporan analisis intelijen otomatis.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
function generateAiReport() {
    const countryId = document.getElementById('aiCountrySelect').value;
    const btn = document.getElementById('btnGenerateAiReport');
    const container = document.getElementById('aiReportCard');

    btn.disabled = true;
    btn.innerHTML = `<i class="bi bi-arrow-repeat spin"></i> Processing AI Analysis...`;

    fetch('/ai/generate-report', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ country_id: countryId })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = `<i class="bi bi-magic fs-5"></i> <span>Generate AI Executive Report</span>`;

        if (data.status === 'success') {
            const r = data.report;
            container.innerHTML = `
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-2">${r.flag}</span>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">Executive Risk Intelligence Report: ${r.country_name}</h5>
                            <small class="text-muted" style="font-size: 11px;">Engine: ${r.ai_model_name} • ${r.generated_at}</small>
                        </div>
                    </div>
                    <span class="badge ${r.risk_score >= 65 ? 'bg-danger' : (r.risk_score >= 35 ? 'bg-warning' : 'bg-success')} px-3 py-2 rounded-pill fs-6">
                        Risk Score: ${r.risk_score}/100 (${r.risk_level})
                    </span>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <div class="p-3 rounded-3 bg-light border">
                            <strong class="text-primary d-block mb-1"><i class="bi bi-shield-exclamation me-1"></i> Executive Threat Assessment</strong>
                            <p class="mb-0 text-dark small">${r.summary}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-white shadow-xs h-100">
                            <strong class="text-dark d-block mb-1"><i class="bi bi-cloud-sun text-warning me-1"></i> Meteorological Impact</strong>
                            <p class="mb-0 text-muted small">${r.weather_impact}</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-white shadow-xs h-100">
                            <strong class="text-dark d-block mb-1"><i class="bi bi-currency-exchange text-success me-1"></i> Financial & FX Impact</strong>
                            <p class="mb-0 text-muted small">${r.financial_impact}</p>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="p-3 rounded-3 bg-primary-subtle border border-primary-subtle text-primary">
                            <strong class="d-block mb-1"><i class="bi bi-lightbulb-fill me-1"></i> Actionable Recommendation for Logistics & Trade Managers</strong>
                            <p class="mb-0 text-dark small fw-medium">${r.actionable_recommendation}</p>
                        </div>
                    </div>
                </div>
            `;
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = `<i class="bi bi-magic fs-5"></i> <span>Generate AI Executive Report</span>`;
    });
}
</script>
@endsection
