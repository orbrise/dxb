@extends('admin.layout.master')

@push('css')
<style>
    .sortable-table tbody {
        cursor: move;
    }
    
    .sortable-table tbody tr {
        transition: all 0.3s ease;
    }
    
    .sortable-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .sortable-table tbody tr.sortable-ghost {
        opacity: 0.4;
        background-color: #e3f2fd;
    }
    
    .sortable-table tbody tr.sortable-chosen {
        background-color: #e3f2fd;
    }
    
    .drag-handle {
        cursor: grab;
        color: #6c757d;
        font-size: 18px;
    }
    
    .drag-handle:hover {
        color: #495057;
    }
    
    .drag-handle:active {
        cursor: grabbing;
    }
    
    .alert-info {
        border-left: 4px solid #17a2b8;
    }
</style>
@endpush

@section('content')
 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Pages</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage pages effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Pages</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>

<div class="d-flex justify-content-between align-items-center mb-3 mt-3">
    <a href="{{ route('pages.create') }}" class="btn btn-primary">Create New Page</a>
    <form method="GET" action="{{ route('pages.index') }}" class="form-inline">
        <label class="mr-2">Per page</label>
        @php($pp = request('perPage', $pages->perPage()))
        <select name="perPage" class="form-control" onchange="this.form.submit()">
            <option value="10" {{ $pp==10 ? 'selected' : '' }}>10</option>
            <option value="25" {{ $pp==25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ $pp==50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ $pp==100 ? 'selected' : '' }}>100</option>
        </select>
    </form>
  </div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="alert alert-info">
    <i class="fas fa-info-circle"></i>
    <strong>Drag & Drop:</strong> You can drag and drop rows to reorder pages. The order will be saved automatically.
</div>

<div class="row mt-3 mb-3">
<div class="col-md-12">
<div class="card">
<div class="card-body">

<table class="table table-bordered sortable-table" id="pages-table">
    <thead>
        <tr>
            <th width="50">Order</th>
            <th>ID</th>
            <th>Title</th>
            <th>Slug</th>
            <th>Status</th>
            <th width="200">Actions</th>
        </tr>
    </thead>
    <tbody id="sortable-pages">
        @foreach($pages as $page)
        <tr data-id="{{ $page->id }}">
            <td class="text-center">
                <i class="fas fa-grip-vertical drag-handle"></i>
                <span class="ms-2">{{ $page->order_index ?? 0 }}</span>
            </td>
            <td>{{ $page->id }}</td>
            <td>{{ $page->title }}</td>
            <td>{{ $page->slug }}</td>
            <td>
                @if($page->is_published)
                    <span class="badge bg-success">Published</span>
                @else
                    <span class="badge bg-warning">Draft</span>
                @endif
            </td>
            <td>
                <a href="{{ route('pages.edit', $page->id) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('pages.destroy', $page->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
 </table>

 <div class="d-flex justify-content-between align-items-center mt-3">
     <div class="text-muted small">
         Showing {{ $pages->firstItem() }} to {{ $pages->lastItem() }} of {{ $pages->total() }} entries
     </div>
     <div>
         {{ $pages->appends(request()->query())->links() }}
     </div>
 </div>

</div>
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
                    const orderCell = row.querySelector('td span');
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
                        // Show success message
                        showAlert('success', data.message || 'Page order updated successfully');
                    } else {
                        showAlert('error', 'Failed to update page order');
                        // Reload page if update failed
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'An error occurred while updating page order');
                    // Reload page if update failed
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
            <div class="alert ${alertClass} auto-dismiss" style="margin-top: 10px;">
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