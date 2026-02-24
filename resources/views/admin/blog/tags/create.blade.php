@extends('admin.layout.master')

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Create Tag</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Add a new blog tag</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blog.tags.index') }}">Tags</a></li>
            <li class="breadcrumb-item active">Create</li>
        </ol>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.blog.tags.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input type="text" name="slug" id="slug" 
                               class="form-control @error('slug') is-invalid @enderror" 
                               value="{{ old('slug') }}" placeholder="auto-generated-from-name">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Leave empty to auto-generate from name</small>
                    </div>
                    
                    <hr>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Tag
                    </button>
                    <a href="{{ route('admin.blog.tags.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    // Auto-generate slug from name
    $('#name').on('blur', function() {
        if ($('#slug').val() === '') {
            var slug = $(this).val()
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            $('#slug').val(slug);
        }
    });
});
</script>
@endpush
