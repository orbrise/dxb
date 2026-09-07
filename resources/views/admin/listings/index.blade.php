@extends("admin.layout.master")

@push('css')
<style>
/* ===== Categories/Listings page: modern redesign ===== */
:root {
    --lst-primary: #6366f1;
    --lst-primary-dark: #4f46e5;
    --lst-success: #10b981;
    --lst-danger: #ef4444;
    --lst-warning: #f59e0b;
    --lst-info: #06b6d4;
    --lst-slate-50: #f8fafc;
    --lst-slate-100: #f1f5f9;
    --lst-slate-200: #e2e8f0;
    --lst-slate-300: #cbd5e1;
    --lst-slate-500: #64748b;
    --lst-slate-600: #475569;
    --lst-slate-700: #334155;
    --lst-slate-800: #1e293b;
}

/* Stats strip */
.lst-stats-row {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin: 4px 0 18px;
}
.lst-stat {
    position: relative;
    padding: 16px 18px;
    border-radius: 12px;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(15,23,42,.08);
}
.lst-stat .lst-stat-label {
    font-size: 12px; text-transform: uppercase; letter-spacing: .06em;
    opacity: .9; margin: 0 0 4px; font-weight: 600;
}
.lst-stat .lst-stat-value {
    font-size: 26px; font-weight: 700; line-height: 1.1; margin: 0;
}
.lst-stat .lst-stat-icon {
    position: absolute; right: 14px; top: 50%;
    transform: translateY(-50%); font-size: 34px; opacity: .35;
}
.lst-stat.total  { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.lst-stat.latest { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.lst-stat.action { background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); }

/* Alerts */
.lst-alert-success {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 13px; font-weight: 500;
    margin-bottom: 14px;
    background: #d1fae5; color: #065f46;
}

/* Toolbar */
.lst-toolbar {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 14px;
}
.lst-add-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, var(--lst-primary) 0%, var(--lst-primary-dark) 100%);
    color: #fff !important;
    border: 0;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(99, 102, 241, .35);
    transition: box-shadow .15s;
}
.lst-add-btn:hover { color: #fff; box-shadow: 0 6px 18px rgba(99, 102, 241, .5); text-decoration: none; }

/* Main card */
.lst-card {
    background: #fff;
    border: 1px solid var(--lst-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
}
.lst-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 18px 22px;
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--lst-slate-100);
    flex-wrap: wrap;
}
.lst-card-head .lst-title {
    display: flex; align-items: center; gap: 10px;
    margin: 0;
    font-size: 16px; font-weight: 700; color: var(--lst-slate-800);
}
.lst-card-head .lst-title-icon {
    width: 34px; height: 34px; border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(99, 102, 241, .12); color: var(--lst-primary-dark); font-size: 15px;
}
.lst-card-head .lst-title small {
    display: block; font-weight: 400;
    color: var(--lst-slate-500); font-size: 12px; margin-top: 2px;
}
.lst-perpage {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--lst-slate-600);
}
.lst-perpage select {
    border: 1px solid var(--lst-slate-200);
    border-radius: 8px;
    padding: 5px 26px 5px 10px;
    font-size: 13px;
    background: #fff;
    color: var(--lst-slate-700);
    cursor: pointer;
    height: auto;
    width: auto;
}
.lst-perpage select:focus {
    outline: none;
    border-color: var(--lst-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .18);
}

/* Table */
.lst-table {
    width: 100% !important;
    margin: 0;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    font-size: 13.5px;
}
.lst-table thead th {
    background: var(--lst-slate-50);
    color: var(--lst-slate-500);
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    padding: 14px 16px;
    border: 0;
    border-bottom: 1px solid var(--lst-slate-200);
    text-align: left;
    white-space: nowrap;
}
.lst-table thead th:last-child { text-align: right; }
.lst-table tbody td {
    padding: 14px 16px;
    vertical-align: middle;
    border: 0;
    border-bottom: 1px solid var(--lst-slate-100);
    color: var(--lst-slate-700);
    background: #fff;
}
.lst-table tbody tr:hover td { background: #fafbff; }
.lst-table tbody tr:last-child td { border-bottom: 0; }

.lst-id-chip {
    display: inline-block;
    padding: 3px 10px;
    font-size: 12px; font-weight: 700;
    border-radius: 6px;
    color: var(--lst-slate-500);
    background: var(--lst-slate-100);
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.lst-name-cell {
    display: flex; align-items: center; gap: 10px;
}
.lst-name-avatar {
    width: 34px; height: 34px; border-radius: 50%;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: #fff; display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; text-transform: uppercase;
    flex-shrink: 0;
}
.lst-name-text {
    font-weight: 600; color: var(--lst-slate-800);
    text-transform: capitalize;
}
.lst-date {
    color: var(--lst-slate-600);
    font-size: 13px;
    line-height: 1.3;
    white-space: nowrap;
}
.lst-date small {
    display: block; color: var(--lst-slate-500); font-size: 11px;
}

/* Actions */
.lst-actions-cell {
    display: inline-flex; gap: 6px;
    align-items: center;
    justify-content: flex-end;
}
.lst-actions-cell form { margin: 0; display: inline; }
.lst-btn-edit {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 14px;
    background: #eef2ff;
    color: var(--lst-primary-dark) !important;
    border: 0;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: background .15s;
    line-height: 1;
}
.lst-btn-edit:hover { background: #e0e7ff; color: var(--lst-primary-dark) !important; text-decoration: none; }
.lst-btn-delete {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 14px;
    background: #fee2e2;
    color: #991b1b !important;
    border: 0;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s;
    line-height: 1;
}
.lst-btn-delete:hover { background: #fecaca; color: #991b1b !important; }

/* Empty */
.lst-empty td {
    text-align: center !important;
    padding: 50px 20px !important;
    color: var(--lst-slate-500) !important;
}

/* Pagination footer */
.lst-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    padding: 16px 22px;
    border-top: 1px solid var(--lst-slate-100);
    background: var(--lst-slate-50);
    font-size: 13px;
    color: var(--lst-slate-600);
}
.lst-pagination .pagination { margin: 0; }
.lst-pagination .pagination .page-link {
    color: var(--lst-slate-600);
    border-color: var(--lst-slate-200);
    padding: 6px 12px;
    font-size: 13px;
    margin: 0 2px;
    border-radius: 7px !important;
}
.lst-pagination .pagination .page-item.active .page-link {
    background: var(--lst-primary);
    border-color: var(--lst-primary);
    color: #fff;
}

@media (max-width: 992px) {
    .lst-stats-row { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    .lst-card-head { flex-direction: column; align-items: flex-start; }
    .lst-table thead { display: none; }
    .lst-pagination { flex-direction: column; text-align: center; }
    .lst-add-btn { width: 100%; justify-content: center; }
}
</style>
@endpush

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Categories</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Manage Categories effectively</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Categories</li>
        </ol>
    </div>
</div>

@php
    $totalCats = $listings->total();
    $latestName = $listings->count() > 0 ? $listings->first()->name : '—';
@endphp

<div class="container-fluid px-0">

    @if(session('success'))
        <div class="lst-alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="lst-stats-row">
        <div class="lst-stat total">
            <p class="lst-stat-label">Total Categories</p>
            <p class="lst-stat-value">{{ number_format($totalCats) }}</p>
            <i class="fas fa-th-large lst-stat-icon"></i>
        </div>
        <div class="lst-stat latest">
            <p class="lst-stat-label">Latest Added</p>
            <p class="lst-stat-value" style="font-size:18px; padding-top:4px;">
                {{ \Illuminate\Support\Str::limit(ucfirst($latestName), 24) }}
            </p>
            <i class="fas fa-clock lst-stat-icon"></i>
        </div>
        <div class="lst-stat action">
            <p class="lst-stat-label">Quick Action</p>
            <p class="lst-stat-value" style="font-size:16px; padding-top:6px;">Add or Edit Categories</p>
            <i class="fas fa-bolt lst-stat-icon"></i>
        </div>
    </div>

    {{-- Add button --}}
    <div class="lst-toolbar">
        <a href="{{ route('admin.listings.create') }}" class="lst-add-btn">
            <i class="fas fa-plus"></i> Add New Category
        </a>
    </div>

    {{-- Main card --}}
    <div class="lst-card">
        <div class="lst-card-head">
            <h5 class="lst-title">
                <span class="lst-title-icon"><i class="fas fa-th-large"></i></span>
                All Categories
                <small>{{ $totalCats }} {{ \Illuminate\Support\Str::plural('category', $totalCats) }} available</small>
            </h5>
            <form method="GET" action="{{ route('admin.listings.index') }}">
                <label class="lst-perpage mb-0">
                    <span>Per page</span>
                    @php $pp = request('perPage', $listings->perPage()); @endphp
                    <select name="perPage" onchange="this.form.submit()">
                        <option value="10"  {{ $pp==10  ? 'selected' : '' }}>10</option>
                        <option value="25"  {{ $pp==25  ? 'selected' : '' }}>25</option>
                        <option value="50"  {{ $pp==50  ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $pp==100 ? 'selected' : '' }}>100</option>
                    </select>
                </label>
            </form>
        </div>

        <div style="overflow-x: auto;">
            <table class="table lst-table">
                <thead>
                    <tr>
                        <th style="width:80px;">ID</th>
                        <th>Name</th>
                        <th style="width:200px;">Created At</th>
                        <th style="width:200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($listings as $listing)
                    <tr>
                        <td><span class="lst-id-chip">#{{ $listing->id }}</span></td>
                        <td>
                            <div class="lst-name-cell">
                                <span class="lst-name-avatar">{{ strtoupper(mb_substr($listing->name, 0, 1)) }}</span>
                                <span class="lst-name-text">{{ $listing->name }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $c = $listing->created_at;
                            @endphp
                            @if($c)
                                <div class="lst-date">
                                    {{ \Carbon\Carbon::parse($c)->format('M d, Y') }}
                                    <small>{{ \Carbon\Carbon::parse($c)->format('H:i') }}</small>
                                </div>
                            @else
                                <span style="color:#cbd5e1;">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="lst-actions-cell">
                                <a href="{{ route('admin.listings.edit', $listing) }}" class="lst-btn-edit">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>
                                <form action="{{ route('admin.listings.destroy', $listing) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="lst-btn-delete" onclick="return confirm('Are you sure?')">
                                        <i class="fa-solid fa-trash-can"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="lst-empty">
                        <td colspan="4">
                            <div style="width:80px; height:80px; border-radius:50%; background:#f1f5f9; color:#cbd5e1; font-size:32px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:14px;">
                                <i class="fas fa-th-large"></i>
                            </div>
                            <p style="color:#64748b; margin:0; font-size:15px;">No categories yet — click "Add New Category" to create one.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="lst-pagination">
            <div>
                @if($listings->total() > 0)
                    Showing <strong>{{ $listings->firstItem() }}</strong> to
                    <strong>{{ $listings->lastItem() }}</strong> of
                    <strong>{{ number_format($listings->total()) }}</strong> entries
                @else
                    No categories found
                @endif
            </div>
            <div>{{ $listings->appends(request()->query())->links() }}</div>
        </div>
    </div>
</div>
@endsection
