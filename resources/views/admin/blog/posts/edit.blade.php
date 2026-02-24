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
    .current-image {
        position: relative;
        display: inline-block;
    }
    .remove-image-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(220, 53, 69, 0.9);
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        color: white;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Edit Blog Post</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Update blog post</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blog.posts.index') }}">Blog Posts</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-12">
        <form action="{{ route('admin.blog.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
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
                                       value="{{ old('title', $post->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="slug">Slug (URL)</label>
                                <input type="text" name="slug" id="slug" 
                                       class="form-control @error('slug') is-invalid @enderror" 
                                       value="{{ old('slug', $post->slug) }}">
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="excerpt">Excerpt</label>
                                <textarea name="excerpt" id="excerpt" rows="3" 
                                          class="form-control @error('excerpt') is-invalid @enderror">{{ old('excerpt', $post->excerpt) }}</textarea>
                                @error('excerpt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="content">Content <span class="text-danger">*</span></label>
                                <textarea name="content" id="content" 
                                          class="form-control @error('content') is-invalid @enderror">{{ old('content', $post->content) }}</textarea>
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
                                       class="form-control" value="{{ old('meta_title', $post->meta_title) }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="meta_description">Meta Description</label>
                                <textarea name="meta_description" id="meta_description" rows="2" 
                                          class="form-control">{{ old('meta_description', $post->meta_description) }}</textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="meta_keywords">Meta Keywords</label>
                                <input type="text" name="meta_keywords" id="meta_keywords" 
                                       class="form-control" value="{{ old('meta_keywords', $post->meta_keywords) }}">
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
                                    <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="scheduled" {{ old('status', $post->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="published_at">Publish Date</label>
                                <input type="datetime-local" name="published_at" id="published_at" 
                                       class="form-control" 
                                       value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}">
                            </div>
                            
                            <div class="form-check mb-2">
                                <input type="checkbox" name="is_featured" id="is_featured" 
                                       class="form-check-input" value="1" 
                                       {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}>
                                <label for="is_featured" class="form-check-label">Featured Post</label>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input type="checkbox" name="allow_comments" id="allow_comments" 
                                       class="form-check-input" value="1" 
                                       {{ old('allow_comments', $post->allow_comments) ? 'checked' : '' }}>
                                <label for="allow_comments" class="form-check-label">Allow Comments</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save"></i> Update Post
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
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
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
                                    <option value="{{ $tag->id }}" 
                                            {{ in_array($tag->id, old('tags', $selectedTags)) ? 'selected' : '' }}>
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
                            @if($post->featured_image)
                                <div class="current-image mb-3" id="current-image">
                                    <img src="{{ $post->featured_image_url }}" 
                                         class="featured-image-preview" alt="Current image">
                                    <button type="button" class="remove-image-btn" onclick="removeCurrentImage()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endif
                            
                            <input type="file" name="featured_image" id="featured_image" 
                                   class="form-control-file" accept="image/*">
                            <small class="text-muted">Upload new image to replace current one</small>
                            <img id="image-preview" class="featured-image-preview d-none" alt="Preview">
                        </div>
                    </div>
                    
                    <!-- Post Info -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Post Info</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><strong>Views:</strong> {{ number_format($post->views) }}</p>
                            <p class="mb-1"><strong>Created:</strong> {{ $post->created_at->format('M d, Y H:i') }}</p>
                            <p class="mb-0"><strong>Updated:</strong> {{ $post->updated_at->format('M d, Y H:i') }}</p>
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
    
    // Image preview
    $('#featured_image').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result).removeClass('d-none');
                $('#current-image').hide();
            };
            reader.readAsDataURL(file);
        }
    });
});

function removeCurrentImage() {
    if (confirm('Are you sure you want to remove the current image?')) {
        $.ajax({
            url: '{{ route("admin.blog.posts.remove-image", $post) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#current-image').remove();
            }
        });
    }
}
</script>
@endpush
