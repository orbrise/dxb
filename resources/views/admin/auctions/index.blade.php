@extends("admin.layout.master")

@push('css')
<style>
/* ===== Auctions page: modern redesign ===== */
:root {
    --a-primary: #6366f1;
    --a-primary-dark: #4f46e5;
    --a-success: #10b981;
    --a-danger: #ef4444;
    --a-warning: #f59e0b;
    --a-info: #06b6d4;
    --a-slate-50: #f8fafc;
    --a-slate-100: #f1f5f9;
    --a-slate-200: #e2e8f0;
    --a-slate-300: #cbd5e1;
    --a-slate-500: #64748b;
    --a-slate-600: #475569;
    --a-slate-700: #334155;
    --a-slate-800: #1e293b;
}

/* Stats strip */
.a-stats-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin: 4px 0 18px;
}
.a-stat {
    position: relative;
    padding: 16px 18px;
    border-radius: 12px;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(15,23,42,.08);
}
.a-stat .a-stat-label {
    font-size: 12px; text-transform: uppercase; letter-spacing: .06em;
    opacity: .9; margin: 0 0 4px; font-weight: 600;
}
.a-stat .a-stat-value {
    font-size: 26px; font-weight: 700; line-height: 1.1; margin: 0;
}
.a-stat .a-stat-icon {
    position: absolute; right: 14px; top: 50%;
    transform: translateY(-50%); font-size: 34px; opacity: .35;
}
.a-stat.total  { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.a-stat.active { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.a-stat.ended  { background: linear-gradient(135deg, #64748b 0%, #334155 100%); }
.a-stat.value  { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); }

/* Header row w/ Create button */
.a-page-actions {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 14px;
}
.a-create-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, var(--a-primary) 0%, var(--a-primary-dark) 100%);
    color: #fff !important;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(99, 102, 241, .35);
    transition: box-shadow .15s;
    border: 0;
}
.a-create-btn:hover { color: #fff; box-shadow: 0 6px 18px rgba(99, 102, 241, .5); text-decoration: none; }

/* Main card */
.a-card {
    background: #fff;
    border: 1px solid var(--a-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
}
.a-card-head {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 18px 22px;
    border-bottom: 1px solid var(--a-slate-100);
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
}
.a-card-head .a-title-icon {
    width: 34px; height: 34px; border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(99, 102, 241, .12); color: var(--a-primary-dark); font-size: 15px;
}
.a-card-head .a-title {
    font-size: 16px; font-weight: 700; color: var(--a-slate-800); margin: 0;
}
.a-card-head .a-title small {
    font-weight: 400; color: var(--a-slate-500); font-size: 12px; margin-left: 4px;
}

/* Filter section */
.a-filters {
    padding: 18px 22px 8px;
    background: var(--a-slate-50);
    border-bottom: 1px solid var(--a-slate-100);
}
.a-filters-grid {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr)) auto;
    gap: 12px;
    align-items: end;
}
.a-field label {
    display: flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .04em;
    color: var(--a-slate-500);
    margin: 0 0 6px 2px;
}
.a-field label i { color: var(--a-primary); font-size: 11px; }
.a-input, .a-filters select.form-control {
    width: 100%; height: 40px;
    padding: 8px 12px;
    font-size: 13px;
    color: var(--a-slate-800);
    background: #fff;
    border: 1px solid var(--a-slate-200);
    border-radius: 9px;
    transition: border-color .15s, box-shadow .15s;
}
.a-input::placeholder { color: var(--a-slate-300); }
.a-input:focus, .a-filters select.form-control:focus {
    outline: none;
    border-color: var(--a-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.a-actions {
    display: flex;
    gap: 8px;
    align-items: end;
}
.a-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    height: 40px;
    padding: 0 16px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid transparent;
    cursor: pointer;
    white-space: nowrap;
    transition: box-shadow .15s;
    text-decoration: none;
}
.a-btn-primary {
    background: linear-gradient(135deg, var(--a-primary) 0%, var(--a-primary-dark) 100%);
    color: #fff;
    box-shadow: 0 4px 12px rgba(99, 102, 241, .35);
}
.a-btn-primary:hover { color: #fff; box-shadow: 0 6px 16px rgba(99, 102, 241, .45); }
.a-btn-ghost {
    background: #fff;
    color: var(--a-slate-600);
    border-color: var(--a-slate-200);
}
.a-btn-ghost:hover { background: var(--a-slate-50); color: var(--a-slate-800); text-decoration: none; }

/* City autocomplete */
#city_search { padding-right: 34px; }
.a-city-wrap { position: relative; }
.a-city-wrap::after {
    content: "\f002";
    font-family: "Font Awesome 5 Free", "Font Awesome 6 Free";
    font-weight: 900;
    position: absolute;
    right: 12px; top: 50%; transform: translateY(-50%);
    color: var(--a-slate-300); font-size: 12px;
    pointer-events: none;
}
#city_results {
    display: none;
    position: absolute; top: calc(100% + 4px); left: 0; right: 0;
    max-height: 240px; overflow-y: auto;
    background: #fff;
    border: 1px solid var(--a-slate-200);
    border-radius: 10px;
    box-shadow: 0 12px 32px rgba(15,23,42,.15);
    padding: 6px;
    z-index: 1050;
}
#city_results .dropdown-item {
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 13px;
    color: var(--a-slate-700);
    cursor: pointer;
}
#city_results .dropdown-item:hover { background: var(--a-slate-50); color: var(--a-slate-800); }

/* Table */
.a-table {
    width: 100%;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 13.5px;
}
.a-table thead th {
    background: var(--a-slate-50);
    color: var(--a-slate-500);
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    padding: 14px 14px;
    border: 0;
    border-bottom: 1px solid var(--a-slate-200);
    text-align: left;
    white-space: nowrap;
}
.a-table thead th:last-child { text-align: right; }
.a-table tbody td {
    padding: 14px;
    vertical-align: middle;
    border: 0;
    border-bottom: 1px solid var(--a-slate-100);
    color: var(--a-slate-700);
    background: #fff;
}
.a-table tbody tr:hover td { background: #fafbff; }
.a-table tbody tr:last-child td { border-bottom: 0; }

.a-id-chip {
    display: inline-block;
    padding: 3px 10px;
    font-size: 12px; font-weight: 700;
    border-radius: 6px;
    color: var(--a-slate-500);
    background: var(--a-slate-100);
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.a-spot-badge {
    display: inline-flex;
    align-items: center; justify-content: center;
    min-width: 40px; height: 40px;
    padding: 0 12px;
    border-radius: 10px;
    background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
    color: var(--a-primary-dark);
    font-weight: 700; font-size: 14px;
    box-shadow: inset 0 -2px 0 rgba(79, 70, 229, .1);
}
.a-city-cell {
    display: inline-flex;
    align-items: center; gap: 6px;
    color: var(--a-slate-800);
    font-weight: 600;
}
.a-city-cell i { color: var(--a-primary); font-size: 12px; }
.a-price {
    display: inline-block;
    padding: 4px 10px;
    background: #d1fae5;
    color: #065f46;
    border-radius: 6px;
    font-weight: 700;
    font-size: 14px;
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.a-date {
    color: var(--a-slate-700);
    font-size: 13px;
    line-height: 1.3;
    white-space: nowrap;
}
.a-date small { display: block; color: var(--a-slate-500); font-size: 11px; }
.a-time-left {
    display: inline-block;
    padding: 3px 9px;
    background: var(--a-slate-100);
    color: var(--a-slate-600);
    border-radius: 6px;
    font-size: 12px; font-weight: 500;
    white-space: nowrap;
}
.a-time-left.urgent { background: #fef3c7; color: #92400e; }
.a-time-left.ended { background: #fee2e2; color: #991b1b; }

/* Gender badges */
.a-gender-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}
.a-gender-badge i { font-size: 10px; }
.a-gender-female  { background: #fce7f3; color: #9d174d; }
.a-gender-male    { background: #dbeafe; color: #1e40af; }
.a-gender-shemale { background: #ede9fe; color: #5b21b6; }

/* Status badges */
.a-status {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px; font-weight: 600;
}
.a-status i { font-size: 10px; }
.a-status-active { background: #d1fae5; color: #065f46; }
.a-status-ended  { background: #f1f5f9; color: #475569; }

/* Bids link */
.a-bids-link {
    display: inline-flex;
    align-items: center; gap: 6px;
    padding: 4px 12px;
    background: #eef2ff;
    color: var(--a-primary-dark) !important;
    border-radius: 20px;
    font-size: 12px; font-weight: 600;
    text-decoration: none;
    transition: background .15s;
}
.a-bids-link:hover { background: #e0e7ff; text-decoration: none; color: var(--a-primary-dark) !important; }
.a-bids-link.zero { background: var(--a-slate-100); color: var(--a-slate-500) !important; }

/* Winner cell */
.a-winner-cell {
    display: inline-flex;
    align-items: center; gap: 8px;
}
.a-winner-avatar {
    width: 30px; height: 30px; border-radius: 50%;
    background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
    color: #fff; display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 12px;
    text-transform: uppercase;
}
.a-winner-name { font-weight: 600; color: var(--a-slate-800); font-size: 13px; }

/* Action buttons */
.a-actions-cell {
    display: inline-flex; gap: 6px;
    align-items: center;
    justify-content: flex-end;
}
.a-actions-cell form { margin: 0; display: inline; }
.a-icon-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px; height: 36px;
    border-radius: 8px;
    border: 0;
    font-size: 13px;
    cursor: pointer;
    text-decoration: none;
    transition: background .15s, box-shadow .15s;
}
.a-icon-btn:hover { box-shadow: 0 3px 8px rgba(0,0,0,.12); text-decoration: none; }
.a-icon-btn.edit   { background: #eef2ff; color: #4338ca; }
.a-icon-btn.edit:hover { background: #e0e7ff; color: #4338ca; }
.a-icon-btn.end    { background: #fef3c7; color: #92400e; }
.a-icon-btn.end:hover { background: #fde68a; color: #92400e; }
.a-icon-btn.reset  { background: #d1fae5; color: #065f46; }
.a-icon-btn.reset:hover { background: #a7f3d0; color: #065f46; }
.a-icon-btn.delete { background: #fee2e2; color: #991b1b; }
.a-icon-btn.delete:hover { background: #fecaca; color: #991b1b; }

/* Alerts */
.a-alert {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 13px; font-weight: 500;
    margin: 18px 22px 0;
}
.a-alert-success { background: #d1fae5; color: #065f46; }
.a-alert-error   { background: #fee2e2; color: #991b1b; }

/* Empty */
.a-empty {
    text-align: center; padding: 60px 20px;
}
.a-empty-icon {
    width: 80px; height: 80px; border-radius: 50%;
    background: var(--a-slate-100); color: var(--a-slate-300);
    font-size: 32px; display: inline-flex; align-items: center; justify-content: center;
    margin-bottom: 16px;
}

/* Pagination */
.a-pagination {
    padding: 16px 22px;
    border-top: 1px solid var(--a-slate-100);
    background: var(--a-slate-50);
}
.a-pagination .pagination { margin: 0; justify-content: flex-end; }
.a-pagination .pagination .page-link {
    color: var(--a-slate-600);
    border-color: var(--a-slate-200);
    padding: 6px 12px; font-size: 13px;
    margin: 0 2px; border-radius: 7px !important;
}
.a-pagination .pagination .page-item.active .page-link {
    background: var(--a-primary);
    border-color: var(--a-primary);
    color: #fff;
}

/* Responsive */
@media (max-width: 1200px) {
    .a-filters-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .a-actions { grid-column: 1 / -1; justify-content: flex-end; }
}
@media (max-width: 768px) {
    .main-wrapper { padding-left: 8px !important; padding-right: 8px !important; }
    .container-fluid { padding-left: 6px !important; padding-right: 6px !important; }
    .a-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .a-filters { padding: 14px; }
    .a-filters-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .a-actions { grid-column: 1 / -1; justify-content: flex-start; }
    .a-page-actions .a-create-btn { width: 100%; }
    .a-table thead { display: none; }
    .a-table tbody td { padding: 10px 12px; }
    .a-pagination { padding: 12px; }
    .a-pagination .pagination { justify-content: center; }
}
</style>
@endpush

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Auctions</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Manage auctions effectively</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Auctions</li>
        </ol>
    </div>
</div>

@php
    $totalSpots  = method_exists($auctions, 'total') ? $auctions->total() : $auctions->count();
    $activeOnPage = collect($auctions->items() ?? $auctions)->filter(fn($a) => $a->status === 'active')->count();
    $endedOnPage  = collect($auctions->items() ?? $auctions)->filter(fn($a) => $a->status !== 'active')->count();
    $totalValue   = collect($auctions->items() ?? $auctions)->sum('current_price');
@endphp

<div class="container-fluid px-0 mb-3">

    {{-- Stats --}}
    <div class="a-stats-row">
        <div class="a-stat total">
            <p class="a-stat-label">Total Auction Spots</p>
            <p class="a-stat-value">{{ number_format($totalSpots) }}</p>
            <i class="fas fa-gavel a-stat-icon"></i>
        </div>
        <div class="a-stat active">
            <p class="a-stat-label">Active · On This Page</p>
            <p class="a-stat-value">{{ number_format($activeOnPage) }}</p>
            <i class="fas fa-bolt a-stat-icon"></i>
        </div>
        <div class="a-stat ended">
            <p class="a-stat-label">Ended · On This Page</p>
            <p class="a-stat-value">{{ number_format($endedOnPage) }}</p>
            <i class="fas fa-flag-checkered a-stat-icon"></i>
        </div>
        <div class="a-stat value">
            <p class="a-stat-label">Value · On This Page</p>
            <p class="a-stat-value">${{ number_format($totalValue, 0) }}</p>
            <i class="fas fa-dollar-sign a-stat-icon"></i>
        </div>
    </div>

    {{-- Create button --}}
    <div class="a-page-actions">
        <a href="{{ route('admin.auctions.create') }}" class="a-create-btn">
            <i class="fas fa-plus"></i> Create New Auction Spot
        </a>
    </div>

    {{-- Main card --}}
    <div class="a-card">
        <div class="a-card-head">
            <span class="a-title-icon"><i class="fas fa-gavel"></i></span>
            <h5 class="a-title">
                Auction Spots Management
                <small>Filter, edit, end or reset auction spots</small>
            </h5>
        </div>

        @if(session('success'))
            <div class="a-alert a-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="a-alert a-alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif

        {{-- Filters --}}
        <div class="a-filters">
            <form action="{{ route('admin.auctions.index') }}" method="GET">
                <div class="a-filters-grid">
                    <div class="a-field">
                        <label><i class="fas fa-map-marker-alt"></i> City</label>
                        <div class="a-city-wrap position-relative">
                            <input type="text" id="city_search" class="a-input @error('city_id') is-invalid @enderror"
                                   placeholder="Type to search cities..." autocomplete="off">
                            <input type="hidden" name="city_id" id="city_id" value="{{ old('city_id', request('city_id')) }}">
                            <div id="city_results" class="dropdown-menu"></div>
                        </div>
                    </div>

                    <div class="a-field">
                        <label><i class="fas fa-toggle-on"></i> Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="ended"  {{ request('status') == 'ended'  ? 'selected' : '' }}>Ended</option>
                        </select>
                    </div>

                    <div class="a-field">
                        <label><i class="fas fa-venus-mars"></i> Gender</label>
                        <select name="gender" class="form-control">
                            <option value="">All Genders</option>
                            <option value="female"  {{ request('gender') == 'female'  ? 'selected' : '' }}>Female</option>
                            <option value="male"    {{ request('gender') == 'male'    ? 'selected' : '' }}>Male</option>
                            <option value="shemale" {{ request('gender') == 'shemale' ? 'selected' : '' }}>Shemale</option>
                        </select>
                    </div>

                    <div class="a-field">
                        <label><i class="fas fa-hashtag"></i> Spot Number</label>
                        <select name="spot" class="form-control">
                            <option value="">All Spots</option>
                            @for($i = 1; $i <= 6; $i++)
                                <option value="{{ $i }}" {{ request('spot') == $i ? 'selected' : '' }}>Spot #{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="a-field">
                        <label><i class="fas fa-sort"></i> Sort By</label>
                        <select name="sort" class="form-control">
                            <option value="end_date"      {{ request('sort') == 'end_date'      ? 'selected' : '' }}>End Date</option>
                            <option value="current_price" {{ request('sort') == 'current_price' ? 'selected' : '' }}>Current Price</option>
                            <option value="bid_count"     {{ request('sort') == 'bid_count'     ? 'selected' : '' }}>Bid Count</option>
                        </select>
                    </div>

                    <div class="a-actions">
                        <button type="submit" class="a-btn a-btn-primary" title="Apply filters">
                            <i class="fas fa-filter"></i> Apply
                        </button>
                        <a href="{{ route('admin.auctions.index') }}" class="a-btn a-btn-ghost" title="Reset">
                            <i class="fas fa-sync"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div style="overflow-x: auto;">
            <table class="a-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Spot #</th>
                        <th>City</th>
                        <th>Gender</th>
                        <th>Current Price</th>
                        <th>End Date</th>
                        <th>Time Left</th>
                        <th>Status</th>
                        <th>Bids</th>
                        <th>Winner</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($auctions as $auction)
                    @php
                        $gender = strtolower($auction->gender ?? '');
                        $genderClass = 'a-gender-' . ($gender ?: 'muted');
                        $genderIcon = match($gender) {
                            'female'  => 'fas fa-venus',
                            'male'    => 'fas fa-mars',
                            'shemale' => 'fas fa-transgender',
                            default   => 'fas fa-genderless',
                        };
                        $timeLeftClass = $auction->status !== 'active' ? 'ended' : '';
                    @endphp
                    <tr>
                        <td><span class="a-id-chip">#{{ $auction->id }}</span></td>
                        <td><span class="a-spot-badge">#{{ $auction->spot_number }}</span></td>
                        <td>
                            <span class="a-city-cell">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $auction->city->name ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <span class="a-gender-badge {{ $genderClass }}">
                                <i class="{{ $genderIcon }}"></i> {{ ucfirst($auction->gender) }}
                            </span>
                        </td>
                        <td><span class="a-price">${{ number_format($auction->current_price, 2) }}</span></td>
                        <td>
                            <div class="a-date">
                                {{ $auction->end_date->format('M d, Y') }}
                                <small>{{ $auction->end_date->format('H:i') }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="a-time-left {{ $timeLeftClass }}">{{ $auction->time_left }}</span>
                        </td>
                        <td>
                            @if($auction->status === 'active')
                                <span class="a-status a-status-active"><i class="fas fa-circle"></i> Active</span>
                            @else
                                <span class="a-status a-status-ended"><i class="fas fa-check"></i> Ended</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.auctions.bids', $auction) }}"
                               class="a-bids-link {{ $auction->bid_count == 0 ? 'zero' : '' }}">
                                <i class="fas fa-gavel"></i> {{ $auction->bid_count }} bids
                            </a>
                        </td>
                        <td>
                            @if($auction->winner_profile_id && ($auction->winnerProfile->name ?? null))
                                <span class="a-winner-cell">
                                    <span class="a-winner-avatar">{{ strtoupper(mb_substr($auction->winnerProfile->name, 0, 1)) }}</span>
                                    <span class="a-winner-name">{{ $auction->winnerProfile->name }}</span>
                                </span>
                            @else
                                <span style="color:#cbd5e1;">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="a-actions-cell">
                                <a href="{{ route('admin.auctions.edit', $auction) }}" class="a-icon-btn edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                @if($auction->status === 'active')
                                    <form action="{{ route('admin.auctions.end', $auction) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="a-icon-btn end" title="End auction" onclick="return confirm('Are you sure you want to end this auction?')">
                                            <i class="fas fa-stop-circle"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.auctions.reset', $auction) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="a-icon-btn reset" title="Reset auction" onclick="return confirm('Are you sure you want to reset this auction?')">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.auctions.destroy', $auction) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="a-icon-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this auction?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if(count($auctions) === 0)
                <div class="a-empty">
                    <div class="a-empty-icon"><i class="fas fa-gavel"></i></div>
                    <p style="color:#64748b; margin:0; font-size:15px;">No auctions found — try adjusting your filters or create a new spot.</p>
                </div>
            @endif
        </div>

        <div class="a-pagination">
            {{ $auctions->links() }}
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    let searchTimeout;

    function searchCities(query) {
        clearTimeout(searchTimeout);

        if (query.length < 2) {
            $('#city_results').hide();
            return;
        }

        searchTimeout = setTimeout(function() {
            $.ajax({
                url: '{{ route("admin.cities.search") }}',
                type: 'POST',
                data: {
                    search: query,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('#city_results').empty();

                    if (data.length === 0) {
                        $('#city_results').append('<div class="dropdown-item text-muted" style="cursor:default;">No cities found</div>');
                    } else {
                        $.each(data, function(index, city) {
                            const cityName = city.name + (city.country ? ` (${city.country})` : '');
                            const item = $('<div class="dropdown-item city-item"></div>')
                                .text(cityName)
                                .data('id', city.id)
                                .data('name', city.name);

                            $('#city_results').append(item);
                        });
                    }

                    $('#city_results').show();
                },
                error: function() {
                    $('#city_results').html('<div class="dropdown-item text-danger">Error loading cities</div>').show();
                }
            });
        }, 300);
    }

    $('#city_search').on('input', function() {
        searchCities($(this).val().trim());
    });

    $(document).on('click', '.city-item', function() {
        const cityId = $(this).data('id');
        const cityName = $(this).data('name');

        $('#city_id').val(cityId);
        $('#city_search').val(cityName);
        $('#city_results').hide();
    });

    $(document).on('click', function(event) {
        if (!$(event.target).closest('.a-city-wrap').length) {
            $('#city_results').hide();
        }
    });

    $('#city_search').on('focus', function() {
        if ($(this).val().trim().length >= 2) {
            searchCities($(this).val().trim());
        }
    });
});
</script>
@endpush
