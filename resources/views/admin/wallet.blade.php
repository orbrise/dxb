@extends('admin.layout.master')

@push('css')
<style>
    /* Main container styling */
    .wallet-container {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    /* Tabs styling */
    .wallet-tabs {
        margin-bottom: 25px;
    }

    .tabs-header {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .tab-btn {
        background: #fff;
        color: #6c757d;
        border: 2px solid #e9ecef;
        padding: 10px 25px;
        border-radius: 4px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 140px;
    }

    .tab-btn:hover {
        border-color: #667eea;
        color: #667eea;
        transform: translateY(-1px);
    }

    .tab-btn.active {
        background:  #7460ee;
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    /* Form styling */
    .wallet-form {
        background: white;
        padding: 25px;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        max-width: 500px;
        margin: 0 auto;
    }

    .form-title {
        color: #495057;
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 20px;
        text-align: center;
        border-bottom: 2px solid #f8f9fa;
        padding-bottom: 15px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        color: #495057;
        font-weight: 600;
        font-size: 0.85rem;
        margin-bottom: 8px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #fff;
    }

    .form-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .form-input::placeholder {
        color: #adb5bd;
        font-size: 0.9rem;
    }

    /* Button styling */
    .btn-primary {
        background: #7460ee;
        border: none;
        padding: 12px 30px;
        border-radius: 25px;
        color: white;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:disabled {
        background: #6c757d;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .btn-transfer {
        background:  #7460ee;
    }

    .btn-transfer:hover {
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
    }

    /* Transfer specific styling */
    .transfer-form {
        max-width: 800px;
    }

    .transfer-row {
        display: flex;
        align-items: end;
        gap: 15px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .transfer-field {
        flex: 1;
        min-width: 200px;
    }

    .transfer-arrow {
        text-align: center;
        margin: 0 10px;
        align-self: center;
        margin-bottom: 10px;
    }

    .transfer-arrow i {
        color: #28a745;
        font-size: 1.5rem;
        background: #f8f9fa;
        padding: 8px;
        border-radius: 50%;
        border: 2px solid #e9ecef;
    }

    .amount-note-row {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .amount-field {
        flex: 1;
        min-width: 150px;
    }

    .note-field {
        flex: 2;
        min-width: 200px;
    }

    @media (max-width: 768px) {
        .transfer-row {
            flex-direction: column;
            gap: 15px;
        }
        
        .transfer-arrow {
            transform: rotate(90deg);
            margin: 10px 0;
        }
        
        .amount-note-row {
            flex-direction: column;
        }
    }

    /* Loading spinner */
    .loading-spinner {
        display: none;
        text-align: center;
        margin: 15px 0;
    }

    .spinner {
        width: 30px;
        height: 30px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Alert messages */
    .alert {
        padding: 12px 15px;
        border-radius: 8px;
        margin: 15px 0;
        font-size: 0.9rem;
        text-align: center;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    /* Modal styling */
    .user-modal .modal-content {
        border-radius: 15px;
        border: none;
        overflow: hidden;
    }

    .user-modal .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 20px 25px;
    }

    .user-modal .modal-title {
        font-weight: 600;
        font-size: 1.1rem;
    }

    .user-info-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        text-align: center;
    }

    .user-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        font-weight: bold;
        margin: 0 auto 12px;
    }

    .user-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }

    .user-email {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 12px;
    }

    .current-balance {
        background: white;
        border-radius: 8px;
        padding: 12px;
        border-left: 3px solid #28a745;
    }

    .balance-label {
        font-size: 0.8rem;
        color: #666;
        margin-bottom: 3px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .balance-amount {
        font-size: 1.4rem;
        font-weight: bold;
        color: #28a745;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .wallet-container {
            padding: 15px;
        }
        
        .wallet-form {
            padding: 20px;
        }
        
        .tabs-header {
            flex-direction: column;
            align-items: center;
        }
        
        .tab-btn {
            width: 200px;
        }
    }

    .modal-content .close {
    top: 6px;
    right: 0.14286em;
    }
</style>
@endpush

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Wallet Management</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Manage user wallet balances</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Wallet</li>
        </ol>
    </div>
</div>

<div class="wallet-container">
    <!-- Wallet Actions Tabs -->
    <div class="wallet-tabs">
        <div class="tabs-header">
            <button class="tab-btn active" onclick="showTab('add-credits')">
                <i class="fas fa-plus"></i> Add Credits
            </button>
            <button class="tab-btn" onclick="showTab('transfer-credits')">
                <i class="fas fa-exchange-alt"></i> Transfer Credits
            </button>
        </div>
    </div>

    <!-- Add Credits Section -->
    <div id="add-credits-tab" class="tab-content active">
        <div class="wallet-form">
            <form id="emailSearchForm">
                <div class="form-group">
                    <label class="form-label">User Email Address</label>
                    <input type="email" 
                           id="userEmail" 
                           class="form-input" 
                           placeholder="Enter user's email address..." 
                           required>
                </div>
                
                <button type="submit" class="btn-primary">
                    <i class="fas fa-search"></i> Find User
                </button>
            </form>

            <div class="loading-spinner" id="loadingSpinner">
                <div class="spinner"></div>
                <p style="margin-top: 10px; color: #666; font-size: 0.9rem;">Searching...</p>
            </div>

            <div id="errorMessage" class="alert alert-error" style="display: none;"></div>
        </div>
    </div>

    <!-- Transfer Credits Section -->
    <div id="transfer-credits-tab" class="tab-content">
        <div class="wallet-form transfer-form">
            <h4 class="form-title"><i class="fas fa-exchange-alt"></i> Transfer Between Users</h4>
            
            <form id="transferForm">
                <div class="transfer-row">
                    <div class="form-group transfer-field">
                        <label class="form-label">From (Sender Email)</label>
                        <input type="email" 
                               id="fromEmail" 
                               class="form-input" 
                               placeholder="Enter sender's email..." 
                               required>
                    </div>
                    
                    <div class="transfer-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    
                    <div class="form-group transfer-field">
                        <label class="form-label">To (Receiver Email)</label>
                        <input type="email" 
                               id="toEmail" 
                               class="form-input" 
                               placeholder="Enter receiver's email..." 
                               required>
                    </div>
                </div>
                
                <div class="amount-note-row">
                    <div class="form-group amount-field">
                        <label class="form-label">Amount ($)</label>
                        <input type="number" 
                               id="transferAmount" 
                               class="form-input" 
                               placeholder="Enter amount..." 
                               min="1" 
                               step="0.01" 
                               required>
                    </div>
                    
                    <div class="form-group note-field">
                        <label class="form-label">Note (Optional)</label>
                        <input type="text" 
                               id="transferNote" 
                               class="form-input" 
                               placeholder="Enter transfer note...">
                    </div>
                </div>
                
                <button type="submit" class="btn-primary btn-transfer" id="transferBtn">
                    <i class="fas fa-exchange-alt"></i> Transfer Credits
                </button>
            </form>

            <div class="loading-spinner" id="transferLoadingSpinner">
                <div class="spinner"></div>
                <p style="margin-top: 10px; color: #666; font-size: 0.9rem;">Processing...</p>
            </div>

            <div id="transferErrorMessage" class="alert alert-error" style="display: none;"></div>
            <div id="transferSuccessMessage" class="alert alert-success" style="display: none;"></div>
        </div>
    </div>
</div>

<!-- User Modal -->
<div class="modal fade user-modal" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-wallet"></i> Add Balance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="user-info-card">
                    <div class="user-avatar" id="userAvatar"></div>
                    <div class="user-name" id="userName"></div>
                    <div class="user-email" id="userEmailDisplay"></div>
                    
                    <div class="current-balance">
                        <div class="balance-label">Current Balance</div>
                        <div class="balance-amount">$ <span id="currentBalance">0.00</span></div>
                    </div>
                </div>

                <form id="addBalanceForm">
                    <input type="hidden" id="selectedUserId" name="user_id">
                    
                    <div class="form-group">
                        <label class="form-label">Amount to Add ($)</label>
                        <input type="number" 
                               id="amountInput" 
                               name="amount" 
                               class="form-input" 
                               placeholder="Enter amount..." 
                               min="1" 
                               step="0.01" 
                               required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Note (Optional)</label>
                        <input type="text" 
                               id="noteInput" 
                               name="note" 
                               class="form-input" 
                               placeholder="Add a note for this transaction...">
                    </div>

                    <button type="submit" class="btn-primary" id="addBalanceBtn">
                        <i class="fas fa-plus"></i> Add Balance
                    </button>
                </form>

                <div id="modalErrorMessage" class="alert alert-error" style="display: none;"></div>
                <div id="modalSuccessMessage" class="alert alert-success" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions Section -->
<div class="transactions-section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recent Transactions</h5>
                        <form method="GET" action="{{ route('admin.wallet') }}" class="form-inline">
                            <label class="mr-2">Per page</label>
                            @php($pp = request('perPage', $transactions->perPage()))
                            <select name="perPage" class="form-control" onchange="this.form.submit()">
                                <option value="10" {{ $pp==10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $pp==25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $pp==50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $pp==100 ? 'selected' : '' }}>100</option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Note</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{$transaction->wallet->user->name ?? 'N/A'}}</td>
                                    <td>$ {{number_format($transaction->amount, 2)}}</td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->type === 'credit' ? 'success' : 'danger' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td>{{$transaction->description}}</td>
                                    <td>{{$transaction->created_at->format('d M Y H:i')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} entries
                        </div>
                        <div>
                            {{ $transactions->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
// Global number formatting function
function number_format(number, decimals = 2) {
    return parseFloat(number).toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

$(document).ready(function() {
    let selectedUser = null;

    // Email search form submission
    $('#emailSearchForm').submit(function(e) {
        e.preventDefault();
        
        const email = $('#userEmail').val().trim();
        if (!email) return;

        searchUser(email);
    });

    // Search user by email
    function searchUser(email) {
        // Show loading spinner
        $('#loadingSpinner').show();
        $('#errorMessage').hide();
        
        $.ajax({
            url: "{{ route('admin.wallet.validate-user') }}",
            method: 'POST',
            data: {
                email: email,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                $('#loadingSpinner').hide();
                
                if (response.success) {
                    selectedUser = response.user;
                    showUserModal(response.user);
                } else {
                    showError('User not found');
                }
            },
            error: function(xhr) {
                $('#loadingSpinner').hide();
                
                let errorMessage = 'User not found with this email address';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                showError(errorMessage);
            }
        });
    }

    // Show user modal with user details
    function showUserModal(user) {
        // Populate user info
        $('#selectedUserId').val(user.id);
        $('#userName').text(user.name);
        $('#userEmailDisplay').text(user.email);
        $('#currentBalance').text(user.current_balance);
        
        // Set user avatar (first letter of name if no avatar)
        if (user.avatar) {
            $('#userAvatar').html(`<img src="${user.avatar}" alt="${user.name}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`);
        } else {
            $('#userAvatar').text(user.name.charAt(0).toUpperCase());
        }

        // Clear previous form data
        $('#amountInput').val('');
        $('#noteInput').val('');
        $('#modalErrorMessage').hide();
        $('#modalSuccessMessage').hide();

        // Show modal using Bootstrap 4/jQuery
        $('#userModal').modal('show');
    }

    // Add balance form submission
    $('#addBalanceForm').submit(function(e) {
        e.preventDefault();
        
        const formData = {
            user_id: $('#selectedUserId').val(),
            amount: $('#amountInput').val(),
            note: $('#noteInput').val() || 'Admin wallet topup',
            _token: "{{ csrf_token() }}"
        };

        // Disable submit button
        $('#addBalanceBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        $('#modalErrorMessage').hide();
        $('#modalSuccessMessage').hide();

        $.ajax({
            url: "{{ route('admin.wallet.topup') }}",
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#modalSuccessMessage').text('Balance added successfully!').show();
                    
                    // Update current balance display
                    const newBalance = parseFloat($('#currentBalance').text().replace(',', '')) + parseFloat(formData.amount);
                    $('#currentBalance').text(number_format(newBalance, 2));
                    
                    // Clear form
                    $('#amountInput').val('');
                    $('#noteInput').val('');
                    
                    // Auto close modal after 2 seconds
                    setTimeout(function() {
                        $('#userModal').modal('hide');
                        setTimeout(() => location.reload(), 500); // Reload after modal closes
                    }, 2000);
                } else {
                    $('#modalErrorMessage').text('Failed to add balance. Please try again.').show();
                }
            },
            error: function() {
                $('#modalErrorMessage').text('An error occurred. Please try again.').show();
            },
            complete: function() {
                // Re-enable submit button
                $('#addBalanceBtn').prop('disabled', false).html('<i class="fas fa-plus"></i> Add Balance');
            }
        });
    });

    // Show error message
    function showError(message) {
        $('#errorMessage').text(message).show();
        setTimeout(function() {
            $('#errorMessage').fadeOut();
        }, 5000);
    }

    // Reset form when modal is closed
    $('#userModal').on('hidden.bs.modal', function() {
        $('#userEmail').val('');
        $('#modalErrorMessage').hide();
        $('#modalSuccessMessage').hide();
    });
});

// Tab switching functionality
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab content
    document.getElementById(tabName + '-tab').classList.add('active');
    
    // Add active class to clicked button
    event.target.classList.add('active');
}

// Transfer credits functionality
$(document).ready(function() {
    $('#transferForm').submit(function(e) {
        e.preventDefault();
        
        const fromEmail = $('#fromEmail').val().trim();
        const toEmail = $('#toEmail').val().trim();
        const amount = $('#transferAmount').val();
        const note = $('#transferNote').val().trim();
        
        // Validation
        if (!fromEmail || !toEmail || !amount) {
            $('#transferErrorMessage').text('Please fill in all required fields.').show();
            return;
        }
        
        if (fromEmail === toEmail) {
            $('#transferErrorMessage').text('Sender and receiver email cannot be the same.').show();
            return;
        }
        
        if (parseFloat(amount) <= 0) {
            $('#transferErrorMessage').text('Amount must be greater than 0.').show();
            return;
        }
        
        processTransfer(fromEmail, toEmail, amount, note);
    });
});

function processTransfer(fromEmail, toEmail, amount, note) {
    // Show loading spinner
    $('#transferLoadingSpinner').show();
    $('#transferErrorMessage').hide();
    $('#transferSuccessMessage').hide();
    $('#transferBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
    
    const formData = {
        from_email: fromEmail,
        to_email: toEmail,
        amount: amount,
        note: note || 'Admin credit transfer',
        _token: "{{ csrf_token() }}"
    };
    
    $.ajax({
        url: "{{ route('admin.wallet.transfer') }}",
        method: 'POST',
        data: formData,
        success: function(response) {
            $('#transferLoadingSpinner').hide();
            $('#transferBtn').prop('disabled', false).html('<i class="fas fa-exchange-alt"></i> Transfer Credits');
            
            if (response.success) {
                $('#transferSuccessMessage').html(`
                    <strong>Transfer Successful!</strong><br>
                    Transferred $ ${number_format(amount, 2)} from ${fromEmail} to ${toEmail}
                `).show();
                
                // Clear form
                $('#transferForm')[0].reset();
                
                // Auto hide success message after 5 seconds
                setTimeout(function() {
                    $('#transferSuccessMessage').hide();
                }, 5000);
            } else {
                $('#transferErrorMessage').text(response.message || 'Transfer failed. Please try again.').show();
            }
        },
        error: function(xhr) {
            $('#transferLoadingSpinner').hide();
            $('#transferBtn').prop('disabled', false).html('<i class="fas fa-exchange-alt"></i> Transfer Credits');
            
            let errorMessage = 'Transfer failed. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                errorMessage = errors.join(', ');
            }
            
            $('#transferErrorMessage').text(errorMessage).show();
        }
    });
}
</script>
@endpush

@endsection