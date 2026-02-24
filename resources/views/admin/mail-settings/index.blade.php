@extends('admin.layout.master')

@section('title', 'Mail Settings')

@section('content')
<style>
.form-check-input
 {
    margin-left: 0px;
}

</style>
 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Mail Settings</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage mail settings </p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Mail Settings</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>

    <div class="row mt-3 mb-3">
        @if($mailSettings)
        <div class="col-md-12 mb-3">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-chart-pie me-1"></i> Current Notification Status</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Enabled:</small>
                            <div class="text-success">
                                {{ collect([
                                    $mailSettings->send_on_account_create ?? true,
                                    $mailSettings->send_on_forget_password ?? true,
                                    $mailSettings->send_on_profile_upgrade ?? true,
                                    $mailSettings->send_on_account_upgrade ?? true,
                                    $mailSettings->send_on_profile_archived ?? true,
                                    $mailSettings->send_on_verification ?? true,
                                    $mailSettings->send_on_package_purchase ?? true,
                                    $mailSettings->send_on_wallet_transaction ?? false
                                ])->filter()->count() }} out of 8 notifications
                            </div>
                        </div>
                        <div class="col-md-9">
                            <small class="text-muted">Mail Configuration:</small>
                            <div>
                                <span class="badge bg-{{ ($mailSettings->mail_driver ?? 'smtp') === 'smtp' ? 'primary' : 'success' }}">
                                    {{ strtoupper($mailSettings->mail_driver ?? 'SMTP') }}
                                </span>
                                @if(($mailSettings->mail_driver ?? 'smtp') === 'smtp')
                                    <span class="badge bg-secondary">{{ $mailSettings->mail_host }}</span>
                                    <span class="badge bg-secondary">Port {{ $mailSettings->mail_port }}</span>
                                    <span class="badge bg-info">{{ strtoupper($mailSettings->mail_encryption) }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $mailSettings->sendmail_path ?? '/usr/sbin/sendmail -bs' }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-envelope-open-text me-2"></i>
                        Mail Settings (SMTP Configuration)
                    </h4>
                    <button type="button" class="btn btn-outline-primary" id="testConnection">
                        <i class="fas fa-paper-plane me-1"></i>
                        Test Connection
                    </button>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.mail-settings.store') }}" id="mailSettingsForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-cog me-1"></i>
                                Mail Driver <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mail_driver" id="driver_smtp" value="smtp" 
                                           {{ old('mail_driver', $mailSettings->mail_driver ?? 'smtp') == 'smtp' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="driver_smtp">
                                        <i class="fas fa-server me-1"></i>
                                        <strong>SMTP</strong>
                                        <br>
                                        <small class="text-muted">Use external SMTP server (Gmail, SendGrid, etc.)</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mail_driver" id="driver_sendmail" value="sendmail"
                                           {{ old('mail_driver', $mailSettings->mail_driver ?? 'smtp') == 'sendmail' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="driver_sendmail">
                                        <i class="fas fa-envelope me-1"></i>
                                        <strong>Sendmail</strong>
                                        <br>
                                        <small class="text-muted">Use server's built-in sendmail (Linux/Unix)</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Sendmail Settings (Hidden by default) -->
                        <div id="sendmail_settings" style="display: none;">
                            <h5 class="mb-3">
                                <i class="fas fa-envelope me-2"></i>
                                Sendmail Configuration
                            </h5>
                            <div class="mb-3">
                                <label for="sendmail_path" class="form-label">
                                    <i class="fas fa-folder-open me-1"></i>
                                    Sendmail Path
                                </label>
                                <input type="text" class="form-control" id="sendmail_path" name="sendmail_path" 
                                       value="{{ old('sendmail_path', $mailSettings->sendmail_path ?? '/usr/sbin/sendmail -bs') }}" 
                                       placeholder="/usr/sbin/sendmail -bs">
                                <small class="form-text text-muted">Path to sendmail binary (default: /usr/sbin/sendmail -bs)</small>
                            </div>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> Sendmail must be installed and configured on your server. Common paths: 
                                /usr/sbin/sendmail, /usr/bin/sendmail
                            </div>
                            <hr class="my-4">
                        </div>

                        <!-- SMTP Settings -->
                        <div id="smtp_settings">
                            <h5 class="mb-3">
                                <i class="fas fa-server me-2"></i>
                                SMTP Configuration
                            </h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_host" class="form-label">
                                        <i class="fas fa-server me-1"></i>
                                        Mail Host <span class="text-danger smtp-required">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="mail_host" name="mail_host" 
                                           value="{{ old('mail_host', $mailSettings->mail_host ?? 'mail.oobben.email') }}" 
                                           placeholder="e.g., mail.yourserver.com">
                                    <small class="form-text text-muted">SMTP Host (e.g., mail.oobben.email)</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_port" class="form-label">
                                        <i class="fas fa-plug me-1"></i>
                                        Mail Port <span class="text-danger smtp-required">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="mail_port" name="mail_port" 
                                           value="{{ old('mail_port', $mailSettings->mail_port ?? 587) }}" 
                                           placeholder="587" min="1" max="65535">
                                    <small class="form-text text-muted">SMTP Port (e.g., 25, 587, 465)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_username" class="form-label">
                                        <i class="fas fa-user me-1"></i>
                                        Mail Username <span class="text-danger smtp-required">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="mail_username" name="mail_username" 
                                           value="{{ old('mail_username', $mailSettings->mail_username ?? 'oobbenmail') }}" 
                                           placeholder="SMTP Username">
                                    <small class="form-text text-muted">SMTP Username</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_password" class="form-label">
                                        <i class="fas fa-key me-1"></i>
                                        Mail Password <span class="text-danger smtp-required">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="mail_password" name="mail_password" 
                                               value="{{ old('mail_password', isset($mailSettings->mail_password) ? $mailSettings->mail_password : '6021577@BaL') }}" 
                                               placeholder="SMTP Password">
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted">SMTP Password</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_encryption" class="form-label">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Mail Encryption <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="mail_encryption" name="mail_encryption">
                                        <option value="tls" {{ old('mail_encryption', $mailSettings->mail_encryption ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ old('mail_encryption', $mailSettings->mail_encryption ?? 'tls') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="none" {{ old('mail_encryption', $mailSettings->mail_encryption ?? 'tls') == 'none' ? 'selected' : '' }}>None</option>
                                    </select>
                                    <small class="form-text text-muted">SMTP Encryption (e.g., tls, ssl, starttls)</small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        </div>
                        <!-- End SMTP Settings -->

                        <!-- Common Settings (Email Sender & Name) -->
                        <h5 class="mb-3">
                            <i class="fas fa-user-circle me-2"></i>
                            Email Sender Information
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mail_from_address" class="form-label">
                                        <i class="fas fa-at me-1"></i>
                                        Email Sender <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control" id="mail_from_address" name="mail_from_address" 
                                           value="{{ old('mail_from_address', $mailSettings->mail_from_address ?? 'ae@oobben.email') }}" 
                                           placeholder="noreply@yoursite.com" required>
                                    <small class="form-text text-muted">Transactional Email Sender. Example: noreply@yoursite.com</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="mail_from_name" class="form-label">
                                        <i class="fas fa-signature me-1"></i>
                                        Sender Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" 
                                           value="{{ old('mail_from_name', $mailSettings->mail_from_name ?? 'DXB Application') }}" 
                                           placeholder="Your Application Name" required>
                                    <small class="form-text text-muted">The name that will appear as the sender in emails</small>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5 class="mb-3">
                            <i class="fas fa-bell me-2"></i>
                            Email Notification Settings
                        </h5>
                        <p class="text-muted mb-3">Choose when to send automatic emails to users</p>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="send_on_account_create" name="send_on_account_create" 
                                           {{ old('send_on_account_create', $mailSettings->send_on_account_create ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="send_on_account_create">
                                        <i class="fas fa-user-plus me-1 text-success"></i>
                                        <strong>Account Creation</strong><br>
                                        <small class="text-muted">Send welcome email when new account is created</small>
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="send_on_forget_password" name="send_on_forget_password" 
                                           {{ old('send_on_forget_password', $mailSettings->send_on_forget_password ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="send_on_forget_password">
                                        <i class="fas fa-key me-1 text-warning"></i>
                                        <strong>Password Reset</strong><br>
                                        <small class="text-muted">Send password reset links</small>
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="send_on_profile_upgrade" name="send_on_profile_upgrade" 
                                           {{ old('send_on_profile_upgrade', $mailSettings->send_on_profile_upgrade ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="send_on_profile_upgrade">
                                        <i class="fas fa-arrow-up me-1 text-primary"></i>
                                        <strong>Profile Upgrade</strong><br>
                                        <small class="text-muted">Send confirmation when profile is upgraded</small>
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="send_on_account_upgrade" name="send_on_account_upgrade" 
                                           {{ old('send_on_account_upgrade', $mailSettings->send_on_account_upgrade ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="send_on_account_upgrade">
                                        <i class="fas fa-crown me-1 text-info"></i>
                                        <strong>Account Upgrade</strong><br>
                                        <small class="text-muted">Send notification when account level is upgraded</small>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="send_on_profile_archived" name="send_on_profile_archived" 
                                           {{ old('send_on_profile_archived', $mailSettings->send_on_profile_archived ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="send_on_profile_archived">
                                        <i class="fas fa-archive me-1 text-secondary"></i>
                                        <strong>Profile Archived</strong><br>
                                        <small class="text-muted">Send notification when profile is archived</small>
                                    </label>
                                </div>

                            

                                

                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="send_on_wallet_transaction" name="send_on_wallet_transaction" 
                                           {{ old('send_on_wallet_transaction', $mailSettings->send_on_wallet_transaction ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="send_on_wallet_transaction">
                                        <i class="fas fa-wallet me-1 text-info"></i>
                                        <strong>Wallet Transactions</strong><br>
                                        <small class="text-muted">Send notification for wallet deposits/withdrawals</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> When email notifications are disabled for specific events, the system will still log the events but won't send emails to users. This is useful for testing or maintenance periods.
                        </div>

                        @if($mailSettings)
                        <div class="alert alert-success mt-3">
                            <h6><i class="fas fa-flask me-2"></i>Quick Test - Account Creation Email</h6>
                            <p class="mb-2">Current Status: 
                                @if($mailSettings->send_on_account_create)
                                    <span class="badge bg-success">ENABLED</span>
                                @else
                                    <span class="badge bg-danger">DISABLED</span>
                                @endif
                            </p>
                            <div class="d-flex gap-2">
                                <a href="{{ url('admin/test-account-creation-toggle/test@example.com') }}" 
                                   class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="fas fa-vial me-1"></i>
                                    Test Account Creation Email
                                </a>
                                <small class="text-muted align-self-center">
                                    This will test if account creation emails are sent based on your current toggle setting.
                                </small>
                            </div>
                        </div>
                        @endif

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


<!-- Test Connection Modal -->
<div class="modal fade" id="testModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-paper-plane me-2"></i>
                    Test SMTP Connection
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="testResult"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle mail driver toggle
    const smtpSettings = document.getElementById('smtp_settings');
    const sendmailSettings = document.getElementById('sendmail_settings');
    const driverRadios = document.querySelectorAll('input[name="mail_driver"]');
    
    function toggleMailSettings() {
        const selectedDriver = document.querySelector('input[name="mail_driver"]:checked').value;
        
        if (selectedDriver === 'smtp') {
            smtpSettings.style.display = 'block';
            sendmailSettings.style.display = 'none';
            
            // Make SMTP fields required
            document.getElementById('mail_host').required = true;
            document.getElementById('mail_port').required = true;
            document.getElementById('mail_username').required = true;
            document.getElementById('mail_password').required = true;
            document.getElementById('mail_encryption').required = true;
            
            // Show SMTP required asterisks
            document.querySelectorAll('.smtp-required').forEach(el => el.style.display = 'inline');
            
            // Remove required from sendmail
            const sendmailPath = document.getElementById('sendmail_path');
            if (sendmailPath) {
                sendmailPath.required = false;
            }
        } else {
            smtpSettings.style.display = 'none';
            sendmailSettings.style.display = 'block';
            
            // Remove required from SMTP fields
            document.getElementById('mail_host').required = false;
            document.getElementById('mail_port').required = false;
            document.getElementById('mail_username').required = false;
            document.getElementById('mail_password').required = false;
            document.getElementById('mail_encryption').required = false;
            
            // Hide SMTP required asterisks
            document.querySelectorAll('.smtp-required').forEach(el => el.style.display = 'none');
        }
    }
    
    // Initialize on page load
    toggleMailSettings();
    
    // Listen for changes
    driverRadios.forEach(radio => {
        radio.addEventListener('change', toggleMailSettings);
    });

    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('mail_password');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }

    // Test connection
    const testConnectionBtn = document.getElementById('testConnection');
    const testModal = new bootstrap.Modal(document.getElementById('testModal'));
    
    testConnectionBtn.addEventListener('click', function() {
        const formData = new FormData(document.getElementById('mailSettingsForm'));
        
        // Show loading
        document.getElementById('testResult').innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Testing connection...</p>
            </div>
        `;
        
        testModal.show();
        
        fetch('{{ route("admin.mail-settings.test") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const alertClass = data.success ? 'alert-success' : 'alert-danger';
            const icon = data.success ? 'fa-check-circle' : 'fa-exclamation-triangle';
            
            let diagnosticsHtml = '';
            if (data.diagnostics && data.diagnostics.length > 0) {
                diagnosticsHtml = `
                    <div class="mt-3">
                        <h6><i class="fas fa-info-circle me-1"></i>Diagnostic Information:</h6>
                        <ul class="small mb-0">
                            ${data.diagnostics.map(diag => `<li>${diag}</li>`).join('')}
                        </ul>
                    </div>
                `;
            }
            
            let suggestionsHtml = '';
            if (data.suggestions && data.suggestions.length > 0) {
                suggestionsHtml = `
                    <div class="mt-3">
                        <h6><i class="fas fa-lightbulb me-1"></i>Suggestions:</h6>
                        <ul class="small mb-0">
                            ${data.suggestions.map(suggestion => `<li>${suggestion}</li>`).join('')}
                        </ul>
                    </div>
                `;
            }
            
            document.getElementById('testResult').innerHTML = `
                <div class="alert ${alertClass}" role="alert">
                    <i class="fas ${icon} me-2"></i>
                    ${data.message}
                    ${diagnosticsHtml}
                    ${suggestionsHtml}
                </div>
            `;
        })
        .catch(error => {
            document.getElementById('testResult').innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    An error occurred while testing the connection.
                </div>
            `;
        });
    });
});
</script>
@endsection