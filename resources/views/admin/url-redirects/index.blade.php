@extends("admin.layout.master")

@push('css')
<style>
    .redirect-form {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .redirect-row {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 15px;
        position: relative;
    }

    .remove-row {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        font-size: 12px;
        cursor: pointer;
        z-index: 99;
    }

    .add-more-btn {
        background: #28a745;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 10px 20px;
        cursor: pointer;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .save-all-btn {
        background: #17a2b8;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 12px 30px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 500;
    }

    .table td {
        vertical-align: middle;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-active {
        background: #d4edda;
        color: #155724;
    }

    .status-inactive {
        background: #f8d7da;
        color: #721c24;
    }

    .action-buttons .btn {
        margin-right: 5px;
        padding: 4px 8px;
        font-size: 12px;
    }
</style>
@endpush

@section("content")
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">URL Redirects</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Manage URL redirections</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">URL Redirects</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Add New Redirects</h5>
            </div>
            <div class="card-body">
                <form id="redirect-form" action="{{ route('admin.url-redirects.store') }}" method="POST">
                    @csrf
                    <div class="redirect-form">
                        <button type="button" class="add-more-btn" onclick="addRedirectRow()">
                            <i class="fas fa-plus"></i> Add More
                        </button>
                        
                        <div id="redirect-rows">
                            <!-- Initial row -->
                            <div class="redirect-row">
                                <button type="button" class="remove-row" onclick="removeRow(this)" title="Remove">×</button>
                                <div class="row">
                                    <div class="col-md-5">
                                        <label>Link From</label>
                                        <input type="text" name="redirects[0][link_from]" class="form-control" placeholder="Enter source URL" required>
                                        <small class="text-muted">e.g., old-page or /old-page</small>
                                    </div>
                                    <div class="col-md-5">
                                        <label>Link To</label>
                                        <input type="text" name="redirects[0][link_to]" class="form-control" placeholder="Enter destination URL" required>
                                        <small class="text-muted">e.g., new-page or https://external-site.com</small>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="save-all-btn">
                                <i class="fas fa-save"></i> Save All
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Redirects</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{session('success')}}</div>
                @endif

                @if($redirects->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Link From</th>
                                <th>Link To</th>
                                <th>Direct Link</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($redirects as $redirect)
                            <tr>
                                <td>{{ $redirect->id }}</td>
                                <td>{{ $redirect->link_from }}</td>
                                <td>{{ $redirect->link_to }}</td>
                                <td>
                                    @if($redirect->is_direct_link)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge {{ $redirect->is_active ? 'status-active' : 'status-inactive' }}">
                                        {{ $redirect->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $redirect->created_at->format('d M Y') }}</td>
                                <td class="action-buttons">
                                    <button type="button" class="btn btn-info" onclick="editRedirect({{ $redirect->id }}, '{{ addslashes($redirect->link_from) }}', '{{ addslashes($redirect->link_to) }}', {{ $redirect->is_direct_link ? 'true' : 'false' }})">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    
                                    <form action="{{ route('admin.url-redirects.toggle-status', $redirect->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn {{ $redirect->is_active ? 'btn-warning' : 'btn-success' }}">
                                            <i class="fas fa-{{ $redirect->is_active ? 'pause' : 'play' }}"></i>
                                            {{ $redirect->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('admin.url-redirects.destroy', $redirect->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $redirects->links() }}
                @else
                    <div class="text-center py-4">
                        <p class="text-muted">No redirects found. Add some redirects using the form above.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Redirect</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Link From</label>
                        <input type="text" name="link_from" id="edit_link_from" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Link To</label>
                        <input type="text" name="link_to" id="edit_link_to" class="form-control" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_direct_link" id="edit_is_direct_link" class="form-check-input">
                        <label class="form-check-label" for="edit_is_direct_link">
                            Is Direct Link?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    let rowCounter = 1;

    function addRedirectRow() {
        const container = document.getElementById('redirect-rows');
        const newRow = document.createElement('div');
        newRow.className = 'redirect-row';
        newRow.innerHTML = `
            <button type="button" class="remove-row" onclick="removeRow(this)" title="Remove">×</button>
            <div class="row">
                <div class="col-md-5">
                    <label>Link From</label>
                    <input type="text" name="redirects[${rowCounter}][link_from]" class="form-control" placeholder="Enter source URL" required>
                    <small class="text-muted">e.g., old-page or /old-page</small>
                </div>
                <div class="col-md-5">
                    <label>Link To</label>
                    <input type="text" name="redirects[${rowCounter}][link_to]" class="form-control" placeholder="Enter destination URL" required>
                    <small class="text-muted">e.g., new-page or https://external-site.com</small>
                </div>
            
            </div>
        `;
        container.appendChild(newRow);
        rowCounter++;
    }

    function removeRow(button) {
        const rows = document.querySelectorAll('.redirect-row');
        if (rows.length > 1) {
            button.closest('.redirect-row').remove();
            updateRowNumbers();
        } else {
            // Allow removing the last row but add a new empty one
            button.closest('.redirect-row').remove();
            addRedirectRow();
        }
    }

    function updateRowNumbers() {
        const rows = document.querySelectorAll('.redirect-row');
        rows.forEach((row, index) => {
            const inputs = row.querySelectorAll('input');
            inputs.forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
                }
                if (input.id) {
                    input.id = input.id.replace(/_\d+$/, `_${index}`);
                }
            });
            
            const labels = row.querySelectorAll('label[for]');
            labels.forEach(label => {
                if (label.getAttribute('for')) {
                    label.setAttribute('for', label.getAttribute('for').replace(/_\d+$/, `_${index}`));
                }
            });
        });
    }

    function editRedirect(id, linkFrom, linkTo, isDirectLink) {
        console.log('Edit function called', { id, linkFrom, linkTo, isDirectLink });
        
        document.getElementById('edit_link_from').value = linkFrom;
        document.getElementById('edit_link_to').value = linkTo;
        document.getElementById('edit_is_direct_link').checked = isDirectLink;
        document.getElementById('edit-form').action = `/admin/url-redirects/${id}`;
        
        // Use jQuery modal for Bootstrap 4 compatibility
        $('#editModal').modal('show');
    }
</script>
@endpush
