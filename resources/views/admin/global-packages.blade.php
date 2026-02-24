@extends("admin.layout.master")

@section("content")

 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Global Packages Management</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage global packages available to all countries</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Global Packages</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>

<!-- Existing Global Packages Section -->
<div class="row mt-3 mb-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fa fa-globe"></i> All Global Packages
                </h5>
            </div>
            <div class="card-body">
                <div id="packagesTableContainer">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Tagline</th>
                                <th>Price Tiers</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="packagesTable">
                            @forelse($packages as $package)
                            <tr data-package-id="{{$package->id}}">
                                <td>{{$package->name}}</td>
                                <td>{{$package->tagline}}</td>
                                <td>
                                    @php
                                        $tiers = json_decode($package->price_tiers, true) ?? [];
                                    @endphp
                                    @if(count($tiers) > 0)
                                        <ul class="mb-0">
                                            @foreach($tiers as $tier)
                                                <li>{{$tier['days']}} days - ${{$tier['price']}}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">No tiers</span>
                                    @endif
                                </td>
                                <td>{!! nl2br(e($package->description)) !!}</td>
                                <td>
                                    <button class="btn btn-info btn-sm editPackage" data-id="{{$package->id}}">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm deletePackage" data-id="{{$package->id}}">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No global packages found. Create one below.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create New Global Package Section -->
<div class="row mb-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header" style="cursor: pointer;" id="createPackageHeader">
                <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                    <span>
                        <i class="fa fa-plus-circle"></i> Create New Global Package
                    </span>
                    <i class="fa fa-chevron-down" id="toggleIcon"></i>
                </h5>
            </div>
            <div id="createPackageCollapse" style="display: none;">
                <div class="card-body">
                    <div id="successMessage" style="display:none;" class="alert alert-success alert-dismissible fade show">
                        Package created successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>

                    <form id="packageForm">
                        {{csrf_field()}}
                        <input type="hidden" name="is_global" value="1">
                        
                        <div class="mb-3">
                            <label class="form-label">Package Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tag Line</label>
                            <input type="text" class="form-control" name="tagline" required>
                        </div>
                        
                        <div id="priceTiers" class="mb-3">
                            <h6 class="mb-3">Price Tiers</h6>
                            <p class="text-muted small">Define pricing options for different durations (global pricing)</p>
                            <div id="tiersContainer">
                                <div class="row price-tier mb-3">
                                    <div class="col-md-5">
                                        <label class="form-label">Days</label>
                                        <input type="number" class="form-control" name="tiers[0][days]" placeholder="e.g., 10" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Price ($)</label>
                                        <input type="number" step="0.01" class="form-control" name="tiers[0][price]" placeholder="e.g., 30" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-success w-100 btn-add-tier">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="4"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa fa-plus"></i> Create Global Package
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Package Modal -->
<div class="modal fade" id="editPackageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Global Package</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editPackageForm">
                    {{csrf_field()}}
                    <input type="hidden" name="package_id" id="edit_package_id">
                    <input type="hidden" name="is_global" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label">Package Name</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tag Line</label>
                        <input type="text" class="form-control" name="tagline" id="edit_tagline" required>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="mb-3">Price Tiers</h6>
                        <div id="editTiersContainer"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="4"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveEditPackage">Save Changes</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
$(document).ready(function() {
    let tierIndex = 1;

    // Toggle create package form
    $('#createPackageHeader').on('click', function() {
        $('#createPackageCollapse').slideToggle();
        $('#toggleIcon').toggleClass('fa-chevron-down fa-chevron-up');
    });

    // Add tier
    $(document).on('click', '.btn-add-tier', function() {
        const container = $(this).closest('#tiersContainer, #editTiersContainer');
        const newTier = `
            <div class="row price-tier mb-3">
                <div class="col-md-5">
                    <label class="form-label">Days</label>
                    <input type="number" class="form-control" name="tiers[${tierIndex}][days]" placeholder="e.g., 10" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Price ($)</label>
                    <input type="number" step="0.01" class="form-control" name="tiers[${tierIndex}][price]" placeholder="e.g., 30" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger w-100 btn-remove-tier">-</button>
                </div>
            </div>
        `;
        container.append(newTier);
        tierIndex++;
    });

    // Remove tier
    $(document).on('click', '.btn-remove-tier', function() {
        $(this).closest('.price-tier').remove();
    });

    // Create package
    $('#packageForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: '{{route("admin.addpackage")}}',
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#successMessage').show();
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                alert('Error creating package: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    // Edit package
    $(document).on('click', '.editPackage', function() {
        const packageId = $(this).data('id');
        
        $.ajax({
            url: `/admin/packages/${packageId}`,
            method: 'GET',
            success: function(pkg) {
                $('#edit_package_id').val(pkg.id);
                $('#edit_name').val(pkg.name);
                $('#edit_tagline').val(pkg.tagline);
                $('#edit_description').val(pkg.description);
                
                // Load tiers
                $('#editTiersContainer').empty();
                const tiers = JSON.parse(pkg.price_tiers || '[]');
                tiers.forEach((tier, index) => {
                    const tierHtml = `
                        <div class="row price-tier mb-3">
                            <div class="col-md-5">
                                <label class="form-label">Days</label>
                                <input type="number" class="form-control" name="tiers[${index}][days]" value="${tier.days}" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Price ($)</label>
                                <input type="number" step="0.01" class="form-control" name="tiers[${index}][price]" value="${tier.price}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn ${index === 0 ? 'btn-success btn-add-tier' : 'btn-danger btn-remove-tier'} w-100">${index === 0 ? '+' : '-'}</button>
                            </div>
                        </div>
                    `;
                    $('#editTiersContainer').append(tierHtml);
                });
                
                tierIndex = tiers.length;
                $('#editPackageModal').modal('show');
            }
        });
    });

    // Save edited package
    $('#saveEditPackage').on('click', function() {
        const formData = $('#editPackageForm').serialize();
        const packageId = $('#edit_package_id').val();

        $.ajax({
            url: `/admin/packages/${packageId}`,
            method: 'POST',
            data: formData + '&_method=PUT',
            success: function(response) {
                $('#editPackageModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Error updating package: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    // Delete package
    $(document).on('click', '.deletePackage', function() {
        if (!confirm('Are you sure you want to delete this package?')) return;
        
        const packageId = $(this).data('id');
        
        $.ajax({
            url: `/admin/packages/${packageId}`,
            method: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                _method: 'DELETE'
            },
            success: function() {
                $(`tr[data-package-id="${packageId}"]`).fadeOut(300, function() {
                    $(this).remove();
                });
            },
            error: function(xhr) {
                alert('Error deleting package: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });
});
</script>
@endpush
