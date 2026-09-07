@extends("admin.layout.master")

@push('css')
<style>
/* ===== Scrapers page: modern redesign ===== */
:root {
    --s-primary: #6366f1;
    --s-primary-dark: #4f46e5;
    --s-success: #10b981;
    --s-danger: #ef4444;
    --s-warning: #f59e0b;
    --s-info: #06b6d4;
    --s-slate-50: #f8fafc;
    --s-slate-100: #f1f5f9;
    --s-slate-200: #e2e8f0;
    --s-slate-300: #cbd5e1;
    --s-slate-500: #64748b;
    --s-slate-600: #475569;
    --s-slate-700: #334155;
    --s-slate-800: #1e293b;
}

/* Stats strip */
.s-stats-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin: 4px 0 18px;
}
.s-stat {
    position: relative;
    padding: 16px 18px;
    border-radius: 12px;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(15,23,42,.08);
}
.s-stat .s-stat-label {
    font-size: 12px; text-transform: uppercase; letter-spacing: .06em;
    opacity: .9; margin: 0 0 4px; font-weight: 600;
}
.s-stat .s-stat-value {
    font-size: 26px; font-weight: 700; line-height: 1.1; margin: 0;
}
.s-stat .s-stat-icon {
    position: absolute; right: 14px; top: 50%;
    transform: translateY(-50%); font-size: 34px; opacity: .35;
}
.s-stat.total     { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.s-stat.running   { background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); }
.s-stat.completed { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.s-stat.failed    { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); }

/* Tabs */
.s-tabs {
    display: inline-flex;
    background: #fff;
    border: 1px solid var(--s-slate-200);
    border-radius: 12px;
    padding: 5px;
    margin: 0 0 18px;
    box-shadow: 0 2px 8px rgba(15,23,42,.04);
    gap: 3px;
    list-style: none;
}
.s-tabs .nav-item { display: inline-block; }
.s-tabs .nav-link {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 18px;
    border-radius: 8px;
    font-size: 13px; font-weight: 600;
    color: var(--s-slate-600);
    text-decoration: none;
    transition: all .15s;
    background: transparent;
    border: 0;
}
.s-tabs .nav-link:hover { color: var(--s-slate-800); background: var(--s-slate-50); }
.s-tabs .nav-link.active {
    background: linear-gradient(135deg, var(--s-primary) 0%, var(--s-primary-dark) 100%);
    color: #fff !important;
    box-shadow: 0 3px 10px rgba(99,102,241,.35);
}

/* Alerts */
.s-alert {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 13px; font-weight: 500;
    margin-bottom: 14px;
}
.s-alert-success { background: #d1fae5; color: #065f46; }
.s-alert-error   { background: #fee2e2; color: #991b1b; }

/* Card */
.s-card {
    background: #fff;
    border: 1px solid var(--s-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
    margin-bottom: 18px;
}
.s-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 16px 20px;
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--s-slate-100);
}
.s-card-head .s-title-icon {
    width: 34px; height: 34px; border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(99, 102, 241, .12); color: var(--s-primary-dark); font-size: 15px;
}
.s-card-head .s-title {
    display: flex; align-items: center; gap: 10px;
    margin: 0;
    font-size: 15px; font-weight: 700; color: var(--s-slate-800);
}
.s-card-head .s-title small {
    display: block; font-weight: 400; color: var(--s-slate-500); font-size: 12px;
}
.s-card-body { padding: 20px; }

/* Form */
.s-card .form-label {
    display: flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .04em;
    color: var(--s-slate-500);
    margin-bottom: 6px;
}
.s-card .form-label small {
    font-size: 11px; text-transform: none; letter-spacing: 0;
    font-weight: 400; margin-left: 4px;
    color: var(--s-slate-500);
}
.s-card .form-control {
    width: 100%; height: 42px;
    padding: 8px 12px;
    font-size: 14px;
    color: var(--s-slate-800);
    background: #fff;
    border: 1px solid var(--s-slate-200);
    border-radius: 9px;
    transition: border-color .15s, box-shadow .15s;
}
.s-card .form-control:focus {
    outline: none;
    border-color: var(--s-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.s-card small.text-muted {
    display: block; margin-top: 6px;
    color: var(--s-slate-500) !important;
    font-size: 12px;
}

/* City picker — restyled to match new theme */
.scraper-city-picker { position: relative; }
.scraper-city-input {
    width: 100%;
    border: 1px solid var(--s-slate-200);
    border-radius: 9px;
    padding: 6px 10px;
    min-height: 42px;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    align-items: center;
    cursor: text;
    background: #fff;
    transition: border-color .15s, box-shadow .15s;
}
.scraper-city-input:focus-within {
    border-color: var(--s-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.scraper-city-chip {
    display: inline-flex; align-items: center; gap: 6px;
    background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
    color: var(--s-primary-dark);
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}
.scraper-city-chip button {
    background: transparent; border: 0; color: var(--s-primary-dark);
    cursor: pointer; padding: 0; font-size: 15px; line-height: 1;
    opacity: .7; transition: opacity .15s;
}
.scraper-city-chip button:hover { opacity: 1; }
.scraper-city-text {
    border: 0; outline: 0; flex: 1; min-width: 120px;
    font-size: 14px; color: var(--s-slate-800);
    background: transparent;
    padding: 4px 0;
}
.scraper-city-text::placeholder { color: var(--s-slate-300); }
.scraper-city-dropdown {
    position: absolute; top: 100%; left: 0; right: 0; z-index: 10;
    background: #fff; border: 1px solid var(--s-slate-200); border-radius: 10px;
    max-height: 240px; overflow-y: auto; margin-top: 4px;
    box-shadow: 0 12px 32px rgba(15,23,42,.15);
    padding: 6px;
    display: none;
}
.scraper-city-dropdown.open { display: block; }
.scraper-city-option {
    padding: 8px 12px;
    cursor: pointer;
    font-size: 13px;
    color: var(--s-slate-700);
    border-radius: 6px;
}
.scraper-city-option:hover, .scraper-city-option.active {
    background: var(--s-slate-50);
    color: var(--s-slate-800);
}
.scraper-city-option.disabled { color: var(--s-slate-300); cursor: not-allowed; }
.scraper-city-empty {
    padding: 8px 12px; color: var(--s-slate-500); font-size: 13px;
}

/* Primary button */
.s-btn-primary {
    display: inline-flex; align-items: center; gap: 6px;
    height: 42px; padding: 0 20px;
    border-radius: 9px;
    background: linear-gradient(135deg, var(--s-primary) 0%, var(--s-primary-dark) 100%);
    color: #fff; font-size: 14px; font-weight: 600;
    border: 0; cursor: pointer;
    box-shadow: 0 4px 12px rgba(99, 102, 241, .35);
    transition: box-shadow .15s;
    text-decoration: none;
}
.s-btn-primary:hover { color: #fff; box-shadow: 0 6px 16px rgba(99, 102, 241, .45); text-decoration: none; }

/* Notes card */
.s-notes {
    padding: 16px 20px;
}
.s-notes h6 {
    display: flex; align-items: center; gap: 6px;
    margin: 0 0 10px;
    font-size: 13px; font-weight: 700; color: var(--s-slate-700);
    text-transform: uppercase; letter-spacing: .04em;
}
.s-notes h6 i { color: var(--s-info); }
.s-notes ul {
    margin: 0; padding-left: 22px;
    color: var(--s-slate-600); font-size: 13px; line-height: 1.6;
}
.s-notes ul li + li { margin-top: 4px; }

/* Runs table */
.s-refresh-indicator {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 500;
    color: var(--s-slate-500);
    padding: 4px 10px;
    background: var(--s-slate-100);
    border-radius: 20px;
}
.s-refresh-indicator #scraper-refresh-indicator {
    display: inline-block; width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--s-success);
    transition: opacity .2s;
    font-size: 0;
    line-height: 0;
    overflow: hidden;
}
.s-runs-body { padding: 0; }
#scraper-runs-table {
    width: 100%;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 13.5px;
}
#scraper-runs-table thead th {
    background: var(--s-slate-50);
    color: var(--s-slate-500);
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    padding: 12px 14px;
    border: 0;
    border-bottom: 1px solid var(--s-slate-200);
    text-align: left;
    white-space: nowrap;
}
#scraper-runs-table tbody td {
    padding: 14px;
    vertical-align: middle;
    border: 0;
    border-bottom: 1px solid var(--s-slate-100);
    color: var(--s-slate-700);
    background: #fff;
}
#scraper-runs-table tbody tr:hover td { background: #fafbff; }
#scraper-runs-table tbody tr:last-child td { border-bottom: 0; }

.s-run-id {
    display: inline-block;
    padding: 3px 9px;
    font-size: 12px; font-weight: 700;
    border-radius: 6px;
    color: var(--s-slate-500);
    background: var(--s-slate-100);
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.s-site-cell {
    font-weight: 600; color: var(--s-slate-800);
}
.s-city-cell {
    display: inline-block;
    padding: 3px 9px;
    background: #eef2ff;
    color: var(--s-primary-dark);
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}

/* Progress bar */
.scraper-progress-bar {
    height: 8px;
    background: var(--s-slate-100);
    border-radius: 4px;
    overflow: hidden;
    min-width: 140px;
}
.scraper-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--s-primary) 0%, var(--s-primary-dark) 100%);
    transition: width 0.4s ease;
    border-radius: 4px;
}
#scraper-runs-table .js-progress-text {
    display: inline-block; margin-top: 4px;
    font-size: 12px; font-weight: 600; color: var(--s-slate-600);
}
#scraper-runs-table .js-stage {
    color: var(--s-slate-500) !important;
    font-size: 11px !important;
    display: inline-block; margin-top: 2px;
}

/* Status badges */
.scraper-status-badge {
    display: inline-flex; align-items: center;
    padding: 4px 10px;
    font-size: 11px !important;
    font-weight: 700 !important;
    border-radius: 6px;
    letter-spacing: .05em;
    text-transform: uppercase;
}
.scraper-status-pending   { background: #fef3c7 !important; color: #92400e !important; }
.scraper-status-running   { background: #dbeafe !important; color: #1e40af !important; }
.scraper-status-completed { background: #d1fae5 !important; color: #065f46 !important; }
.scraper-status-failed    { background: #fee2e2 !important; color: #991b1b !important; }

.s-started {
    color: var(--s-slate-600);
    font-size: 12px !important;
    font-weight: 500;
}

/* Log button + viewer */
.js-log-btn {
    display: inline-flex !important;
    align-items: center; gap: 5px;
    padding: 6px 12px !important;
    background: var(--s-slate-100) !important;
    color: var(--s-slate-700) !important;
    border: 0 !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
}
.js-log-btn:hover { background: var(--s-slate-200) !important; color: var(--s-slate-800) !important; }
.scraper-log-viewer {
    display: none;
    background: #0f172a;
    color: #cbd5e1;
    font-family: 'SFMono-Regular', 'Consolas', 'Monaco', monospace;
    font-size: 12px;
    padding: 14px;
    border-radius: 9px;
    max-height: 320px;
    overflow-y: auto;
    white-space: pre-wrap;
    word-break: break-word;
    margin-top: 10px;
    border: 1px solid #1e293b;
    box-shadow: inset 0 2px 6px rgba(0,0,0,.3);
}
.scraper-log-viewer.open { display: block; }

/* Empty */
.s-empty td {
    text-align: center !important;
    padding: 40px 20px !important;
    color: var(--s-slate-500) !important;
    font-size: 14px;
}

/* Responsive */
@media (max-width: 992px) {
    .s-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
    .main-wrapper { padding-left: 8px !important; padding-right: 8px !important; }
    .container-fluid { padding-left: 6px !important; padding-right: 6px !important; }
    .s-stats-row { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    #scraper-runs-table thead { display: none; }
}
</style>
@endpush

@section("content")
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Scrapers &mdash; Manual Run</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Pick a site + cities and run scrapers on demand</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Scrapers</li>
        </ol>
    </div>
</div>

@php
    $runsCollection = collect($runs ?? []);
    $totalRuns    = $runsCollection->count();
    $runningRuns  = $runsCollection->where('status', 'running')->count() + $runsCollection->where('status', 'pending')->count();
    $completedRuns = $runsCollection->where('status', 'completed')->count();
    $failedRuns   = $runsCollection->where('status', 'failed')->count();
@endphp

<div class="container-fluid px-0">

    {{-- Stats --}}
    <div class="s-stats-row">
        <div class="s-stat total">
            <p class="s-stat-label">Recent Runs</p>
            <p class="s-stat-value">{{ number_format($totalRuns) }}</p>
            <i class="fas fa-list-alt s-stat-icon"></i>
        </div>
        <div class="s-stat running">
            <p class="s-stat-label">In Progress</p>
            <p class="s-stat-value">{{ number_format($runningRuns) }}</p>
            <i class="fas fa-spinner s-stat-icon"></i>
        </div>
        <div class="s-stat completed">
            <p class="s-stat-label">Completed</p>
            <p class="s-stat-value">{{ number_format($completedRuns) }}</p>
            <i class="fas fa-check-circle s-stat-icon"></i>
        </div>
        <div class="s-stat failed">
            <p class="s-stat-label">Failed</p>
            <p class="s-stat-value">{{ number_format($failedRuns) }}</p>
            <i class="fas fa-exclamation-triangle s-stat-icon"></i>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-pills s-tabs">
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.scrapers.index') }}"><i class="fa fa-play"></i> Manual Run</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.scrapers.auto') }}"><i class="fa fa-clock"></i> Auto Setup (cron)</a></li>
    </ul>

    @if(session('success'))
        <div class="s-alert s-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="s-alert s-alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-5">
            {{-- Run a scraper --}}
            <div class="s-card">
                <div class="s-card-head">
                    <h5 class="s-title">
                        <span class="s-title-icon"><i class="fas fa-play"></i></span>
                        Run a scraper
                    </h5>
                </div>
                <div class="s-card-body">
                    <form method="post" action="{{ route('admin.scrapers.store') }}" id="scraper-run-form">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-globe"></i> Website</label>
                            <select name="source" id="scraper-source" class="form-control" required>
                                @foreach($sources as $key => $src)
                                    <option value="{{ $key }}">{{ $src['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-map-marker-alt"></i> Cities <small>(type to search, click to add — multi-select)</small></label>
                            <div class="scraper-city-picker" id="scraper-city-picker">
                                <div class="scraper-city-input" id="scraper-city-input">
                                    <input type="text" class="scraper-city-text" id="scraper-city-text" placeholder="Type city name...">
                                </div>
                                <div class="scraper-city-dropdown" id="scraper-city-dropdown"></div>
                                <div id="scraper-city-hidden-inputs"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-hashtag"></i> Number of profiles</label>
                            <input type="number" name="limit" class="form-control" value="25" min="1" max="500" required>
                            <small class="text-muted">Between 1 and 500.</small>
                        </div>

                        <button type="submit" class="s-btn-primary">
                            <i class="fa fa-play"></i> Start scraper
                        </button>
                    </form>
                </div>
            </div>

            {{-- Notes --}}
            <div class="s-card">
                <div class="s-notes">
                    <h6><i class="fas fa-info-circle"></i> Notes</h6>
                    <ul>
                        <li>Select multiple cities to queue them — they run one after another for the same site.</li>
                        <li>Runs execute in the background — you can close this page.</li>
                        <li>The history table auto-refreshes every 3 seconds.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            {{-- Recent runs --}}
            <div class="s-card">
                <div class="s-card-head">
                    <h5 class="s-title">
                        <span class="s-title-icon"><i class="fas fa-history"></i></span>
                        Recent runs
                    </h5>
                    <span class="s-refresh-indicator">
                        <span id="scraper-refresh-indicator">·</span> auto-refresh
                    </span>
                </div>
                <div class="s-runs-body">
                    <div class="table-responsive">
                        <table class="table" id="scraper-runs-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Site</th>
                                    <th>City</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Started</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="scraper-runs-body">
                                @forelse($runs as $run)
                                    @include('admin.scrapers.partials.run-row', ['run' => $run, 'sources' => $sources])
                                @empty
                                    <tr id="scraper-empty-row" class="s-empty"><td colspan="7">
                                        <i class="fas fa fa-inbox" style="font-size:22px; color:#cbd5e1; display:block; margin-bottom:8px;"></i>
                                        No runs yet.
                                    </td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
(function() {
    const CITIES = @json($cities);
    const sourceSelect = document.getElementById('scraper-source');

    // ----- Multi-city searchable picker -----
    const inputWrap = document.getElementById('scraper-city-input');
    const textInput = document.getElementById('scraper-city-text');
    const dropdown  = document.getElementById('scraper-city-dropdown');
    const hiddenBox = document.getElementById('scraper-city-hidden-inputs');
    const picker    = document.getElementById('scraper-city-picker');
    let selectedSlugs = [];
    let activeIndex = -1;
    let currentFiltered = [];

    function getCityLabel(slug) {
        const match = CITIES.find(c => c.slug === slug);
        return match ? match.name : slug;
    }

    function renderChips() {
        // Wipe existing chips (keep the text input at the end).
        inputWrap.querySelectorAll('.scraper-city-chip').forEach(c => c.remove());
        hiddenBox.innerHTML = '';
        selectedSlugs.forEach(slug => {
            const chip = document.createElement('span');
            chip.className = 'scraper-city-chip';
            chip.innerHTML = `<span>${getCityLabel(slug)}</span><button type="button" data-slug="${slug}" aria-label="Remove">&times;</button>`;
            inputWrap.insertBefore(chip, textInput);
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'cities[]';
            hidden.value = slug;
            hiddenBox.appendChild(hidden);
        });
    }

    function filterAndRender() {
        const q = textInput.value.trim().toLowerCase();
        // Filter DB cities: hide already-selected, match on either name or slug.
        // Cap to 100 results so a 34k-row cities table doesn't kill the browser.
        currentFiltered = [];
        for (const c of CITIES) {
            if (selectedSlugs.includes(c.slug)) continue;
            if (q && !c.slug.toLowerCase().includes(q) && !c.name.toLowerCase().includes(q)) continue;
            currentFiltered.push(c);
            if (currentFiltered.length >= 100) break;
        }
        activeIndex = currentFiltered.length ? 0 : -1;
        dropdown.innerHTML = '';
        if (currentFiltered.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'scraper-city-empty';
            empty.textContent = q ? 'No matching cities.' : 'Start typing a city name...';
            dropdown.appendChild(empty);
        } else {
            currentFiltered.forEach((c, i) => {
                const opt = document.createElement('div');
                opt.className = 'scraper-city-option' + (i === activeIndex ? ' active' : '');
                opt.dataset.slug = c.slug;
                opt.textContent = c.name + ' (' + c.slug + ')';
                dropdown.appendChild(opt);
            });
        }
    }

    function openDropdown() {
        filterAndRender();
        dropdown.classList.add('open');
    }
    function closeDropdown() { dropdown.classList.remove('open'); }

    function addSlug(slug) {
        if (selectedSlugs.includes(slug)) return;
        selectedSlugs.push(slug);
        renderChips();
        textInput.value = '';
        filterAndRender();
        textInput.focus();
    }
    function removeSlug(slug) {
        selectedSlugs = selectedSlugs.filter(s => s !== slug);
        renderChips();
        filterAndRender();
    }

    inputWrap.addEventListener('click', (e) => {
        if (e.target.closest('.scraper-city-chip button')) return;
        textInput.focus();
        openDropdown();
    });

    textInput.addEventListener('focus', openDropdown);
    textInput.addEventListener('input', filterAndRender);

    textInput.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (currentFiltered.length) {
                activeIndex = (activeIndex + 1) % currentFiltered.length;
                renderActive();
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (currentFiltered.length) {
                activeIndex = (activeIndex - 1 + currentFiltered.length) % currentFiltered.length;
                renderActive();
            }
        } else if (e.key === 'Enter') {
            if (activeIndex >= 0 && currentFiltered[activeIndex]) {
                e.preventDefault();
                addSlug(currentFiltered[activeIndex][0]);
            }
        } else if (e.key === 'Backspace' && textInput.value === '' && selectedSlugs.length) {
            e.preventDefault();
            removeSlug(selectedSlugs[selectedSlugs.length - 1]);
        } else if (e.key === 'Escape') {
            closeDropdown();
        }
    });

    function renderActive() {
        dropdown.querySelectorAll('.scraper-city-option').forEach((el, i) => {
            el.classList.toggle('active', i === activeIndex);
        });
    }

    dropdown.addEventListener('click', (e) => {
        const opt = e.target.closest('.scraper-city-option');
        if (!opt) return;
        addSlug(opt.dataset.slug);
    });

    inputWrap.addEventListener('click', (e) => {
        const btn = e.target.closest('.scraper-city-chip button');
        if (btn) {
            e.preventDefault();
            removeSlug(btn.dataset.slug);
        }
    });

    document.addEventListener('click', (e) => {
        if (!picker.contains(e.target)) closeDropdown();
    });

    // Block submit if nothing selected.
    document.getElementById('scraper-run-form').addEventListener('submit', (e) => {
        if (selectedSlugs.length === 0) {
            e.preventDefault();
            alert('Please select at least one city.');
        }
    });

    // First paint.
    renderChips();
    filterAndRender();

    // Poll each non-terminal run every 3s and swap its <tr> in place.
    async function refreshRuns() {
        const rows = document.querySelectorAll('#scraper-runs-body tr[data-run-id]');
        for (const row of rows) {
            if (row.dataset.terminal === '1') continue;
            const id = row.dataset.runId;
            try {
                const res = await fetch(`{{ url('admin/scrapers') }}/${id}/status`, { credentials: 'same-origin' });
                if (!res.ok) continue;
                const data = await res.json();
                updateRow(row, data);
            } catch (_) { /* ignore transient errors */ }
        }
        blinkIndicator();
    }

    function updateRow(row, data) {
        row.dataset.terminal = data.is_terminal ? '1' : '0';
        const pct = data.progress_percent || 0;
        row.querySelector('.js-progress-fill').style.width = pct + '%';
        row.querySelector('.js-progress-text').textContent =
            (data.progress_current || 0) + ' / ' + (data.progress_total || data.requested_count) + ' (' + pct + '%)';
        const badge = row.querySelector('.js-status');
        badge.className = 'scraper-status-badge scraper-status-' + data.status;
        badge.textContent = data.status;
        const stageEl = row.querySelector('.js-stage');
        if (stageEl) stageEl.textContent = data.progress_stage || '';
    }

    function blinkIndicator() {
        const el = document.getElementById('scraper-refresh-indicator');
        el.style.opacity = '1';
        setTimeout(() => { el.style.opacity = '0.3'; }, 200);
    }

    setInterval(refreshRuns, 3000);

    // Log viewer toggle
    document.getElementById('scraper-runs-body').addEventListener('click', async (e) => {
        const btn = e.target.closest('.js-log-btn');
        if (!btn) return;
        e.preventDefault();
        const id = btn.dataset.runId;
        const viewer = document.getElementById('scraper-log-' + id);
        if (viewer.classList.contains('open')) {
            viewer.classList.remove('open');
            return;
        }
        viewer.textContent = 'Loading...';
        viewer.classList.add('open');
        try {
            const res = await fetch(`{{ url('admin/scrapers') }}/${id}/log`, { credentials: 'same-origin' });
            viewer.textContent = await res.text();
            viewer.scrollTop = viewer.scrollHeight;
        } catch (err) {
            viewer.textContent = 'Failed to load log: ' + err.message;
        }
    });
})();
</script>
@endpush
