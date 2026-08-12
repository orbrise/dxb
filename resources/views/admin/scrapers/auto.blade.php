@extends("admin.layout.master")

@push('css')
<style>
    .auto-source-block { margin-bottom: 24px; }
    .auto-city-picker { position: relative; }
    .auto-city-input {
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
    .auto-city-input:focus-within { border-color: #86b7fe; box-shadow: 0 0 0 0.15rem rgba(13,110,253,.15); }
    .auto-city-chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: #e7f1ff; color: #0d6efd;
        padding: 2px 8px; border-radius: 12px; font-size: 12px;
    }
    .auto-city-chip button {
        background: transparent; border: 0; color: #0d6efd;
        cursor: pointer; padding: 0; font-size: 14px; line-height: 1;
    }
    .auto-city-text {
        border: 0; outline: 0; flex: 1; min-width: 120px; font-size: 14px;
    }
    .auto-city-dropdown {
        position: absolute; top: 100%; left: 0; right: 0; z-index: 10;
        background: #fff; border: 1px solid #dee2e6; border-radius: 4px;
        max-height: 240px; overflow-y: auto; margin-top: 2px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.08);
        display: none;
    }
    .auto-city-dropdown.open { display: block; }
    .auto-city-option {
        padding: 8px 12px; cursor: pointer; font-size: 14px;
    }
    .auto-city-option:hover, .auto-city-option.active { background: #f1f3f5; }
    .auto-city-empty { padding: 8px 12px; color: #6c757d; font-size: 13px; }
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

<ul class="nav nav-pills mb-3">
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.scrapers.index') }}">Manual Run</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('admin.scrapers.auto') }}">Auto Setup (cron)</a></li>
</ul>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card mb-3">
    <div class="card-body">
        <h6 class="mb-1">How this works</h6>
        <p class="text-muted mb-0" style="font-size: 13px;">
            Add cities to a source below. The scheduled cron job iterates through the active cities for that source and scrapes each one sequentially.
            Massage Republic runs <b>every 2 hours</b>; Ivy Societe runs <b>daily at 03:00</b>. Disable a city with the toggle to keep it in the list without running.
            If no cities are configured for a source, the cron falls back to a built-in default list so scheduled runs never stop.
        </p>
    </div>
</div>

@foreach($sources as $sourceKey => $source)
    <div class="auto-source-block">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">{{ $source['label'] }}</h5>
                <small class="text-muted">
                    {{ ($entries[$sourceKey] ?? collect())->count() }} configured
                    &middot;
                    {{ ($entries[$sourceKey] ?? collect())->where('is_active', true)->count() }} active
                </small>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('admin.scrapers.auto.store') }}" class="auto-add-form">
                    @csrf
                    <input type="hidden" name="source" value="{{ $sourceKey }}">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label">Add cities</label>
                            <div class="auto-city-picker" data-source="{{ $sourceKey }}">
                                <div class="auto-city-input">
                                    <input type="text" class="auto-city-text" placeholder="Type city name...">
                                </div>
                                <div class="auto-city-dropdown"></div>
                                <div class="auto-city-hidden-inputs"></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Limit / run</label>
                            <input type="number" name="limit_per_run" class="form-control" value="20" min="1" max="500" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="fa fa-plus"></i> Add</button>
                        </div>
                    </div>
                </form>

                <hr class="my-3">

                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>City</th>
                                <th>Slug</th>
                                <th class="text-center">Limit / run</th>
                                <th class="text-center">Active</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($entries[$sourceKey] ?? []) as $entry)
                                <tr>
                                    <td>{{ $entry->city_name ?: '—' }}</td>
                                    <td><code>{{ $entry->city_slug }}</code></td>
                                    <td class="text-center">{{ $entry->limit_per_run }}</td>
                                    <td class="text-center">
                                        <form method="post" action="{{ route('admin.scrapers.auto.toggle', $entry->id) }}" style="display:inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $entry->is_active ? 'btn-success' : 'btn-outline-secondary' }}">
                                                {{ $entry->is_active ? 'ON' : 'OFF' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <form method="post" action="{{ route('admin.scrapers.auto.destroy', $entry->id) }}" style="display:inline" onsubmit="return confirm('Remove {{ $entry->city_slug }} from the auto-scraper?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-3">No cities configured — cron will use built-in defaults.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endforeach
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
