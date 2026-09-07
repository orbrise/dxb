@extends("admin.layout.master")

@push('css')
<style>
/* ===== Scrapers Auto page: modern redesign ===== */
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

/* Tabs (same as manual page) */
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

/* "How this works" callout */
.s-info-card {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 1px solid #bfdbfe;
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 18px;
    display: flex;
    gap: 14px;
    align-items: flex-start;
}
.s-info-card .s-info-icon {
    width: 42px; height: 42px; border-radius: 10px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: #fff;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(59, 130, 246, .35);
}
.s-info-card h6 {
    margin: 0 0 4px;
    font-size: 14px; font-weight: 700; color: #1e40af;
}
.s-info-card p {
    margin: 0;
    font-size: 13px; color: #1e3a8a; line-height: 1.5;
}
.s-info-card b { color: #1e3a8a; }

/* Source card */
.s-source-block { margin-bottom: 18px; }
.s-card {
    background: #fff;
    border: 1px solid var(--s-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
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
.s-card-head .s-title {
    display: flex; align-items: center; gap: 10px;
    margin: 0;
    font-size: 15px; font-weight: 700; color: var(--s-slate-800);
}
.s-card-head .s-title-icon {
    width: 34px; height: 34px; border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(99, 102, 241, .12); color: var(--s-primary-dark); font-size: 15px;
}
.s-card-head .s-title-count {
    display: inline-flex; align-items: center; gap: 12px;
    font-size: 12px; color: var(--s-slate-500); font-weight: 500;
}
.s-count-chip {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px; font-weight: 700;
}
.s-count-chip.configured { background: var(--s-slate-100); color: var(--s-slate-700); }
.s-count-chip.active     { background: #d1fae5; color: #065f46; }
.s-card-body { padding: 20px; }

/* Form */
.s-card .form-label {
    display: flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .04em;
    color: var(--s-slate-500);
    margin-bottom: 6px;
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

.s-btn-primary {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    height: 42px; padding: 0 20px;
    border-radius: 9px;
    background: linear-gradient(135deg, var(--s-primary) 0%, var(--s-primary-dark) 100%);
    color: #fff !important; font-size: 14px; font-weight: 600;
    border: 0; cursor: pointer;
    box-shadow: 0 4px 12px rgba(99, 102, 241, .35);
    transition: box-shadow .15s;
    text-decoration: none;
    width: 100%;
}
.s-btn-primary:hover { color: #fff; box-shadow: 0 6px 16px rgba(99, 102, 241, .45); text-decoration: none; }

/* City picker (auto variant) */
.auto-city-picker { position: relative; }
.auto-city-input {
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
.auto-city-input:focus-within {
    border-color: var(--s-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.auto-city-chip {
    display: inline-flex; align-items: center; gap: 6px;
    background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
    color: var(--s-primary-dark);
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}
.auto-city-chip button {
    background: transparent; border: 0; color: var(--s-primary-dark);
    cursor: pointer; padding: 0; font-size: 15px; line-height: 1;
    opacity: .7; transition: opacity .15s;
}
.auto-city-chip button:hover { opacity: 1; }
.auto-city-text {
    border: 0; outline: 0; flex: 1; min-width: 120px;
    font-size: 14px; color: var(--s-slate-800);
    background: transparent;
    padding: 4px 0;
}
.auto-city-text::placeholder { color: var(--s-slate-300); }
.auto-city-dropdown {
    position: absolute; top: 100%; left: 0; right: 0; z-index: 10;
    background: #fff; border: 1px solid var(--s-slate-200); border-radius: 10px;
    max-height: 240px; overflow-y: auto; margin-top: 4px;
    box-shadow: 0 12px 32px rgba(15,23,42,.15);
    padding: 6px;
    display: none;
}
.auto-city-dropdown.open { display: block; }
.auto-city-option {
    padding: 8px 12px;
    cursor: pointer;
    font-size: 13px;
    color: var(--s-slate-700);
    border-radius: 6px;
}
.auto-city-option:hover, .auto-city-option.active {
    background: var(--s-slate-50);
    color: var(--s-slate-800);
}
.auto-city-empty {
    padding: 8px 12px; color: var(--s-slate-500); font-size: 13px;
}

/* Divider */
.s-card hr {
    border: 0;
    border-top: 1px solid var(--s-slate-100);
    margin: 20px 0;
}

/* Config table */
.s-config-table {
    width: 100%;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 13.5px;
}
.s-config-table thead th {
    background: var(--s-slate-50);
    color: var(--s-slate-500);
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    padding: 12px 14px;
    border: 0;
    border-top: 1px solid var(--s-slate-200);
    border-bottom: 1px solid var(--s-slate-200);
    text-align: left;
    white-space: nowrap;
}
.s-config-table tbody td {
    padding: 12px 14px;
    vertical-align: middle;
    border: 0;
    border-bottom: 1px solid var(--s-slate-100);
    color: var(--s-slate-700);
    background: #fff;
}
.s-config-table tbody tr:hover td { background: #fafbff; }
.s-config-table tbody tr:last-child td { border-bottom: 0; }
.s-config-table .s-city-name { font-weight: 600; color: var(--s-slate-800); }
.s-config-table code {
    background: var(--s-slate-100);
    color: var(--s-slate-700);
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.s-config-table .s-limit {
    display: inline-block;
    padding: 3px 9px;
    background: #eef2ff;
    color: var(--s-primary-dark);
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}

/* On/Off toggle button */
.s-toggle-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: .05em;
    border: 0;
    cursor: pointer;
    transition: box-shadow .15s;
    min-width: 60px;
    justify-content: center;
}
.s-toggle-btn.on {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff;
    box-shadow: 0 3px 8px rgba(16, 185, 129, .35);
}
.s-toggle-btn.off {
    background: var(--s-slate-100);
    color: var(--s-slate-500);
}
.s-toggle-btn:hover { box-shadow: 0 5px 12px rgba(0,0,0,.15); }
.s-toggle-btn.on::before {
    content: ""; display: inline-block;
    width: 6px; height: 6px; border-radius: 50%;
    background: #fff;
}
.s-toggle-btn.off::before {
    content: ""; display: inline-block;
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--s-slate-300);
}

.s-delete-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 34px; height: 34px;
    background: #fee2e2 !important;
    color: #991b1b !important;
    border: 0 !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    transition: background .15s;
}
.s-delete-btn:hover { background: #fecaca !important; color: #991b1b !important; }

.s-empty-row td {
    text-align: center !important;
    padding: 30px 20px !important;
    color: var(--s-slate-500) !important;
    font-size: 14px !important;
    font-style: italic;
}

@media (max-width: 768px) {
    .s-card-body { padding: 14px; }
    .s-info-card { flex-direction: column; }
    .s-config-table thead { display: none; }
    .s-btn-primary { margin-top: 8px; }
}
</style>
@endpush

@section("content")
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Scrapers &mdash; Auto Setup</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Cities added here run automatically on the existing cron schedule</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Scrapers &mdash; Auto</li>
        </ol>
    </div>
</div>

<div class="container-fluid px-0">

    {{-- Tabs --}}
    <ul class="nav nav-pills s-tabs">
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.scrapers.index') }}"><i class="fa fa-play"></i> Manual Run</a></li>
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.scrapers.auto') }}"><i class="fa fa-clock"></i> Auto Setup (cron)</a></li>
    </ul>

    @if(session('success'))
        <div class="s-alert s-alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="s-alert s-alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    {{-- How this works --}}
    <div class="s-info-card">
        <div class="s-info-icon"><i class="fas fa-info"></i></div>
        <div>
            <h6>How this works</h6>
            <p>
                Add cities to a source below. The scheduled cron job iterates through the active cities for that source and scrapes each one sequentially.
                Massage Republic runs <b>every 2 hours</b>; Ivy Societe runs <b>daily at 03:00</b>. Disable a city with the toggle to keep it in the list without running.
                If no cities are configured for a source, the cron falls back to a built-in default list so scheduled runs never stop.
            </p>
        </div>
    </div>

    @foreach($sources as $sourceKey => $source)
        @php
            $configured = ($entries[$sourceKey] ?? collect())->count();
            $activeC    = ($entries[$sourceKey] ?? collect())->where('is_active', true)->count();
        @endphp
        <div class="s-source-block">
            <div class="s-card">
                <div class="s-card-head">
                    <h5 class="s-title">
                        <span class="s-title-icon"><i class="fas fa-globe"></i></span>
                        {{ $source['label'] }}
                    </h5>
                    <span class="s-title-count">
                        <span class="s-count-chip configured"><i class="fas fa-list"></i> {{ $configured }} configured</span>
                        <span class="s-count-chip active"><i class="fas fa-bolt"></i> {{ $activeC }} active</span>
                    </span>
                </div>
                <div class="s-card-body">
                    <form method="post" action="{{ route('admin.scrapers.auto.store') }}" class="auto-add-form">
                        @csrf
                        <input type="hidden" name="source" value="{{ $sourceKey }}">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-8 mb-2">
                                <label class="form-label"><i class="fas fa-map-marker-alt"></i> Add cities</label>
                                <div class="auto-city-picker" data-source="{{ $sourceKey }}">
                                    <div class="auto-city-input">
                                        <input type="text" class="auto-city-text" placeholder="Type city name...">
                                    </div>
                                    <div class="auto-city-dropdown"></div>
                                    <div class="auto-city-hidden-inputs"></div>
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label class="form-label"><i class="fas fa-hashtag"></i> Limit / run</label>
                                <input type="number" name="limit_per_run" class="form-control" value="20" min="1" max="500" required>
                            </div>
                            <div class="col-md-2 mb-2">
                                <button type="submit" class="s-btn-primary"><i class="fa fa-plus"></i> Add</button>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <div class="table-responsive" style="margin: 0 -20px -20px; border-radius: 0 0 14px 14px; overflow: hidden;">
                        <table class="s-config-table">
                            <thead>
                                <tr>
                                    <th style="padding-left: 20px;">City</th>
                                    <th>Slug</th>
                                    <th class="text-center">Limit / run</th>
                                    <th class="text-center">Active</th>
                                    <th class="text-end" style="padding-right: 20px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($entries[$sourceKey] ?? []) as $entry)
                                    <tr>
                                        <td style="padding-left: 20px;">
                                            <span class="s-city-name">{{ $entry->city_name ?: '—' }}</span>
                                        </td>
                                        <td><code>{{ $entry->city_slug }}</code></td>
                                        <td class="text-center">
                                            <span class="s-limit">{{ $entry->limit_per_run }}</span>
                                        </td>
                                        <td class="text-center">
                                            <form method="post" action="{{ route('admin.scrapers.auto.toggle', $entry->id) }}" style="display:inline">
                                                @csrf
                                                <button type="submit" class="s-toggle-btn {{ $entry->is_active ? 'on' : 'off' }}">
                                                    {{ $entry->is_active ? 'ON' : 'OFF' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td class="text-end" style="padding-right: 20px;">
                                            <form method="post" action="{{ route('admin.scrapers.auto.destroy', $entry->id) }}" style="display:inline" onsubmit="return confirm('Remove {{ $entry->city_slug }} from the auto-scraper?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="s-delete-btn" title="Remove"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="s-empty-row"><td colspan="5">
                                        No cities configured — cron will use built-in defaults.
                                    </td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@push('js')
<script>
(function() {
    const CITIES = @json($cities);

    document.querySelectorAll('.auto-city-picker').forEach(initPicker);

    function initPicker(picker) {
        const inputWrap = picker.querySelector('.auto-city-input');
        const textInput = picker.querySelector('.auto-city-text');
        const dropdown  = picker.querySelector('.auto-city-dropdown');
        const hiddenBox = picker.querySelector('.auto-city-hidden-inputs');
        let selected = [];
        let activeIndex = -1;
        let filtered = [];

        function renderChips() {
            inputWrap.querySelectorAll('.auto-city-chip').forEach(c => c.remove());
            hiddenBox.innerHTML = '';
            selected.forEach(slug => {
                const match = CITIES.find(c => c.slug === slug);
                const label = match ? match.name : slug;
                const chip = document.createElement('span');
                chip.className = 'auto-city-chip';
                chip.innerHTML = `<span>${label}</span><button type="button" data-slug="${slug}" aria-label="Remove">&times;</button>`;
                inputWrap.insertBefore(chip, textInput);
                const h = document.createElement('input');
                h.type = 'hidden'; h.name = 'cities[]'; h.value = slug;
                hiddenBox.appendChild(h);
            });
        }

        function filterAndRender() {
            const q = textInput.value.trim().toLowerCase();
            filtered = [];
            for (const c of CITIES) {
                if (selected.includes(c.slug)) continue;
                if (q && !c.slug.toLowerCase().includes(q) && !c.name.toLowerCase().includes(q)) continue;
                filtered.push(c);
                if (filtered.length >= 100) break;
            }
            activeIndex = filtered.length ? 0 : -1;
            dropdown.innerHTML = '';
            if (filtered.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'auto-city-empty';
                empty.textContent = q ? 'No matches.' : 'Start typing...';
                dropdown.appendChild(empty);
            } else {
                filtered.forEach((c, i) => {
                    const opt = document.createElement('div');
                    opt.className = 'auto-city-option' + (i === activeIndex ? ' active' : '');
                    opt.dataset.slug = c.slug;
                    opt.textContent = c.name + ' (' + c.slug + ')';
                    dropdown.appendChild(opt);
                });
            }
        }

        function openDropdown() { filterAndRender(); dropdown.classList.add('open'); }
        function closeDropdown() { dropdown.classList.remove('open'); }

        function addSlug(slug) {
            if (selected.includes(slug)) return;
            selected.push(slug); renderChips(); textInput.value = '';
            filterAndRender(); textInput.focus();
        }
        function removeSlug(slug) {
            selected = selected.filter(s => s !== slug); renderChips(); filterAndRender();
        }
        function renderActive() {
            dropdown.querySelectorAll('.auto-city-option').forEach((el, i) => {
                el.classList.toggle('active', i === activeIndex);
            });
        }

        inputWrap.addEventListener('click', (e) => {
            const btn = e.target.closest('.auto-city-chip button');
            if (btn) { e.preventDefault(); removeSlug(btn.dataset.slug); return; }
            textInput.focus(); openDropdown();
        });
        textInput.addEventListener('focus', openDropdown);
        textInput.addEventListener('input', filterAndRender);
        textInput.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown') { e.preventDefault(); if (filtered.length) { activeIndex = (activeIndex + 1) % filtered.length; renderActive(); } }
            else if (e.key === 'ArrowUp') { e.preventDefault(); if (filtered.length) { activeIndex = (activeIndex - 1 + filtered.length) % filtered.length; renderActive(); } }
            else if (e.key === 'Enter') { if (activeIndex >= 0 && filtered[activeIndex]) { e.preventDefault(); addSlug(filtered[activeIndex].slug); } }
            else if (e.key === 'Backspace' && textInput.value === '' && selected.length) { e.preventDefault(); removeSlug(selected[selected.length - 1]); }
            else if (e.key === 'Escape') { closeDropdown(); }
        });
        dropdown.addEventListener('click', (e) => {
            const opt = e.target.closest('.auto-city-option');
            if (opt) addSlug(opt.dataset.slug);
        });
        document.addEventListener('click', (e) => {
            if (!picker.contains(e.target)) closeDropdown();
        });

        picker.closest('form').addEventListener('submit', (e) => {
            if (selected.length === 0) {
                e.preventDefault();
                alert('Please select at least one city.');
            }
        });

        renderChips();
    }
})();
</script>
@endpush
