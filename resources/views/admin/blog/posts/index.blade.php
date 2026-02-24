@extends('admin.layout.master')

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Blog Posts</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Manage blog posts</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Blog Posts</li>
        </ol>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3 mt-3">
    <a href="{{ route('admin.blog.posts.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create New Post
    </a>
    <div class="d-flex align-items-center">
        <form method="GET" action="{{ route('admin.blog.posts.index') }}" class="form-inline">
            <input type="text" name="search" class="form-control mr-2" placeholder="Search posts..." 
                   value="{{ request('search') }}">
            <select name="status" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
            </select>
            <select name="category" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <select name="perPage" class="form-control" onchange="this.form.submit()">
                <option value="10" {{ request('perPage', 10) == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
            </select>
            <button type="submit" class="btn btn-secondary ml-2">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

<div class="row mt-3 mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="60">ID</th>
                            <th width="80">Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th width="100">Status</th>
                            <th width="120">Published</th>
                            <th width="80">Views</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody> 
                        @forelse($posts as $post)
                            <tr>
                                <td>{{ $post->id }}</td>
                                <td>
                                    @if($post->featured_image)
                                        <img src="{{ $post->featured_image_url }}" 
                                             alt="{{ $post->title }}" 
                                             style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                    @else
                                        <span class="text-muted"><i class="fas fa-image"></i></span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ Str::limit($post->title, 50) }}</strong>
                                    @if($post->is_featured)
                                        <span class="badge badge-warning">Featured</span>
                                    @endif
                                </td>
                                <td>{{ $post->category->name ?? '-' }}</td>
                                <td>{{ $post->author->name ?? 'Unknown' }}</td>
                                <td>
                                    @if($post->status == 'published')
                                        <span class="badge badge-success">Published</span>
                                    @elseif($post->status == 'draft')
                                        <span class="badge badge-secondary">Draft</span>
                                    @else
                                        <span class="badge badge-info">Scheduled</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $post->published_at ? $post->published_at->format('M d, Y') : '-' }}
                                </td>
                                <td>{{ number_format($post->views) }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.blog.posts.edit', $post) }}" 
                                           class="btn btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.blog.posts.show', $post) }}" 
                                           class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.blog.posts.destroy', $post) }}" 
                                              method="POST" style="display: inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this post?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-newspaper fa-3x mb-3"></i>
                                    <p>No blog posts found.</p>
                                    <a href="{{ route('admin.blog.posts.create') }}" class="btn btn-primary">
                                        Create your first post
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <div class="d-flex justify-content-center mt-3">
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
