@extends('admin.layout.master')

@push('css')
<style>
/* ===== Pages admin: modern redesign ===== */
:root {
    --pg-primary: #6366f1;
    --pg-primary-dark: #4f46e5;
    --pg-success: #10b981;
    --pg-danger: #ef4444;
    --pg-warning: #f59e0b;
    --pg-info: #06b6d4;
    --pg-slate-50: #f8fafc;
    --pg-slate-100: #f1f5f9;
    --pg-slate-200: #e2e8f0;
    --pg-slate-300: #cbd5e1;
    --pg-slate-500: #64748b;
    --pg-slate-600: #475569;
    --pg-slate-700: #334155;
    --pg-slate-800: #1e293b;
}

/* Stats strip */
.pg-stats-row {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin: 4px 0 18px;
}
.pg-stat {
    position: relative;
    padding: 16px 18px;
    border-radius: 12px;
    color: #fff;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(15,23,42,.08);
}
.pg-stat .pg-stat-label {
    font-size: 12px; text-transform: uppercase; letter-spacing: .06em;
    opacity: .9; margin: 0 0 4px; font-weight: 600;
}
.pg-stat .pg-stat-value {
    font-size: 26px; font-weight: 700; line-height: 1.1; margin: 0;
}
.pg-stat .pg-stat-icon {
    position: absolute; right: 14px; top: 50%;
    transform: translateY(-50%); font-size: 34px; opacity: .35;
}
.pg-stat.total     { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
.pg-stat.published { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.pg-stat.drafts    { background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%); }

/* Toolbar row: create button + per page */
.pg-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    margin-bottom: 18px;
    flex-wrap: wrap;
}
.pg-create-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, var(--pg-primary) 0%, var(--pg-primary-dark) 100%);
    color: #fff !important;
    border: 0;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(99, 102, 241, .35);
    transition: box-shadow .15s;
}
.pg-create-btn:hover { color: #fff; box-shadow: 0 6px 18px rgba(99, 102, 241, .5); text-decoration: none; }
.pg-perpage {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    border: 1px solid var(--pg-slate-200);
    border-radius: 10px;
    padding: 6px 12px;
    font-size: 13px;
    color: var(--pg-slate-600);
    box-shadow: 0 2px 8px rgba(15,23,42,.04);
}
.pg-perpage .form-control {
    border: 1px solid var(--pg-slate-200);
    border-radius: 8px;
    padding: 4px 26px 4px 10px;
    font-size: 13px;
    color: var(--pg-slate-700);
    background: #fff;
    height: 32px;
    cursor: pointer;
    width: auto;
}
.pg-perpage .form-control:focus {
    outline: none;
    border-color: var(--pg-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
}

/* Info banner */
.pg-info-banner {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 1px solid #bfdbfe;
    border-radius: 12px;
    margin-bottom: 18px;
    color: #1e3a8a;
    font-size: 13px;
}
.pg-info-banner .pg-info-icon {
    width: 34px; height: 34px; border-radius: 10px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: #fff;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
    box-shadow: 0 3px 10px rgba(59, 130, 246, .35);
}
.pg-info-banner strong { color: #1e40af; }

/* Success alert */
.pg-alert-success {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 13px; font-weight: 500;
    margin-bottom: 14px;
    background: #d1fae5; color: #065f46;
}

/* Auto-dismiss alerts (JS) */
.alert.auto-dismiss {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 13px; font-weight: 500;
    margin-top: 12px !important;
    border: 0;
}
.alert.auto-dismiss.alert-success { background: #d1fae5; color: #065f46; }
.alert.auto-dismiss.alert-danger  { background: #fee2e2; color: #991b1b; }

/* Main card */
.pg-card {
    background: #fff;
    border: 1px solid var(--pg-slate-200);
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(15,23,42,.06);
    overflow: hidden;
}

/* Table */
.sortable-table {
    width: 100%;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 13.5px;
}
.sortable-table thead th {
    background: var(--pg-slate-50);
    color: var(--pg-slate-500);
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    padding: 14px 16px;
    border: 0;
    border-bottom: 1px solid var(--pg-slate-200);
    text-align: left;
    white-space: nowrap;
}
.sortable-table thead th:last-child { text-align: right; }
.sortable-table tbody td {
    padding: 14px 16px;
    vertical-align: middle;
    border: 0;
    border-bottom: 1px solid var(--pg-slate-100);
    color: var(--pg-slate-700);
    background: #fff;
}
.sortable-table tbody tr {
    cursor: move;
    transition: background .12s;
}
.sortable-table tbody tr:hover td { background: #fafbff; }
.sortable-table tbody tr:last-child td { border-bottom: 0; }
.sortable-table tbody tr.sortable-ghost td { opacity: .4; background: #eef2ff !important; }
.sortable-table tbody tr.sortable-chosen td { background: #eef2ff !important; }

/* Order cell w/ drag handle */
.pg-order-cell {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.drag-handle {
    cursor: grab;
    color: var(--pg-slate-300);
    font-size: 15px;
    padding: 6px;
    border-radius: 6px;
    transition: all .15s;
}
.drag-handle:hover {
    color: var(--pg-primary);
    background: #eef2ff;
}
.drag-handle:active { cursor: grabbing; }
.pg-order-num {
    display: inline-flex;
    align-items: center; justify-content: center;
    min-width: 28px; height: 28px;
    padding: 0 8px;
    background: var(--pg-slate-100);
    color: var(--pg-slate-700);
    border-radius: 6px;
    font-weight: 700;
    font-size: 12px;
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}

/* ID chip */
.pg-id-chip {
    display: inline-block;
    padding: 3px 10px;
    font-size: 12px; font-weight: 700;
    border-radius: 6px;
    color: var(--pg-slate-500);
    background: var(--pg-slate-100);
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}

.pg-title-cell {
    font-weight: 600;
    color: var(--pg-slate-800);
    font-size: 14px;
}
.pg-slug-cell {
    display: inline-block;
    padding: 3px 10px;
    font-size: 12.5px;
    color: var(--pg-slate-600);
    background: var(--pg-slate-50);
    border: 1px solid var(--pg-slate-100);
    border-radius: 6px;
    font-family: 'SFMono-Regular', Menlo, Consolas, monospace;
}

/* Status badges */
.pg-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}
.pg-status i { font-size: 10px; }
.pg-status-published { background: #d1fae5; color: #065f46; }
.pg-status-draft     { background: #fef3c7; color: #92400e; }

/* Action buttons */
.pg-actions-cell {
    display: inline-flex; gap: 6px;
    align-items: center;
    justify-content: flex-end;
}
.pg-actions-cell form { margin: 0; display: inline; }
.pg-btn-edit {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 14px;
    background: #eef2ff;
    color: var(--pg-primary-dark) !important;
    border: 0;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: background .15s;
    line-height: 1;
}
.pg-btn-edit:hover { background: #e0e7ff; color: var(--pg-primary-dark) !important; text-decoration: none; }
.pg-btn-delete {
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
.pg-btn-delete:hover { background: #fecaca; color: #991b1b !important; }

/* Empty */
.pg-empty td {
    text-align: center !important;
    padding: 60px 20px !important;
    color: var(--pg-slate-500) !important;
}

/* Pagination footer */
.pg-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    padding: 16px 22px;
    border-top: 1px solid var(--pg-slate-100);
    background: var(--pg-slate-50);
    font-size: 13px;
    color: var(--pg-slate-600);
}
.pg-pagination .pagination { margin: 0; }
.pg-pagination .pagination .page-link {
    color: var(--pg-slate-600);
    border-color: var(--pg-slate-200);
    padding: 6px 12px;
    font-size: 13px;
    margin: 0 2px;
    border-radius: 7px !important;
}
.pg-pagination .pagination .page-item.active .page-link {
    background: var(--pg-primary);
    border-color: var(--pg-primary);
    color: #fff;
}

/* Responsive */
@media (max-width: 992px) {
    .pg-stats-row { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 600px) {
    .pg-stats-row { grid-template-columns: 1fr; }
    .pg-toolbar { flex-direction: column; align-items: stretch; }
    .pg-create-btn { justify-content: center; }
    .pg-perpage { justify-content: space-between; }
    .sortable-table thead { display: none; }
    .pg-pagination { flex-direction: column; text-align: center; }
}
</style>
@endpush

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Pages</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Manage pages effectively</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Pages</li>
        </ol>
    </div>
</div>

@php
    $totalPages     = $pages->total();
    $publishedOnPage = collect($pages->items())->filter(fn($p) => $p->is_published)->count();
    $draftOnPage     = collect($pages->items())->filter(fn($p) => !$p->is_published)->count();
    $pp = request('perPage', $pages->perPage());
@endphp

<div class="container-fluid px-0">

    {{-- Stats --}}
    <div class="pg-stats-row">
        <div class="pg-stat total">
            <p class="pg-stat-label">Total Pages</p>
            <p class="pg-stat-value">{{ number_format($totalPages) }}</p>
            <i class="fas fa-file-alt pg-stat-icon"></i>
        </div>
        <div class="pg-stat published">
            <p class="pg-stat-label">Published · On This Page</p>
            <p class="pg-stat-value">{{ number_format($publishedOnPage) }}</p>
            <i class="fas fa-check-circle pg-stat-icon"></i>
        </div>
        <div class="pg-stat drafts">
            <p class="pg-stat-label">Drafts · On This Page</p>
            <p class="pg-stat-value">{{ number_format($draftOnPage) }}</p>
            <i class="fas fa-edit pg-stat-icon"></i>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="pg-toolbar">
        <a href="{{ route('pages.create') }}" class="pg-create-btn">
            <i class="fas fa-plus"></i> Create New Page
        </a>
        <form method="GET" action="{{ route('pages.index') }}">
            <label class="pg-perpage mb-0">
                <span>Per page</span>
                <select name="perPage" class="form-control" onchange="this.form.submit()">
                    <option value="10"  {{ $pp==10  ? 'selected' : '' }}>10</option>
                    <option value="25"  {{ $pp==25  ? 'selected' : '' }}>25</option>
                    <option value="50"  {{ $pp==50  ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $pp==100 ? 'selected' : '' }}>100</option>
                </select>
            </label>
        </form>
    </div>

    @if(session('success'))
        <div class="pg-alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Info banner --}}
    <div class="pg-info-banner">
        <div class="pg-info-icon"><i class="fas fa-arrows-alt"></i></div>
        <div>
            <strong>Drag &amp; Drop:</strong> You can drag and drop rows to reorder pages. The order will be saved automatically.
        </div>
    </div>

    {{-- Main card --}}
    <div class="pg-card">
        <div style="overflow-x: auto;">
            <table class="table sortable-table" id="pages-table">
                <thead>
                    <tr>
                        <th style="width:110px;">Order</th>
                        <th style="width:80px;">ID</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th style="width:140px;">Status</th>
                        <th style="width:200px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="sortable-pages">
                    @forelse($pages as $page)
                    <tr data-id="{{ $page->id }}">
                        <td>
                            <span class="pg-order-cell">
                                <i class="fas fa-grip-vertical drag-handle" title="Drag to reorder"></i>
                                <span class="pg-order-num">{{ $page->order_index ?? 0 }}</span>
                            </span>
                        </td>
                        <td><span class="pg-id-chip">#{{ $page->id }}</span></td>
                        <td><span class="pg-title-cell">{{ $page->title }}</span></td>
                        <td><span class="pg-slug-cell">/{{ $page->slug }}</span></td>
                        <td>
                            @if($page->is_published)
                                <span class="pg-status pg-status-published"><i class="fas fa-circle"></i> Published</span>
                            @else
                                <span class="pg-status pg-status-draft"><i class="fas fa-pen"></i> Draft</span>
                            @endif
                        </td>
                        <td>
                            <div class="pg-actions-cell">
                                <a href="{{ route('pages.edit', $page->id) }}" class="pg-btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('pages.destroy', $page->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="pg-btn-delete" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="pg-empty"><td colspan="6">
                        <div style="width:80px; height:80px; border-radius:50%; background:#f1f5f9; color:#cbd5e1; font-size:32px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:14px;">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <p style="color:#64748b; margin:0; font-size:15px;">No pages yet — create your first page.</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pg-pagination">
            <div>
                @if($pages->total() > 0)
                    Showing <strong>{{ $pages->firstItem() }}</strong> to
                    <strong>{{ $pages->lastItem() }}</strong> of
                    <strong>{{ number_format($pages->total()) }}</strong> entries
                @else
                    No pages found
                @endif
            </div>
            <div>{{ $pages->appends(request()->query())->links() }}</div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortableElement = document.getElementById('sortable-pages');

    if (sortableElement) {
        const sortable = new Sortable(sortableElement, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function(evt) {
                // Get the new order of pages
                const rows = document.querySelectorAll('#sortable-pages tr');
                const pageIds = [];

                rows.forEach(function(row) {
                    pageIds.push(row.getAttribute('data-id'));
                });

                // Update order numbers in the UI
                rows.forEach(function(row, index) {
                    const orderCell = row.querySelector('td span.pg-order-num');
                    if (orderCell) {
                        orderCell.textContent = index + 1;
                    }
                });

                // Send AJAX request to update order
                fetch('{{ route("admin.pages.update-order") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        pages: pageIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message || 'Page order updated successfully');
                    } else {
                        showAlert('error', 'Failed to update page order');
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'An error occurred while updating page order');
                    location.reload();
                });
            }
        });
    }

    function showAlert(type, message) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert.auto-dismiss');
        existingAlerts.forEach(alert => alert.remove());

        // Create new alert
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} auto-dismiss">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                ${message}
            </div>
        `;

        // Insert alert after the page title
        const pageTitle = document.querySelector('.page-title');
        if (pageTitle) {
            pageTitle.insertAdjacentHTML('afterend', alertHtml);

            // Auto-dismiss after 3 seconds
            setTimeout(() => {
                const alert = document.querySelector('.alert.auto-dismiss');
                if (alert) {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            }, 3000);
        }
    }
});
</script>
@endpush
