@extends('admin.layout.master')

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Blog Tags</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Manage blog tags</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Blog Tags</li>
        </ol>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3 mt-3">
    <a href="{{ route('admin.blog.tags.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create New Tag
    </a>
    <form method="GET" action="{{ route('admin.blog.tags.index') }}" class="form-inline">
        <label class="mr-2">Per page</label>
        @php($pp = request('perPage', $tags->perPage()))
        <select name="perPage" class="form-control" onchange="this.form.submit()">
            <option value="10" {{ $pp==10 ? 'selected' : '' }}>10</option>
            <option value="25" {{ $pp==25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ $pp==50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ $pp==100 ? 'selected' : '' }}>100</option>
        </select>
    </form>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
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
                            <th>Name</th>
                            <th>Slug</th>
                            <th width="100">Posts</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tags as $tag)
                            <tr>
                                <td>{{ $tag->id }}</td>
                                <td><span class="badge badge-secondary">{{ $tag->name }}</span></td>
                                <td><code>{{ $tag->slug }}</code></td>
                                <td>{{ $tag->posts_count }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.blog.tags.edit', $tag) }}" 
                                           class="btn btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.blog.tags.destroy', $tag) }}" 
                                              method="POST" style="display: inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this tag?');">
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
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-tags fa-3x mb-3"></i>
                                    <p>No tags found.</p>
                                    <a href="{{ route('admin.blog.tags.create') }}" class="btn btn-primary">
                                        Create your first tag
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <div class="d-flex justify-content-center mt-3">
                    {{ $tags->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
