@php
    // ---- Required vars ----
    $entity        = $entity        ?? 'Item';
    $entityPlural  = $entityPlural  ?? ($entity.'s');
    $items         = $items         ?? collect();
    $addRoute      = $addRoute      ?? '#';
    $updateUrl     = $updateUrl     ?? '#';
    $deleteUrl     = $deleteUrl     ?? '#';
    $icon          = $icon          ?? 'fas fa-list';
    $breadcrumb    = $breadcrumb    ?? $entityPlural;
@endphp

@push('css')
<style>
/* ===== Lookup page: shared modern design ===== */
:root {
    --l-primary: #6366f1;
    --l-primary-dark: #4f46e5;
    --l-success: #10b981;
    --l-danger: #ef4444;
    --l-warning: #f59e0b;
    --l-slate-50: #f8fafc;
    --l-slate-100: #f1f5f9;
    --l-slate-200: #e2e8f0;
    --l-slate-300: #cbd5e1;
    --l-slate-500: #64748b;
    --l-slate-600: #475569;
    --l-slate-700: #334155;
    --l-slate-800: #1e293b;
}
span.input { display: none; }

.l-stats-row {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin: 4px 0 18px;
}
.l-stat {
    position: relative;
    padding: 16px 18px;
    border-radius: 12px;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(15,23,42,.08);
}
.l-stat .l-stat-label {
    font-size: 12px; text-transform: uppercase; letter-spacing: .06em;
    opacity: .9; margin: 0 0 4px; font-weight: 600;
}
.l-stat .l-stat-value {
    font-size: 26px; font-weight: 700; line-height: 1.1; margin: 0;
}
.l-stat .l-stat-icon {
    position: absolute; right: 14px; top: 50%;
    transform: translateY(-50%); font-size: 34px; opacity: .35;
}
.l-stat.total  { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.l-stat.latest { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.l-stat.info   { background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%); }

.l-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 18px;
    align-items: start;
}
@media (max-width: 992px) {
    .l-grid { grid-template-columns: 1fr; }
    .l-stats-row { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 600px) {
    .l-stats-row { grid-template-columns: 1fr; }
}

.l-card {
    background: #fff;
    border: 1px solid var(--l-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
}
.l-card-head {
    padding: 16px 20px;
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    border-bottom: 1px solid var(--l-slate-100);
    display: flex;
    align-items: center;
    gap: 10px;
}
.l-card-head .l-title-icon {
    width: 34px; height: 34px; border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(99, 102, 241, .12); color: var(--l-primary-dark); font-size: 14px;
}
.l-card-head .l-title {
    font-size: 15px; font-weight: 700; color: var(--l-slate-800); margin: 0;
}
.l-card-head .l-title small {
    display: block; font-size: 12px; font-weight: 400; color: var(--l-slate-500);
}
.l-card-body {
    padding: 20px;
}

.l-field label {
    display: block; font-size: 12px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .04em;
    color: var(--l-slate-500); margin-bottom: 6px;
}
.l-input {
    width: 100%; height: 42px; padding: 8px 12px;
    font-size: 14px; color: var(--l-slate-800);
    background: #fff; border: 1px solid var(--l-slate-200);
    border-radius: 9px;
    transition: border-color .15s, box-shadow .15s;
}
.l-input:focus {
    outline: none; border-color: var(--l-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}
.l-btn-primary {
    display: inline-flex; align-items: center; gap: 6px;
    height: 42px; padding: 0 20px; border-radius: 9px;
    background: linear-gradient(135deg, var(--l-primary) 0%, var(--l-primary-dark) 100%);
    color: #fff; font-size: 14px; font-weight: 600;
    border: 0; cursor: pointer;
    box-shadow: 0 4px 12px rgba(99, 102, 241, .35);
    transition: box-shadow .15s;
}
.l-btn-primary:hover { color: #fff; box-shadow: 0 6px 16px rgba(99, 102, 241, .45); }

.l-alert-success {
    display: flex; align-items: center; gap: 8px;
    background: #d1fae5; color: #065f46;
    padding: 10px 14px; border-radius: 9px;
    font-size: 13px; margin-top: 14px; font-weight: 500;
}
.l-alert-error {
    display: block;
    color: #b91c1c; font-size: 12px; margin-top: 6px;
}

.l-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin: 0;
    font-size: 14px;
}
.l-table thead th {
    background: var(--l-slate-50);
    color: var(--l-slate-500);
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    padding: 12px 16px;
    border: 0;
    border-bottom: 1px solid var(--l-slate-200);
    text-align: left;
}
.l-table thead th:last-child { text-align: right; }
.l-table tbody td {
    padding: 12px 16px;
    vertical-align: middle;
    border: 0;
    border-bottom: 1px solid var(--l-slate-100);
    color: var(--l-slate-700);
    background: #fff;
}
.l-table tbody tr:hover td { background: #fafbff; }
.l-table tbody tr:last-child td { border-bottom: 0; }
.l-table tbody td:last-child { text-align: right; white-space: nowrap; }

.l-index-chip {
    display: inline-block;
    padding: 3px 9px;
    font-size: 11px; font-weight: 700;
    border-radius: 6px;
    color: var(--l-slate-500);
    background: var(--l-slate-100);
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}
.l-name-cell {
    display: flex; align-items: center; gap: 10px;
}
.l-name-avatar {
    width: 34px; height: 34px; border-radius: 50%;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: #fff; display: inline-flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; text-transform: uppercase;
    flex-shrink: 0;
}
.l-name-text { font-weight: 600; color: var(--l-slate-800); }

.l-inline-input {
    width: 100%; max-width: 260px;
    height: 36px; padding: 6px 12px;
    font-size: 13px; color: var(--l-slate-800);
    background: #fff; border: 1px solid var(--l-primary);
    border-radius: 8px;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}

.l-action-btn {
    display: inline-flex !important;
    align-items: center; gap: 5px;
    padding: 6px 12px !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    border: 1px solid transparent !important;
    line-height: 1 !important;
    cursor: pointer;
    transition: background .15s, box-shadow .15s;
}
.l-btn-edit    { background: #eef2ff !important; color: #4338ca !important; }
.l-btn-edit:hover { background: #e0e7ff !important; }
.l-btn-update  { background: #d1fae5 !important; color: #065f46 !important; }
.l-btn-update:hover { background: #a7f3d0 !important; }
.l-btn-del     { background: #fee2e2 !important; color: #991b1b !important; }
.l-btn-del:hover { background: #fecaca !important; }
.l-success-msg { color: var(--l-success); font-size: 12px; font-weight: 600; margin-left: 6px; }

.l-empty {
    text-align: center; padding: 50px 20px; color: var(--l-slate-500);
}
.l-empty-icon {
    width: 72px; height: 72px; border-radius: 50%;
    background: var(--l-slate-100); color: var(--l-slate-300);
    font-size: 28px; display: inline-flex; align-items: center; justify-content: center;
    margin-bottom: 14px;
}
</style>
@endpush

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">{{ $entity }}</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Manage {{ strtolower($entityPlural) }} effectively</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">{{ $breadcrumb }}</li>
        </ol>
    </div>
</div>

<div class="container-fluid px-0">

    {{-- Stats --}}
    <div class="l-stats-row">
        <div class="l-stat total">
            <p class="l-stat-label">Total {{ $entityPlural }}</p>
            <p class="l-stat-value">{{ number_format(count($items)) }}</p>
            <i class="{{ $icon }} l-stat-icon"></i>
        </div>
        <div class="l-stat latest">
            <p class="l-stat-label">Latest</p>
            <p class="l-stat-value" style="font-size:18px; padding-top:4px;">
                {{ count($items) > 0 ? \Illuminate\Support\Str::limit(collect($items)->last()->name ?? '—', 24) : '—' }}
            </p>
            <i class="fas fa-clock l-stat-icon"></i>
        </div>
        <div class="l-stat info">
            <p class="l-stat-label">Quick Action</p>
            <p class="l-stat-value" style="font-size:16px; padding-top:6px;">Add or Edit {{ $entityPlural }}</p>
            <i class="fas fa-bolt l-stat-icon"></i>
        </div>
    </div>

    <div class="l-grid">

        {{-- Create form --}}
        <div class="l-card">
            <div class="l-card-head">
                <span class="l-title-icon"><i class="fas fa-plus"></i></span>
                <h5 class="l-title">
                    Create New
                    <small>Add a new {{ strtolower($entity) }}</small>
                </h5>
            </div>
            <div class="l-card-body">
                <form method="post" action="{{ route($addRoute) }}">
                    {{ csrf_field() }}
                    <div class="l-field">
                        <label>{{ $entity }} Name</label>
                        <input type="text" class="l-input" name="name" placeholder="Enter {{ strtolower($entity) }} name" autocomplete="off">
                        @error('name')
                            <span class="l-alert-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div style="margin-top:16px;">
                        <button type="submit" class="l-btn-primary">
                            <i class="fas fa-plus-circle"></i> Submit
                        </button>
                    </div>
                    @if(!empty(session('success')))
                        <div class="l-alert-success">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- List --}}
        <div class="l-card">
            <div class="l-card-head">
                <span class="l-title-icon"><i class="{{ $icon }}"></i></span>
                <h5 class="l-title">
                    All {{ $entityPlural }}
                    <small>{{ count($items) }} {{ strtolower($entityPlural) }} available</small>
                </h5>
            </div>
            <div id="alert" style="padding: 0 20px;"></div>
            <div style="overflow-x: auto;">
                @if(count($items) > 0)
                    <table class="l-table">
                        <thead>
                            <tr>
                                <th style="width:80px;">#</th>
                                <th>{{ $entity }}</th>
                                <th style="width:280px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $k => $item)
                            <tr id="row{{ $item->id }}">
                                <td><span class="l-index-chip">#{{ $item->id }}</span></td>
                                <td>
                                    <span class="text" id="text{{ $k }}">
                                        <div class="l-name-cell">
                                            <span class="l-name-avatar">{{ strtoupper(mb_substr($item->name, 0, 1)) }}</span>
                                            <span class="l-name-text">{{ $item->name }}</span>
                                        </div>
                                    </span>
                                    <span class="input" id="input{{ $k }}">
                                        <input type="text" class="l-inline-input" value="{{ $item->name }}" id="name{{ $k }}">
                                    </span>
                                </td>
                                <td>
                                    <button type="button" key="{{ $k }}" id="editthis" class="l-action-btn l-btn-edit">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </button>
                                    <button type="button" key="{{ $k }}" rid="{{ $item->id }}" id="updatethis" class="l-action-btn l-btn-update">
                                        <i class="fa-solid fa-floppy-disk"></i> Update
                                    </button>
                                    <button onclick="return confirm('Are you sure?')" type="button" id="del" key="{{ $k }}" rid="{{ $item->id }}" class="l-action-btn l-btn-del">
                                        <i class="fa-solid fa-trash-can"></i> Delete
                                    </button>
                                    <span id="success{{ $k }}"></span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="l-empty">
                        <div class="l-empty-icon"><i class="fas fa-inbox"></i></div>
                        <p style="margin:0; font-size:15px;">No {{ strtolower($entityPlural) }} yet — add your first one on the left.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
(function(){
    var token = "{{ csrf_token() }}";
    var updateUrl = "{{ url($updateUrl) }}";
    var deleteUrl = "{{ url($deleteUrl) }}";
    var entityLabel = @json($entity);
    var edited = 0;

    jQuery.fn.clickToggle = function(a, b) {
        return this.on("click", function(ev) {
            [b, a][this.$_io ^= 1].call(this, ev);
        });
    };

    $("button#editthis").clickToggle(function() {
        var id = $(this).attr("key");
        $("span#text" + id).hide();
        $("span#input" + id).show();
        $("input#name" + id).trigger('focus');
        edited = 1;
    }, function() {
        var id = $(this).attr("key");
        $("span#text" + id).show();
        $("span#input" + id).hide();
        edited = 0;
    });

    $("button#updatethis").click(function(){
        if (edited === 0) {
            alert("Click Edit on a row first.");
            return false;
        }
        var id = $(this).attr("key");
        var rid = $(this).attr("rid");
        var name = $("input#name" + id).val();

        $.post(updateUrl, {_token: token, name: name, rid: rid}, function(data){
            if (data == "success") {
                $("span.l-name-text", "tr#row" + rid).text(name);
                $("span.l-name-avatar", "tr#row" + rid).text(name.charAt(0).toUpperCase());
                $("span#text" + id).show();
                $("span#input" + id).hide();
                $("span#success" + id).html("<span class='l-success-msg'><i class='fas fa-check'></i> Saved</span>")
                    .delay(2500).fadeOut('slow', function(){ $(this).html('').show(); });
                edited = 0;
            }
        });
    });

    $("button#del").click(function(){
        var rid = $(this).attr("rid");
        $.post(deleteUrl, {_token: token, rid: rid}, function(data){
            if (data == "success") {
                $("tr#row" + rid).fadeOut(200, function(){ $(this).remove(); });
                $("div#alert").html(
                    "<div class='l-alert-success' style='margin: 14px 20px;'><i class='fas fa-check-circle'></i> " +
                    entityLabel + " has been deleted successfully</div>"
                ).find('.l-alert-success').delay(2500).fadeOut('slow');
            }
        });
    });
})();
</script>
@endpush
