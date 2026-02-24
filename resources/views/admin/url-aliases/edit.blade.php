@extends('admin.layout.master')

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">URL Aliases</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Edit URL alias</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.url-aliases.index') }}">URL Aliases</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </div>
</div>

<div class="row mt-3 mb-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0 float-left">Edit URL Alias</h5>
                <a href="{{ route('admin.url-aliases.index') }}" class="btn btn-secondary float-right">Back to List</a>
            </div>

            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa fa-exclamation-circle mr-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <!-- Current URL Preview -->
                <div class="alert alert-info">
                    <h6><i class="fa fa-info-circle mr-2"></i>Current Configuration:</h6>
                    <div class="row">
                        <div class="col-md-12">
                            <strong>Custom URL:</strong> <code>{{ url('/') }}/{{ $urlAlias->custom_url }}</code><br>
                            <strong>Points to:</strong> <code>{{ url('/') }}/{{ $urlAlias->base_pattern }}</code><br>
                            <strong>Type:</strong> <span class="badge badge-{{ $urlAlias->redirect_type == 'canonical' ? 'info' : 'primary' }}">{{ ucfirst($urlAlias->redirect_type) }}</span>
                            <strong>Status:</strong> <span class="badge badge-{{ $urlAlias->is_active ? 'success' : 'secondary' }}">{{ $urlAlias->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="alert alert-secondary">
                    <h6><i class="fa fa-lightbulb mr-2"></i>Examples:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Custom URL:</strong> <code>all_female-escorts-in-dubai</code><br>
                            <strong>Base Pattern:</strong> <code>female-escorts-in-dubai</code><br>
                            <small class="text-muted">Creates: /all_female-escorts-in-dubai → /female-escorts-in-dubai</small>
                        </div>
                        <div class="col-md-6">
                            <strong>Custom URL:</strong> <code>premium_male-escorts-in-abu-dhabi</code><br>
                            <strong>Base Pattern:</strong> <code>male-escorts-in-abu-dhabi</code><br>
                            <small class="text-muted">Creates: /premium_male-escorts-in-abu-dhabi → /male-escorts-in-abu-dhabi</small>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.url-aliases.update', $urlAlias->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="custom_url" class="form-label">Custom URL <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ url('/') }}/</span>
                                    </div>
                                    <input type="text" name="custom_url" id="custom_url" class="form-control" 
                                           value="{{ old('custom_url', $urlAlias->custom_url) }}" required 
                                           placeholder="all_female-escorts-in-dubai"
                                           pattern="[a-zA-Z0-9_-]+" 
                                           title="Only letters, numbers, underscores and hyphens allowed">
                                </div>
                                <small class="text-muted">Only letters, numbers, underscores (_) and hyphens (-) allowed</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="base_pattern" class="form-label">Base Pattern <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ url('/') }}/</span>
                                    </div>
                                    <input type="text" name="base_pattern" id="base_pattern" class="form-control" 
                                           value="{{ old('base_pattern', $urlAlias->base_pattern) }}" required 
                                           placeholder="female-escorts-in-dubai">
                                </div>
                                <small class="text-muted">The original URL pattern this alias should point to</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="redirect_type" class="form-label">Redirect Type <span class="text-danger">*</span></label>
                                <select name="redirect_type" id="redirect_type" class="form-control" required>
                                    <option value="301" {{ old('redirect_type', $urlAlias->redirect_type) == '301' ? 'selected' : '' }}>301 Permanent Redirect (SEO Recommended)</option>
                                    <option value="302" {{ old('redirect_type', $urlAlias->redirect_type) == '302' ? 'selected' : '' }}>302 Temporary Redirect</option>
                                    <option value="canonical" {{ old('redirect_type', $urlAlias->redirect_type) == 'canonical' ? 'selected' : '' }}>Canonical (Same Content, No Redirect)</option>
                                </select>
                                <small class="text-muted">
                                    <strong>301:</strong> User is redirected, SEO juice passes<br>
                                    <strong>302:</strong> User is redirected, temporary<br>
                                    <strong>Canonical:</strong> Same content, no redirect, adds canonical tag
                                </small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3" 
                                          placeholder="Optional description for admin reference">{{ old('description', $urlAlias->description) }}</textarea>
                                <small class="text-muted">Internal note about this alias (not public)</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check mb-3">
                                <input style="margin-left:1px" type="checkbox" name="is_active" id="is_active" class="form-check-input" 
                                       value="1" {{ old('is_active', $urlAlias->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="form-check-label">
                                    Active (alias will work immediately)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Live Preview -->
                    <div class="mb-3">
                        <label class="form-label">Preview:</label>
                        <div id="preview" class="p-3 bg-light border rounded">
                            <!-- Preview will be updated by JavaScript -->
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Update URL Alias</button>
                        <a href="{{ route('admin.url-aliases.index') }}" class="btn btn-secondary">Cancel</a>
                        
                        <!-- Test Link -->
                        @if($urlAlias->is_active)
                            <a href="{{ url('/' . $urlAlias->custom_url) }}" target="_blank" class="btn btn-info">
                                <i class="fa fa-external-link"></i> Test URL
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
$(document).ready(function() {
    // Auto-generate preview
    function updatePreview() {
        const customUrl = $('#custom_url').val();
        const basePattern = $('#base_pattern').val();
        const redirectType = $('#redirect_type').val();
        
        let previewText = '';
        if (customUrl && basePattern) {
            const baseUrl = '{{ url("/") }}';
            if (redirectType === 'canonical') {
                previewText = `${baseUrl}/${customUrl} will serve the same content as ${baseUrl}/${basePattern}`;
            } else {
                previewText = `${baseUrl}/${customUrl} will redirect (${redirectType}) to ${baseUrl}/${basePattern}`;
            }
        }
        
        $('#preview').text(previewText);
    }
    
    $('#custom_url, #base_pattern, #redirect_type').on('input change', updatePreview);
    updatePreview();
    
    // Validate URL format
    $('#custom_url').on('input', function() {
        const value = $(this).val();
        const isValid = /^[a-zA-Z0-9_-]*$/.test(value);
        
        if (!isValid && value.length > 0) {
            $(this).addClass('is-invalid');
            if (!$('#custom_url_error').length) {
                $(this).after('<div id="custom_url_error" class="invalid-feedback">Only letters, numbers, underscores and hyphens allowed</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $('#custom_url_error').remove();
        }
    });
    
    // Warn about URL changes
    const originalCustomUrl = '{{ $urlAlias->custom_url }}';
    $('#custom_url').on('change', function() {
        const newValue = $(this).val();
        if (newValue !== originalCustomUrl && newValue.length > 0) {
            if (!$('#url_change_warning').length) {
                $(this).after('<div id="url_change_warning" class="text-warning mt-1"><i class="fa fa-warning"></i> Changing the custom URL will break existing links to: {{ url("/") }}/' + originalCustomUrl + '</div>');
            }
        } else {
            $('#url_change_warning').remove();
        }
    });
});
</script>
@endpush
