@extends('admin.layout.master')

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">View Post</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">{{ $post->title }}</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blog.posts.index') }}">Blog Posts</a></li>
            <li class="breadcrumb-item active">View</li>
        </ol>
    </div>
</div>
 
<div class="row mt-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                @if($post->featured_image)
                    <img src="{{ $post->featured_image_url }}" 
                         class="img-fluid rounded mb-4" alt="{{ $post->title }}"
                         style="max-height: 400px; width: 100%; object-fit: cover;">
                @endif
                
                <h2>{{ $post->title }}</h2>
                
                <div class="text-muted mb-3">
                    <span><i class="fas fa-user"></i> {{ $post->author->name ?? 'Unknown' }}</span>
                    <span class="mx-2">|</span>
                    <span><i class="fas fa-calendar"></i> {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Not published' }}</span>
                    <span class="mx-2">|</span>
                    <span><i class="fas fa-folder"></i> {{ $post->category->name ?? 'Uncategorized' }}</span>
                    <span class="mx-2">|</span>
                    <span><i class="fas fa-eye"></i> {{ number_format($post->views) }} views</span>
                </div>
                
                @if($post->excerpt)
                    <div class="lead text-muted mb-4">
                        {{ $post->excerpt }}
                    </div>
                @endif
                
                <hr>
                
                <div class="post-content">
                    {!! $post->content !!}
                </div>
                
                @if($post->tags->count() > 0)
                    <hr>
                    <div class="tags">
                        <strong>Tags:</strong>
                        @foreach($post->tags as $tag)
                            <span class="badge badge-secondary">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Post Details</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>Status</strong></td>
                        <td>
                            @if($post->status == 'published')
                                <span class="badge badge-success">Published</span>
                            @elseif($post->status == 'draft')
                                <span class="badge badge-secondary">Draft</span>
                            @else
                                <span class="badge badge-info">Scheduled</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Featured</strong></td>
                        <td>{{ $post->is_featured ? 'Yes' : 'No' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Comments</strong></td>
                        <td>{{ $post->allow_comments ? 'Allowed' : 'Disabled' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Slug</strong></td>
                        <td><code>{{ $post->slug }}</code></td>
                    </tr>
                    <tr>
                        <td><strong>Created</strong></td>
                        <td>{{ $post->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Updated</strong></td>
                        <td>{{ $post->updated_at->format('M d, Y H:i') }}</td>
                    </tr>
                </table>
                
                <hr>
                
                <a href="{{ route('admin.blog.posts.edit', $post) }}" class="btn btn-primary btn-block">
                    <i class="fas fa-edit"></i> Edit Post
                </a>
                <a href="{{ route('admin.blog.posts.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Back to Posts
                </a>
            </div>
        </div>
        
        @if($post->meta_title || $post->meta_description)
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">SEO Preview</h6>
                </div>
                <div class="card-body">
                    <div class="seo-preview" style="font-family: Arial, sans-serif;">
                        <div style="color: #1a0dab; font-size: 18px;">
                            {{ $post->meta_title ?: $post->title }}
                        </div>
                        <div style="color: #006621; font-size: 14px;">
                            blog.massagerepublic.com.co/{{ $post->slug }}
                        </div>
                        <div style="color: #545454; font-size: 13px;">
                            {{ Str::limit($post->meta_description ?: $post->excerpt, 160) }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
