@extends('admin.layout.master')

@section('content')

<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Page-Specific SEO</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Set title / description / keywords for individual frontend routes</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('default-seo.index')}}">Default SEO</a></li>
            <li class="breadcrumb-item active">Page-Specific SEO</li>
        </ol>
    </div>
</div>

<div class="row mt-3 mb-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0 float-left" style="float:left">Frontend Routes</h5>
                <a href="{{ route('default-seo.index') }}" style="float:right" class="btn btn-secondary float-right">
                    <i class="fa fa-arrow-left"></i> Back to Default SEO
                </a>
            </div>

            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="alert alert-info" role="alert">
                    <i class="fa fa-info-circle mr-2"></i>
                    <strong>How it works:</strong> Each route below can have its own SEO. Rows marked <span class="badge badge-success">Custom</span> have an override; rows marked <span class="badge badge-light">Default</span> fall back to the global default. Click <em>Set SEO</em> to add a custom entry, or <em>Edit</em> to change an existing one.
                </div>

                <div class="table-responsive">
                    <table id="page_seo_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Path</th>
                                <th>Route Name</th>
                                <th>Status</th>
                                <th>Current Title</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($routes as $route)
                                @php $setting = $settings[$route->path] ?? null; @endphp
                                <tr>
                                    <td>
                                        <a href="{{ url($route->path) }}" target="_blank" rel="noopener">/{{ $route->path }}</a>
                                    </td>
                                    <td>
                                        @if($route->name)
                                            <code>{{ $route->name }}</code>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($setting && $setting->is_active)
                                            <span class="badge badge-success">Custom</span>
                                        @elseif($setting && !$setting->is_active)
                                            <span class="badge badge-warning">Custom (Inactive)</span>
                                        @else
                                            <span class="badge badge-light">Default</span>
                                        @endif
                                    </td>
                                    <td>{{ $setting ? Str::limit($setting->title, 60) : '—' }}</td>
                                    <td>
                                        @if($setting)
                                            <a href="{{ route('default-seo.edit', $setting->id) }}" class="btn btn-info btn-sm">
                                                <i class="fa-solid fa-pen"></i> Edit
                                            </a>
                                            <form action="{{ route('default-seo.destroy', $setting->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Remove custom SEO for /{{ $route->path }}? It will revert to the global default.')">
                                                    <i class="fa-solid fa-trash-can"></i> Remove
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('default-seo.create', ['name' => $route->path, 'priority' => 10]) }}" class="btn btn-primary btn-sm">
                                                <i class="fa-solid fa-plus"></i> Set SEO
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        <p class="my-3">No SEO-eligible routes found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
$(document).ready(function() {
    $('#page_seo_table').DataTable({
        "order": [[ 0, "asc" ]],
        "pageLength": 50,
        "responsive": true
    });
});
</script>
@endpush
