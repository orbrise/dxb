@extends('admin.layout.master')

@push('css')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css" rel="stylesheet" />
<style>
.img-preview {
    max-width: 200px;
    cursor: pointer;
}
.profile-photos img {
    width: 100px;
    margin-right: 10px;
}

.profile-photo-thumb {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.profile-photo-thumb:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
}

.modal-xl {
    max-width: 1200px;
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.g-2 {
    --bs-gutter-x: 0.5rem;
    --bs-gutter-y: 0.5rem;
}
.swal2-modal .btn {
    min-width: 5.33333em;
    font-size: 12px;
}

.dataTables_wrapper .dataTables_filter input {
    margin-left: 0.5em;
}

.dataTables_wrapper .dataTables_length select {
    padding: 4px 30px 4px 10px;
    min-width: 80px;
    width: auto;
}

.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.stats-card h3 {
    font-size: 2.5rem;
    margin: 0;
    font-weight: bold;
}

.stats-card p {
    margin: 5px 0 0 0;
    opacity: 0.9;
}
h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
    margin: 0px; 
        line-height: 24px;
}
.modal-body h1, .modal-body h2, .modal-body h3, .modal-body h4, .modal-body h5, .modal-body h6 {
    color: #474747;
}

.img-preview
 {
    max-width: 200px;
    cursor: pointer;
    width: 91px;
    height: 112px;
    object-fit: contain;
}
</style>
@endpush

@section('content')
 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Pending Profiles</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage pending profiles effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active"></li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>
<div class="container-fluid">
    <!-- Stats Card -->
    

    <div class="row">
        <div class="col-12">
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Pending Photo Verifications</h5>
                    <div class="d-flex gap-2 align-items-center">
                        <label class="mb-0 mr-2">Show:</label>
                        <select id="perPageSelect" class="form-control form-control-sm" style="width: auto;">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <label class="mb-0 ml-2">entries</label>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Bar -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.verifications') }}" id="searchForm">
                                <div class="input-group">
                                    <input type="text" 
                                           name="search" 
                                           class="form-control" 
                                           placeholder="Search by profile name..." 
                                           value="{{ request('search') }}">
                                    <input type="hidden" name="per_page" id="searchPerPage" value="{{ request('per_page', 15) }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                        @if(request('search'))
                                        <a href="{{ route('admin.verifications') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Clear
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 text-right">
                            @if(request('search'))
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle"></i> 
                                Showing results for: <strong>"{{ request('search') }}"</strong>
                            </p>
                            @endif
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="verificationTable">
                            <thead>
                                <tr>
                                    <th>Profile</th>
                                    <th>Verification Photo</th>
                                    <th>Profile Photos</th>
                                    <th>Photo Code</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($photos as $photo)
                                    @if($photo->profile && $photo->profile->ggender && $photo->profile->gcity)
                                    <tr id="row-{{$photo->id}}">
                                        <td>
                                            <a href="/{{ strtolower($photo->profile->ggender->name) }}-escorts-in-{{ strtolower($photo->profile->gcity->name) }}/{{ $photo->profile->id }}/{{ $photo->profile->name }}" target="_blank">
                                                {{ $photo->profile->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <img src="{{ smart_asset('userimages/'.$photo->user_id.'/verification/'.$photo->photo) }}" 
                                                 class="img-preview" 
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#photoModal{{ $photo->id }}">
                                        </td>
                                        <td>
                                            <div class="profile-photos">
                                         
                                                @if($photo->profile && $photo->profile->singleimg->image)
                                                <img src="{{smart_asset("userimages/".$photo->profile->user_id."/".$photo->profile->id."/".$photo->profile->singleimg->image)}}" 
                                                class="img-thumbnail">
                                                @else
                                                    <span class="text-muted">No profile images</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td> <div class="badge bg-warning " style="font-size:20px" >{{$photo->profile->photo_code}}</span></div>
                                        <td>{{ $photo->created_at->diffForHumans() }}</td>
                                        <td>
                                            <button class="btn btn-info btn-sm view-btn" 
                                                    data-toggle="modal" 
                                                    data-target="#viewModal{{ $photo->id }}">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            
                                            <select class="form-control form-control-sm d-inline-block action-dropdown" 
                                                    style="width: auto;"
                                                    data-id="{{ $photo->id }}"
                                                    data-profile-name="{{ $photo->profile->name }}"
                                                    data-profile-id="{{ $photo->profile->id }}"
                                                    data-profile-slug="{{ $photo->profile->slug }}">
                                                <option value="">-- Action --</option>
                                                <option value="approve">✓ Approve</option>
                                                <option value="reject">✗ Reject</option>
                                            </select>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                        
                        @if($photos->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">
                                @if(request('search'))
                                    No verifications found matching "{{ request('search') }}"
                                @else
                                    No pending verifications at the moment
                                @endif
                            </p>
                        </div>
                        @endif
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing {{ $photos->firstItem() ?? 0 }} to {{ $photos->lastItem() ?? 0 }} of {{ $photos->total() }} entries
                                @if(request('search'))
                                    (filtered from total entries)
                                @endif
                            </div>
                            <div>
                                {{ $photos->appends(['search' => request('search'), 'per_page' => request('per_page', 15)])->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($photos as $photo)
@if($photo->profile && $photo->profile->ggender && $photo->profile->gcity)
<div class="modal fade" id="photoModal{{ $photo->id }}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <img src="{{ smart_asset('userimages/'.$photo->user_id.'/verification/'.$photo->photo) }}" 
                     class="img-fluid">
            </div>
        </div>
    </div>
</div>

{{-- View Modal with All Photos --}}
<div class="modal fade" id="viewModal{{ $photo->id }}" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-images"></i> {{ $photo->profile->name }} - Photo Verification Review
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    {{-- Verification Photo --}}
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">
                                    <i class="fas fa-id-card"></i> Verification Photo
                                    <span class="badge badge-dark float-right" style="font-size: 16px;">Code: {{ $photo->profile->photo_code }}</span>
                                </h6>
                            </div>
                            <div class="card-body text-center">
                                <img src="{{ smart_asset('userimages/'.$photo->user_id.'/verification/'.$photo->photo) }}" 
                                     class="img-fluid rounded shadow-sm" 
                                     style="max-height: 400px;">
                                <p class="text-muted mt-2 mb-0">
                                    <small><i class="fas fa-clock"></i> Submitted {{ $photo->created_at->diffForHumans() }}</small>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Profile Photos --}}
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-images"></i> Profile Photos 
                                    <span class="badge badge-light text-dark float-right">{{ $photo->profile->allimages ? $photo->profile->allimages->count() : 0 }} photos</span>
                                </h6>
                            </div>
                            <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                                @if($photo->profile->allimages && $photo->profile->allimages->count() > 0)
                                    <div class="row g-2">
                                        @foreach($photo->profile->allimages as $image)
                                            <div class="col-6 col-md-4">
                                                <div class="position-relative">
                                                    <img src="{{ smart_asset('userimages/'.$photo->profile->user_id.'/'.$photo->profile->id.'/'.$image->image) }}" 
                                                         class="img-fluid rounded shadow-sm profile-photo-thumb" 
                                                         style="cursor: pointer; width: 100%; height: 150px; object-fit: cover;"
                                                         data-toggle="modal" 
                                                         data-target="#profilePhotoModal{{ $photo->id }}_{{ $image->id }}">
                                                    @if($image->is_main)
                                                        <span class="badge badge-success position-absolute" style="top: 5px; left: 5px;">
                                                            <i class="fas fa-star"></i> Main
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center text-muted py-5">
                                        <i class="fas fa-image fa-3x mb-3 opacity-50"></i>
                                        <p>No profile photos available</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Profile Info --}}
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0 text-white"><i class="fas fa-user"></i> Profile Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> {{ $photo->profile->name }}</p>
                                <p><strong>City:</strong> {{ $photo->profile->getcity->name ?? 'N/A' }}</p>
                                <p><strong>Gender:</strong> {{ $photo->profile->ggender->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Age:</strong> {{ $photo->profile->age ?? 'N/A' }}</p>
                                <p><strong>Profile Link:</strong> 
                                    <a href="/{{ strtolower($photo->profile->ggender->name) }}-escorts-in-{{ strtolower($photo->profile->getcity->name) }}/{{ $photo->profile->id }}/{{ $photo->profile->name }}" 
                                       target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt"></i> View Profile
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <select class="form-control form-control-sm action-dropdown" 
                        style="max-width: 200px;"
                        data-id="{{ $photo->id }}"
                        data-profile-name="{{ $photo->profile->name }}"
                        data-profile-id="{{ $photo->profile->id }}"
                        data-profile-slug="{{ $photo->profile->slug }}">
                    <option value="">-- Select Action --</option>
                    <option value="approve">Approve</option>
                    <option value="reject">Reject</option>
                </select>
            </div>
        </div>
    </div>
</div>

{{-- Individual Profile Photo Modals --}}
@if($photo->profile && $photo->profile->allimages)
@foreach($photo->profile->allimages as $image)
<div class="modal fade" id="profilePhotoModal{{ $photo->id }}_{{ $image->id }}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body text-center">
                <img src="{{ smart_asset('userimages/'.$photo->profile->user_id.'/'.$photo->profile->id.'/'.$image->image) }}" 
                     class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endforeach
@endif
@endif

@endforeach

@endsection

@push('js')

<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        }
    });

    // Handle per page change
    $('#perPageSelect').on('change', function() {
        const perPage = $(this).val();
        const currentSearch = "{{ request('search') }}";
        let url = "{{ route('admin.verifications') }}?per_page=" + perPage;
        if (currentSearch) {
            url += "&search=" + encodeURIComponent(currentSearch);
        }
        window.location.href = url;
    });

    // Update hidden per_page input when searching
    $('#searchForm').on('submit', function() {
        $('#searchPerPage').val($('#perPageSelect').val());
    });

    // Handle action dropdown changes
    $(document).on('change', '.action-dropdown', function() {
        const action = $(this).val();
        const id = $(this).data('id');
        const profileName = $(this).data('profile-name');
        const profileId = $(this).data('profile-id');
        const profileSlug = $(this).data('profile-slug');
        const $dropdown = $(this);
        
        if (!action) return;
        
        // Reset dropdown
        $dropdown.val('');
        
        if (action === 'view') {
            const target = $dropdown.find('option:selected').data('target');
            if (target) {
                $(target).modal('show');
            }
        } else if (action === 'approve') {
            handleApprove(id);
        } else if (action === 'reject') {
            handleReject(id, profileName, profileId, profileSlug);
        }
    });

    function handleApprove(id) {
        const row = $(`#row-${id}`);
        
        // Close any open Bootstrap 4 modals first
        $('.modal').modal('hide');
        
        // Wait for modal to close, then show confirmation
        setTimeout(() => {
            swal({
                title: 'Approve Verification?',
                text: 'This will mark the photo as verified.',
                type: 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes, Approve',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result) {
                    $.ajax({
                        url: `/admin/verifications/${id}/approve`,
                        type: 'POST',
                        success: function() {
                            row.fadeOut();
                            swal('Approved!', 'Photo verified successfully', 'success');
                        },
                        error: function() {
                            swal('Error!', 'Failed to approve photo', 'error');
                        }
                    });
                }
            });
        }, 300);
    }

    function handleReject(id, profileName, profileId, profileSlug) {
        const row = $(`#row-${id}`);
        
        // Close any open Bootstrap 4 modals first
        $('.modal').modal('hide');
        
        // Wait for modal to close, then show rejection form
        setTimeout(() => {
            // Generate URLs
            const baseUrl = window.location.origin;
            const verifyUrl = `${baseUrl}/my-profile/${profileSlug}/${profileId}/verify-photo`;
            const editUrl = `${baseUrl}/edit-profile/${profileSlug}/${profileId}`;
            const upgradeUrl = `${baseUrl}/my-profile/${profileSlug}/${profileId}/upgrade`;
            
            swal({
                title: 'Rejection Details',
                html: `
                    <div class="form-group text-left mb-3">
                        <label for="rejection-reason" class="font-weight-bold">Rejection Reason *</label>
                        <input id="rejection-reason" class="swal2-input" placeholder="Enter reason for rejection" style="width: 100%; margin: 0;">
                    </div>
                    <div class="form-group text-left mb-3">
                        <label for="rejection-link" class="font-weight-bold">Action Link (Optional)</label>
                        <div class="input-group" style="margin-bottom: 10px;">
                            <input id="rejection-link" class="swal2-input" placeholder="e.g., https://example.com/help" style="width: 100%; margin: 0;">
                        </div>
                        <div class="btn-group btn-group-sm mt-2" role="group" style="display: flex; gap: 5px;">
                            <button type="button" class="btn btn-outline-primary quick-link-btn" data-url="${verifyUrl}" style="flex: 1;">
                                <i class="fas fa-check-circle"></i> Verify Photo
                            </button>
                            <button type="button" class="btn btn-outline-info quick-link-btn" data-url="${editUrl}" style="flex: 1;">
                                <i class="fas fa-edit"></i> Edit Profile
                            </button>
                            <button type="button" class="btn btn-outline-success quick-link-btn" data-url="${upgradeUrl}" style="flex: 1;">
                                <i class="fas fa-arrow-up"></i> Upgrade
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">Click a button above to auto-fill the link, or enter a custom URL</small>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Reject',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                customClass: 'swal-wide'
            }).then((result) => {
                if (result) {
                    const reason = document.getElementById('rejection-reason').value;
                    const link = document.getElementById('rejection-link').value;
                    
                    if (!reason) {
                        swal('Error!', 'Please enter a rejection reason', 'error');
                        return false;
                    }
                    
                    $.ajax({
                        url: `/admin/verifications/${id}/reject`,
                        type: 'POST',
                        data: {
                            reason: reason,
                            link: link
                        },
                        success: function() {
                            row.fadeOut();
                            swal('Rejected!', 'Photo has been rejected', 'success');
                        },
                        error: function() {
                            swal('Error!', 'Failed to reject photo', 'error');
                        }
                    });
                }
            });
            
            // Add click handlers for quick link buttons after SweetAlert opens
            setTimeout(() => {
                document.querySelectorAll('.quick-link-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        document.getElementById('rejection-link').value = this.getAttribute('data-url');
                        
                        // Remove active class from all buttons
                        document.querySelectorAll('.quick-link-btn').forEach(b => {
                            b.classList.remove('active');
                            b.style.backgroundColor = '';
                            b.style.borderColor = '';
                            b.style.color = '';
                        });
                        
                        // Add active class to clicked button
                        this.classList.add('active');
                        const color = this.classList.contains('btn-outline-primary') ? '#007bff' :
                                    this.classList.contains('btn-outline-info') ? '#17a2b8' : '#28a745';
                        this.style.backgroundColor = color;
                        this.style.borderColor = color;
                        this.style.color = '#fff';
                    });
                });
            }, 100);
        }, 300);
    }
});
</script>
@endpush