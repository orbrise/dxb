@extends('admin.layout.master')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .featured-image-preview {
        max-width: 300px;
        max-height: 200px;
        object-fit: cover;
        border-radius: 8px;
        margin-top: 10px;
    }
    .note-editor {
        border-radius: 4px;
    }
    .select2-container {
        width: 100% !important;
    }
</style>
@endpush

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Create Blog Post</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Add a new blog post</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blog.posts.index') }}">Blog Posts</a></li>
            <li class="breadcrumb-item active">Create</li>
        </ol>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-12">
        <form action="{{ route('admin.blog.posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <!-- Main Content Column -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Post Content</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" 
                                       class="form-control @error('title') is-invalid @enderror" 
                                       value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="slug">Slug (URL)</label>
                                <input type="text" name="slug" id="slug" 
                                       class="form-control @error('slug') is-invalid @enderror" 
                                       value="{{ old('slug') }}" placeholder="auto-generated-from-title">
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Leave empty to auto-generate from title</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="excerpt">Excerpt</label>
                                <textarea name="excerpt" id="excerpt" rows="3" 
                                          class="form-control @error('excerpt') is-invalid @enderror" 
                                          placeholder="Brief summary of the post...">{{ old('excerpt') }}</textarea>
                                @error('excerpt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Short description shown in listing. Max 500 characters.</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="content">Content <span class="text-danger">*</span></label>
                                <textarea name="content" id="content" 
                                          class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- SEO Section -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">SEO Settings</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="meta_title">Meta Title</label>
                                <input type="text" name="meta_title" id="meta_title" 
                                       class="form-control" value="{{ old('meta_title') }}"
                                       placeholder="SEO title (defaults to post title)">
                                <small class="text-muted">Recommended: 50-60 characters</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="meta_description">Meta Description</label>
                                <textarea name="meta_description" id="meta_description" rows="2" 
                                          class="form-control" 
                                          placeholder="SEO description...">{{ old('meta_description') }}</textarea>
                                <small class="text-muted">Recommended: 150-160 characters</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="meta_keywords">Meta Keywords</label>
                                <input type="text" name="meta_keywords" id="meta_keywords" 
                                       class="form-control" value="{{ old('meta_keywords') }}"
                                       placeholder="keyword1, keyword2, keyword3">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar Column -->
                <div class="col-md-4">
                    <!-- Publish Settings -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Publish</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="published_at">Publish Date</label>
                                <input type="datetime-local" name="published_at" id="published_at" 
                                       class="form-control" value="{{ old('published_at') }}">
                                <small class="text-muted">Leave empty for immediate publish</small>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input type="checkbox" name="is_featured" id="is_featured" 
                                       class="form-check-input" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label for="is_featured" class="form-check-label">Featured Post</label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input type="checkbox" name="allow_comments" id="allow_comments" 
                                       class="form-check-input" value="1" {{ old('allow_comments', true) ? 'checked' : '' }}>
                                <label for="allow_comments" class="form-check-label">Allow Comments</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save"></i> Save Post
                            </button>
                            <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-secondary btn-block">
                                Cancel
                            </a>
                        </div>
                    </div>
                    
                    <!-- Category -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Category</h6>
                        </div>
                        <div class="card-body">
                            <select name="category_id" id="category_id" class="form-control">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <a href="{{ route('admin.blog.categories.create') }}" class="btn btn-link btn-sm mt-2">
                                <i class="fas fa-plus"></i> Add New Category
                            </a>
                        </div>
                    </div>
                    
                    <!-- Tags -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Tags</h6>
                        </div>
                        <div class="card-body">
                            <select name="tags[]" id="tags" class="form-control select2-tags" multiple>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Featured Image -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Featured Image</h6>
                        </div>
                        <div class="card-body">
                            <input type="file" name="featured_image" id="featured_image" 
                                   class="form-control-file" accept="image/*">
                            <small class="text-muted">Recommended: 1200x630px. Max 2MB.</small>
                            <img id="image-preview" class="featured-image-preview d-none" alt="Preview">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Summernote
    $('#content').summernote({
        height: 400,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'italic', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
    
    // Initialize Select2 for tags
    $('.select2-tags').select2({
        placeholder: 'Select tags...',
        allowClear: true
    });
    
    // Auto-generate slug from title
    $('#title').on('blur', function() {
        if ($('#slug').val() === '') {
            var slug = $(this).val()
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            $('#slug').val(slug);
        }
    });
    
    // Image preview
    $('#featured_image').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result).removeClass('d-none');
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush
