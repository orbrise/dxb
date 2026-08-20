@extends("admin.layout.master")

@push('css')
<style>
    .scraper-progress-bar {
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
    }
    .scraper-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #0d6efd, #6ea8fe);
        transition: width 0.4s ease;
    }
    .scraper-status-badge {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 3px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }
    .scraper-status-pending   { background:#fff3cd; color:#664d03; }
    .scraper-status-running   { background:#cfe2ff; color:#084298; }
    .scraper-status-completed { background:#d1e7dd; color:#0f5132; }
    .scraper-status-failed    { background:#f8d7da; color:#842029; }

    .scraper-log-viewer {
        display: none;
        background: #1e1e1e;
        color: #d4d4d4;
        font-family: 'Consolas', 'Monaco', monospace;
        font-size: 12px;
        padding: 12px;
        border-radius: 4px;
        max-height: 320px;
        overflow-y: auto;
        white-space: pre-wrap;
        word-break: break-word;
        margin-top: 8px;
    }
    .scraper-log-viewer.open { display: block; }

    /* Multi-city picker */
    .scraper-city-picker { position: relative; }
    .scraper-city-input {
        width: 100%;
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 8px 12px;
        min-height: 40px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
        cursor: text;
        background: #fff;
    }
    .scraper-city-input:focus-within { border-color: #86b7fe; box-shadow: 0 0 0 0.15rem rgba(13,110,253,.15); }
    .scraper-city-chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: #e7f1ff; color: #0d6efd;
        padding: 2px 8px; border-radius: 12px; font-size: 12px;
    }
    .scraper-city-chip button {
        background: transparent; border: 0; color: #0d6efd;
        cursor: pointer; padding: 0; font-size: 14px; line-height: 1;
    }
    .scraper-city-text {
        border: 0; outline: 0; flex: 1; min-width: 120px; font-size: 14px;
    }
    .scraper-city-dropdown {
        position: absolute; top: 100%; left: 0; right: 0; z-index: 10;
        background: #fff; border: 1px solid #dee2e6; border-radius: 4px;
        max-height: 240px; overflow-y: auto; margin-top: 2px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.08);
        display: none;
    }
    .scraper-city-dropdown.open { display: block; }
    .scraper-city-option {
        padding: 8px 12px; cursor: pointer; font-size: 14px;
    }
    .scraper-city-option:hover, .scraper-city-option.active { background: #f1f3f5; }
    .scraper-city-option.disabled { color: #adb5bd; cursor: not-allowed; }
    .scraper-city-empty { padding: 8px 12px; color: #6c757d; font-size: 13px; }
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

<ul class="nav nav-pills mb-3 mt-2">
    <li class="nav-item"><a class="nav-link active" href="{{ route('admin.scrapers.index') }}">Manual Run</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.scrapers.auto') }}">Auto Setup (cron)</a></li>
</ul>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row mt-3">
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Run a scraper</h5>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('admin.scrapers.store') }}" id="scraper-run-form">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Website</label>
                        <select name="source" id="scraper-source" class="form-control" required>
                            @foreach($sources as $key => $src)
                                <option value="{{ $key }}">{{ $src['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Cities <small class="text-muted">(type to search, click to add — multi-select)</small></label>
                        <div class="scraper-city-picker" id="scraper-city-picker">
                            <div class="scraper-city-input" id="scraper-city-input">
                                <input type="text" class="scraper-city-text" id="scraper-city-text" placeholder="Type city name...">
                            </div>
                            <div class="scraper-city-dropdown" id="scraper-city-dropdown"></div>
                            <div id="scraper-city-hidden-inputs"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Number of profiles</label>
                        <input type="number" name="limit" class="form-control" value="25" min="1" max="500" required>
                        <small class="text-muted">Between 1 and 500.</small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-play"></i> Start scraper
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="mb-2">Notes</h6>
                <ul class="mb-0 text-muted" style="font-size:13px; padding-left:18px;">
                    <li>Select multiple cities to queue them — they run one after another for the same site.</li>
                    <li>Runs execute in the background — you can close this page.</li>
                    <li>The history table auto-refreshes every 3 seconds.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent runs</h5>
                <small class="text-muted"><span id="scraper-refresh-indicator">·</span> auto-refresh</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0" id="scraper-runs-table">
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
                                <tr id="scraper-empty-row"><td colspan="7" class="text-center text-muted py-4">No runs yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
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
