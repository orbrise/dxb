@extends("admin.layout.master")

@push('css')
<link href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css" rel="stylesheet" />
<style>
    .message-card {
        background: #fff;
        border: 1px solid #e3e3e3;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        cursor: move;
        transition: all 0.2s ease;
    }
    
    .message-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .message-card.inactive {
        opacity: 0.6;
        background: #f5f5f5;
    }
    
    .message-card .drag-handle {
        color: #999;
        margin-right: 10px;
        cursor: move;
    }
    
    .message-card .message-text {
        flex: 1;
        font-size: 14px;
        line-height: 1.5;
        word-break: break-word;
    }
    
    .message-card .message-actions {
        display: flex;
        gap: 8px;
        margin-left: 15px;
    }
    
    .message-card .order-badge {
        background: #007bff;
        color: #fff;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
        margin-right: 10px;
        flex-shrink: 0;
    }
    
    .message-card.inactive .order-badge {
        background: #999;
    }
    
    .placeholder-info {
        background: #e7f3ff;
        border: 1px solid #b3d7ff;
        border-radius: 6px;
        padding: 12px 15px;
        margin-bottom: 20px;
    }
    
    .placeholder-info code {
        background: #fff;
        padding: 2px 6px;
        border-radius: 3px;
        color: #d63384;
    }
    
    .sortable-ghost {
        opacity: 0.4;
    }
    
    .btn-toggle-status {
        min-width: 80px;
    }
    
    /* Modal styles for compatibility */
    .modal.show {
        display: block !important;
    }
    
    .modal-backdrop.show {
        opacity: 0.5;
    }
    
    .add-message-form textarea {
        resize: vertical;
        min-height: 80px;
    }
    
    .stats-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 8px;
        padding: 20px;
        color: white;
        margin-bottom: 20px;
    }
    
    .stats-box h6 {
        opacity: 0.8;
        margin-bottom: 5px;
    }
    
    .stats-box h3 {
        margin: 0;
    }
</style>
@endpush

@section("content")
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">WhatsApp Rotation Messages</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Manage rotating messages to avoid spam detection</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.whatsapp.index') }}">WhatsApp</a></li>
            <li class="breadcrumb-item active">Rotation Messages</li>
        </ol>
    </div>
</div>

<div class="widget-list">
    <div class="row">
        <div class="col-md-8">
            <!-- Messages List -->
            <div class="widget-holder">
                <div class="widget-bg">
                    <div class="widget-heading clearfix">
                        <h5>Message Templates</h5>
                        <p class="text-muted mt-1 mb-0">Drag to reorder. Messages are sent in sequence for each profile.</p>
                    </div>
                    <div class="widget-body">
                        <div class="placeholder-info">
                            <strong>Available Placeholder:</strong> Use <code>{url}</code> to insert the profile URL in your message.
                            <br><small class="text-muted">Example: "Hi, I found your profile: {url}" → "Hi, I found your profile: https://example.com/female-escorts/123/name"</small>
                        </div>
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        
                        @if($messages->isEmpty())
                            <div class="text-center py-5">
                                <i class="fa fa-comments-o fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No rotation messages yet. Add your first message below!</p>
                            </div>
                        @else
                            <div id="messages-list">
                                @foreach($messages as $index => $message)
                                    <div class="message-card d-flex align-items-center {{ $message->is_active ? '' : 'inactive' }}" data-id="{{ $message->id }}">
                                        <span class="drag-handle">
                                            <i class="fa fa-grip-vertical"></i>
                                        </span>
                                        <span class="order-badge">{{ $index + 1 }}</span>
                                        <div class="message-text">
                                            {{ $message->message }}
                                        </div>
                                        <div class="message-actions">
                                            <button type="button" 
                                                    class="btn btn-sm btn-toggle-status {{ $message->is_active ? 'btn-success' : 'btn-secondary' }}"
                                                    onclick="toggleStatus({{ $message->id }})"
                                                    id="toggle-btn-{{ $message->id }}">
                                                {{ $message->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                            <button type="button" class="btn btn-sm btn-info" onclick="editMessage({{ $message->id }})">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.whatsapp.rotation.destroy', $message->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this message?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Stats -->
            <div class="stats-box">
                <h6>Total Messages</h6>
                <h3>{{ $messages->count() }}</h3>
            </div>
            <div class="stats-box" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <h6>Active Messages</h6>
                <h3>{{ $messages->where('is_active', true)->count() }}</h3>
            </div>
            
            <!-- Add New Message -->
            <div class="widget-holder">
                <div class="widget-bg">
                    <div class="widget-heading clearfix">
                        <h5 id="form-title">Add New Message</h5>
                    </div>
                    <div class="widget-body">
                        <form id="message-form" action="{{ route('admin.whatsapp.rotation.store') }}" method="POST" class="add-message-form">
                            @csrf
                            <input type="hidden" name="_method" id="form-method" value="POST">
                            <div class="form-group">
                                <label for="message">Message Template</label>
                                <textarea class="form-control" name="message" id="message" rows="4" 
                                          placeholder="Hi! I saw your profile on MassageRepublic: {url}" required></textarea>
                                <small class="form-text text-muted">Use {url} placeholder for the profile link</small>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block" id="submit-btn">
                                <i class="fa fa-plus"></i> Add Message
                            </button>
                            <button type="button" class="btn btn-secondary btn-block d-none" id="cancel-edit-btn" onclick="cancelEdit()">
                                Cancel Edit
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-message">Message Template</label>
                        <textarea class="form-control" name="message" id="edit-message" rows="4" required></textarea>
                        <small class="form-text text-muted">Use {url} placeholder for the profile link</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Message</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    // Initialize sortable for drag and drop reordering
    document.addEventListener('DOMContentLoaded', function() {
        var el = document.getElementById('messages-list');
        if (el) {
            Sortable.create(el, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function(evt) {
                    updateOrder();
                    updateOrderBadges();
                }
            });
        }
    });
    
    function updateOrder() {
        var order = [];
        document.querySelectorAll('#messages-list .message-card').forEach(function(card) {
            order.push(card.dataset.id);
        });
        
        fetch('{{ route("admin.whatsapp.rotation.reorder") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order: order })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Order updated');
            }
        });
    }
    
    function updateOrderBadges() {
        document.querySelectorAll('#messages-list .message-card').forEach(function(card, index) {
            card.querySelector('.order-badge').textContent = index + 1;
        });
    }
    
    function toggleStatus(id) {
        fetch('{{ url("admin/whatsapp/rotation") }}/' + id + '/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                var btn = document.getElementById('toggle-btn-' + id);
                var card = btn.closest('.message-card');
                
                if (data.is_active) {
                    btn.classList.remove('btn-secondary');
                    btn.classList.add('btn-success');
                    btn.textContent = 'Active';
                    card.classList.remove('inactive');
                } else {
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-secondary');
                    btn.textContent = 'Inactive';
                    card.classList.add('inactive');
                }
            }
        });
    }
    
    function editMessage(id) {
        fetch('{{ url("admin/whatsapp/rotation") }}/' + id + '/edit')
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(function(data) {
                document.getElementById('edit-message').value = data.message;
                document.getElementById('edit-form').action = '{{ url("admin/whatsapp/rotation") }}/' + id;
                
                // Try multiple ways to show modal for compatibility
                var editModal = document.getElementById('editModal');
                if (typeof $ !== 'undefined' && $.fn.modal) {
                    $('#editModal').modal('show');
                } else if (typeof bootstrap !== 'undefined') {
                    var modal = new bootstrap.Modal(editModal);
                    modal.show();
                } else {
                    // Fallback: manually show modal
                    editModal.classList.add('show');
                    editModal.style.display = 'block';
                    document.body.classList.add('modal-open');
                    var backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    backdrop.id = 'modal-backdrop';
                    document.body.appendChild(backdrop);
                }
            })
            .catch(function(error) {
                console.error('Error fetching message:', error);
                alert('Error loading message. Please try again.');
            });
    }
    
    // Close modal manually if needed
    document.querySelectorAll('[data-dismiss="modal"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var modal = this.closest('.modal');
            if (modal) {
                modal.classList.remove('show');
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
                var backdrop = document.getElementById('modal-backdrop');
                if (backdrop) backdrop.remove();
            }
        });
    });
</script>
@endpush
