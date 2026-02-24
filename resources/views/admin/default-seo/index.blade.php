@extends('admin.layout.master')

@section('content')

<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Default SEO Settings</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Manage default SEO fallback content for when no specific matches are found</p>
    </div>
    <!-- /.page-title-left -->
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('seo.index')}}">SEO Management</a></li>
            <li class="breadcrumb-item active">Default SEO Settings</li>
        </ol>
    </div>
    <!-- /.page-title-right -->
</div>

<div class="row mt-3 mb-3">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0 float-left" style="float:left">Default SEO Settings</h5>
                <a href="{{ route('default-seo.create') }}" style="float:right" class="btn btn-primary float-right">Add New Default SEO</a>
            </div><!-- end card header -->

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

                <!-- Info Box -->
                <div class="alert alert-info" role="alert">
                    <i class="fa fa-info-circle mr-2"></i>
                    <strong>How it works:</strong> When no specific SEO content is found for a gender/city combination, the system will use these default settings as fallback. Higher priority settings are used first.
                </div>

                <div class="table-responsive">
                    <table id="default_seo_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Keywords</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($defaultSeoSettings as $seoSetting)
                            <tr>
                                <td>
                                    <span class="badge badge-secondary">{{ $seoSetting->name }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $seoSetting->priority > 5 ? 'success' : ($seoSetting->priority > 2 ? 'warning' : 'light') }}">
                                        {{ $seoSetting->priority }}
                                    </span>
                                </td>
                                <td>
                                    @if($seoSetting->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($seoSetting->title, 40) }}</td>
                                <td>{{ Str::limit($seoSetting->description, 50) }}</td>
                                <td>{{ Str::limit($seoSetting->keywords, 30) }}</td>
                                <td>
                                    <a href="{{ route('default-seo.edit', $seoSetting->id) }}" class="btn btn-info btn-sm">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </a>
                                    <form action="{{ route('default-seo.destroy', $seoSetting->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this default SEO setting?')">
                                            <i class="fa-solid fa-trash-can"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <p class="my-3">No default SEO settings found.</p>
                                    <a href="{{ route('default-seo.create') }}" class="btn btn-primary">Create Your First Default SEO Setting</a>
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
    // DataTable initialization if needed
    $('#default_seo_table').DataTable({
        "order": [[ 1, "desc" ]], // Order by priority column descending
        "pageLength": 25,
        "responsive": true
    });
});
</script>
@endpush