@extends('admin.layout.master')

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">URL Aliases</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Manage custom URL redirects and aliases</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">URL Aliases</li>
        </ol>
    </div>
</div>

<div class="row mt-3 mb-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0 float-left">URL Alias Management</h5>
                <a href="{{ route('admin.url-aliases.create') }}" class="btn btn-primary float-right">Add New Alias</a>
            </div>

            <div class="card-body">
                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <!-- Info Box -->
                <div class="alert alert-info">
                    <i class="fa fa-info-circle mr-2"></i>
                    <strong>How it works:</strong> Create custom URLs that redirect to or serve the same content as your base URLs. 
                    For example: <code>/all_female-escorts-in-dubai</code> → <code>/female-escorts-in-dubai</code>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Custom URL</th>
                                <th>Base Pattern</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($aliases as $alias)
                            <tr>
                                <td>
                                    <code>/{{ $alias->custom_url }}</code>
                                    <br>
                                    <small class="text-muted">
                                        <a href="{{ url($alias->custom_url) }}" target="_blank" class="text-primary">
                                            <i class="fa fa-external-link-alt"></i> Test Link
                                        </a>
                                    </small>
                                </td>
                                <td>
                                    <code>/{{ $alias->base_pattern }}</code>
                                    <br>
                                    <small class="text-muted">
                                        <a href="{{ url($alias->base_pattern) }}" target="_blank" class="text-primary">
                                            <i class="fa fa-external-link-alt"></i> Original
                                        </a>
                                    </small>
                                </td>
                                <td>
                                    @if($alias->redirect_type == 'canonical')
                                        <span class="badge badge-info">Canonical</span>
                                        <br><small class="text-muted">Same content</small>
                                    @else
                                        <span class="badge badge-warning">{{ $alias->redirect_type }} Redirect</span>
                                        <br><small class="text-muted">Redirects user</small>
                                    @endif
                                </td>
                                <td>
                                    @if($alias->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $alias->description ?? '-' }}</td>
                                <td>{{ $alias->created_at->format('M j, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.url-aliases.edit', $alias->id) }}" class="btn btn-info btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        
                                        <form action="{{ route('admin.url-aliases.toggle', $alias->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-{{ $alias->is_active ? 'warning' : 'success' }} btn-sm" 
                                                    title="{{ $alias->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fa fa-{{ $alias->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('admin.url-aliases.destroy', $alias->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                    onclick="return confirm('Are you sure you want to delete this alias?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fa fa-link fa-3x mb-3"></i>
                                    <br>
                                    No URL aliases created yet.
                                    <br>
                                    <a href="{{ route('admin.url-aliases.create') }}" class="btn btn-primary mt-2">Create First Alias</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $aliases->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
