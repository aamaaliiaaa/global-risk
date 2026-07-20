@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<div class="page-header">
    <div>
        <h1 class="dashboard-title">Admin Management System</h1>
        <p class="page-subtitle">
            Manage user accounts, port datasets, sentiment lexicon dictionaries, and logistics articles.
        </p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success rounded-3 mb-4">{{ session('success') }}</div>
@endif

<div class="dashboard-card">
    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-3 px-4 py-2" id="ports-tab" data-bs-toggle="tab" data-bs-target="#ports" type="button" role="tab" aria-controls="ports" aria-selected="true">
                <i class="bi bi-geo-alt"></i> Ports Dataset
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 px-4 py-2" id="lexicon-tab" data-bs-toggle="tab" data-bs-target="#lexicon" type="button" role="tab" aria-controls="lexicon" aria-selected="false">
                <i class="bi bi-spellcheck"></i> Sentiment Lexicon
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 px-4 py-2" id="articles-tab" data-bs-toggle="tab" data-bs-target="#articles" type="button" role="tab" aria-controls="articles" aria-selected="false">
                <i class="bi bi-file-earmark-text"></i> Articles
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 px-4 py-2" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="false">
                <i class="bi bi-people"></i> Users
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="adminTabsContent">
        
        <!-- Ports Management -->
        <div class="tab-pane fade show active" id="ports" role="tabpanel" aria-labelledby="ports-tab">
            <div class="row">
                <div class="col-md-8">
                    <h5>Registered Ports</h5>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle small">
                            <thead class="table-light">
                                <tr>
                                    <th>Port Name</th>
                                    <th>City</th>
                                    <th>Country</th>
                                    <th>Status</th>
                                    <th>Risk Score</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ports as $port)
                                <tr>
                                    <td>🚢 <strong>{{ $port->name }}</strong></td>
                                    <td>{{ $port->city }}</td>
                                    <td>{{ $port->country->name }}</td>
                                    <td>
                                        <span class="badge {{ $port->status === 'Normal' ? 'bg-success' : ($port->status === 'Busy' ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $port->status }}
                                        </span>
                                    </td>
                                    <td>{{ $port->risk_score }}/100</td>
                                    <td>
                                        <form action="{{ route('admin.ports.destroy', $port->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete port?')">
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
                
                <div class="col-md-4">
                    <div class="bg-light p-3 rounded-4 border">
                        <h5>Add New Port</h5>
                        <form method="POST" action="{{ route('admin.ports.store') }}">
                            @csrf
                            <div class="mb-2">
                                <label class="small fw-semibold">Country</label>
                                <select name="country_id" class="form-select form-select-sm" required>
                                    @foreach($countries as $c)
                                    <option value="{{ $c->id }}">{{ $c->flag }} {{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-semibold">Port Name</label>
                                <input type="text" name="name" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-semibold">City</label>
                                <input type="text" name="city" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-semibold">Latitude</label>
                                <input type="number" step="any" name="latitude" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-semibold">Longitude</label>
                                <input type="number" step="any" name="longitude" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-semibold">Status</label>
                                <select name="status" class="form-select form-select-sm" required>
                                    <option>Normal</option>
                                    <option>Busy</option>
                                    <option>Delay</option>
                                    <option>Congested</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-semibold">Base Risk Score (0-100)</label>
                                <input type="number" name="risk_score" class="form-control form-control-sm" value="20" min="0" max="100" required>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary w-100 rounded-3">Save Port</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sentiment Lexicon Management -->
        <div class="tab-pane fade" id="lexicon" role="tabpanel" aria-labelledby="lexicon-tab">
            <div class="row">
                <div class="col-md-4">
                    <div class="bg-light p-3 rounded-4 border mb-3">
                        <h5>Add Word to Dictionary</h5>
                        <form method="POST" action="{{ route('admin.words.store') }}">
                            @csrf
                            <div class="mb-2">
                                <label class="small fw-semibold">Word (English)</label>
                                <input type="text" name="word" class="form-control form-control-sm" required placeholder="e.g. disaster">
                            </div>
                            <div class="mb-3">
                                <label class="small fw-semibold">Type</label>
                                <select name="type" class="form-select form-select-sm" required>
                                    <option value="positive">Positive Word</option>
                                    <option value="negative">Negative Word</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary w-100 rounded-3">Add Word</button>
                        </form>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <h5>Positive Words Dictionary</h5>
                    <div style="max-height: 400px; overflow-y: auto;" class="border p-2 rounded">
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($posWords as $w)
                            <span class="badge bg-success-subtle text-success p-2 d-flex align-items-center">
                                {{ $w->word }}
                                <form action="{{ route('admin.words.destroy', ['type' => 'positive', 'id' => $w->id]) }}" method="POST" style="display:inline;" class="ms-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="border-0 bg-transparent text-danger p-0 small" onclick="return confirm('Delete word?')">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </form>
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <h5>Negative Words Dictionary</h5>
                    <div style="max-height: 400px; overflow-y: auto;" class="border p-2 rounded">
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($negWords as $w)
                            <span class="badge bg-danger-subtle text-danger p-2 d-flex align-items-center">
                                {{ $w->word }}
                                <form action="{{ route('admin.words.destroy', ['type' => 'negative', 'id' => $w->id]) }}" method="POST" style="display:inline;" class="ms-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="border-0 bg-transparent text-danger p-0 small" onclick="return confirm('Delete word?')">
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
        <div class="tab-pane fade" id="articles" role="tabpanel" aria-labelledby="articles-tab">
            <div class="row">
                <div class="col-md-8">
                    <h5>Logistics & Shipping Articles</h5>
                    <div style="max-height: 400px; overflow-y: auto;" class="pe-2">
                        @foreach($articles as $art)
                        <div class="border p-3 rounded-4 mb-3 position-relative">
                            <h6 class="fw-bold">{{ $art->title }}</h6>
                            <p class="text-secondary small">{{ Str::limit($art->content, 200) }}</p>
                            <small class="text-muted">By: {{ $art->author }} | {{ $art->published_at ? $art->published_at->format('d M Y') : 'N/A' }}</small>
                            
                            <form action="{{ route('admin.articles.destroy', $art->id) }}" method="POST" class="position-absolute top-0 end-0 mt-3 me-3">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete article?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="bg-light p-3 rounded-4 border">
                        <h5>Publish Analysis Article</h5>
                        <form method="POST" action="{{ route('admin.articles.store') }}">
                            @csrf
                            <div class="mb-2">
                                <label class="small fw-semibold">Article Title</label>
                                <input type="text" name="title" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-semibold">Author</label>
                                <input type="text" name="author" class="form-control form-control-sm" value="System Administrator" required>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-semibold">Content</label>
                                <textarea name="content" rows="6" class="form-control form-control-sm" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary w-100 rounded-3">Publish Article</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users List -->
        <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
            <h5>Registered Accounts</h5>
            <table class="table table-hover align-middle mt-3">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at ? $user->created_at->format('d M Y H:i') : 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>

@endsection
