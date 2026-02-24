@extends("admin.layout.master")

@section("content")

 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Packages Management</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage country-specific package pricing</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Packages</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>

<!-- Country Selection -->
<div class="row mt-3 mb-3">
    <div class="col-lg-12">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0 text-white">
                    <i class="fa fa-globe"></i> Select Country
                </h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Choose a country to manage packages:</label>
                        <select class="form-control form-control-lg" id="countryFilter">
                            <option value="">-- Select Country --</option>
                            @foreach($countries as $country)
                                <option value="{{$country->id}}" >
                                    {{$country->nicename}} @if($country->domain_prefix)({{$country->domain_prefix}}.domain.com)@endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info mb-0 mt-3 mt-md-0">
                            <i class="fa fa-info-circle"></i> 
                            <strong>Note:</strong> Packages and pricing are managed per country. Select a country to view and edit its packages.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="packagesSection" style="display: none;">

<!-- Existing Packages Section (Priority) -->
<div class="row mb-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Packages for <span id="packagesCountryName" class="text-primary"></span>
                </h5>
            </div>
            <div class="card-body">
                <div id="packagesTableContainer">
                    <!-- Packages will load here via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create New Package Section (Collapsible) -->
<div class="row mb-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#createPackageCollapse">
                <h5 class="card-title mb-0 d-flex justify-content-between align-items-center">
                    <span>
                        <i class="fa fa-plus-circle"></i> Create New Package for <span id="selectedCountryName" class="text-primary"></span>
                    </span>
                    <i class="fa fa-chevron-down"></i>
                </h5>
            </div>
            <div id="createPackageCollapse" class="collapse">
                <div class="card-body">
                    <div id="successMessage" style="display:none;" class="alert alert-success alert-dismissible fade show">
                        Package created successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>

                
                    <form id="packageForm" method="post" action="{{route('admin.addpackage')}}">
                        {{csrf_field()}}
                        <input type="hidden" name="selected_country_id" id="selected_country_id">
                        
                        <!-- Global Package Option -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_global" name="is_global">
                                <label class="form-check-label" for="is_global">
                                    <strong>Global Package</strong> - Available to all countries
                                </label>
                            </div>
                            <small class="text-muted">Enable this to create a package that works across all countries without country-specific pricing</small>
                        </div>
                        
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
                            <p class="text-muted small">Define pricing options for different durations</p>
                            <div id="tiersContainer">
                                <div class="row price-tier mb-3">
                                    <div class="col-md-5">
                                        <label class="form-label">Days</label>
                                        <input type="number" class="form-control" name="tiers[0][days]" placeholder="e.g., 10" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Price ($)</label>
                                        <input type="number" class="form-control" name="tiers[0][price]" placeholder="e.g., 30" required>
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
                            <textarea class="form-control" id="desc" name="description"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa fa-plus"></i> Create Package
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</div><!-- End packagesSection -->

<!-- Edit Package Modal -->
<div class="modal fade" id="editPackageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Edit Package for <span id="editCountryName" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editPackageForm">
                    <input type="hidden" id="edit_package_id">
                    <input type="hidden" id="edit_country_id">
                    
                    <!-- Global Package Option -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_is_global" name="is_global">
                            <label class="form-check-label" for="edit_is_global">
                                <strong>Global Package</strong> - Available to all countries
                            </label>
                        </div>
                        <small class="text-muted">Enable this to make package available across all countries</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Package Name</label>
                        <input type="text" class="form-control" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tag Line</label>
                        <input type="text" class="form-control" id="edit_tagline" required>
                    </div>
                    
                    <div id="editPriceTiers" class="mb-3">
                        <h6 class="mb-3">Price Tiers</h6>
                        <div id="editTiersContainer">
                            <!-- Tiers will be loaded here -->
                        </div>
                        <button type="button" class="btn btn-success btn-sm btn-add-edit-tier mt-2">
                            <i class="fa fa-plus"></i> Add Tier
                        </button>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-modal-btn" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="savePackageChanges">Save changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
var token = "{{ csrf_token() }}";
var countries = @json($countries);
var selectedCountryId = null;
var tierIndex = 1;

$(document).ready(function() {
    
    // Toggle collapse manually
    $('[data-bs-toggle="collapse"]').click(function() {
        const target = $(this).data('bs-target');
        $(target).collapse('toggle');
    });
    
    // Country Filter Change
    $('#countryFilter').change(function() {
        selectedCountryId = $(this).val();
        
        if (selectedCountryId) {
            const selectedCountryName = $(this).find('option:selected').text();
            $('#selectedCountryName').text(selectedCountryName);
            $('#packagesCountryName').text(selectedCountryName);
            $('#selected_country_id').val(selectedCountryId);
            $('#packagesSection').slideDown();
            
            // Load packages for this country
            loadPackagesForCountry(selectedCountryId);
        } else {
            $('#packagesSection').slideUp();
        }
    });
    
    // Load packages for selected country
    function loadPackagesForCountry(countryId) {
        $.get(`{{ url('admin/packages/by-country') }}/${countryId}`, function(data) {
            let tableHtml = '';
            
            if (data.packages && data.packages.length > 0) {
                tableHtml = `
                    <div class="table-responsive">
                        <table class="table table-bordered" id="packagesTable">
                            <thead>
                                <tr>
                                    <th>Package Name</th>
                                    <th>Tag Line</th>
                                    <th>Type</th>
                                    <th>Price Tiers</th>
                                    <th>Description</th>
                                    <th width="150">Action</th>
                                </tr>
                            </thead>
                            <tbody>`;
                
                data.packages.forEach(pkg => {
                    const isGlobal = pkg.is_global;
                    const countryPrice = pkg.country_prices ? pkg.country_prices.find(cp => cp.country_id == countryId) : null;
                    let tiersHtml = '';
                    
                    if (isGlobal) {
                        // For global packages, use price_tiers from package
                        const tiers = typeof pkg.price_tiers === 'string' ? JSON.parse(pkg.price_tiers) : pkg.price_tiers;
                        if (tiers && tiers.length) {
                            tiersHtml = tiers.map(t => `<div>${t.days} days - $${t.price}</div>`).join('');
                        }
                    } else if (countryPrice && countryPrice.price_tiers) {
                        const tiers = JSON.parse(countryPrice.price_tiers);
                        tiersHtml = tiers.map(t => `<div>${t.days} days - $${t.price}</div>`).join('');
                    }
                    
                    const packageType = isGlobal 
                        ? '<span class="badge bg-success">Global</span>' 
                        : '<span class="badge bg-primary">Country-Specific</span>';
                    
                    tableHtml += `
                        <tr id="row${pkg.id}">
                            <td>${pkg.name}</td>
                            <td>${pkg.tagline}</td>
                            <td>${packageType}</td>
                            <td>${tiersHtml || 'No pricing'}</td>
                            <td>${pkg.description || ''}</td>
                            <td>
                                <button class="btn btn-info btn-sm editPackage" data-id="${pkg.id}" data-is-global="${isGlobal}" data-country-price-id="${countryPrice ? countryPrice.id : ''}">
                                    <i class="fa fa-pen"></i> Edit
                                </button>
                                <button class="btn btn-danger btn-sm deletePackage" data-id="${pkg.id}" data-country-id="${countryId}">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    `;
                });
                
                tableHtml += `
                            </tbody>
                        </table>
                    </div>`;
            } else {
                tableHtml = '<div class="alert alert-info"><i class="fa fa-info-circle"></i> No packages found for this country. Click the header above to create your first package.</div>';
            }
            
            $('#packagesTableContainer').html(tableHtml);
        });
    }
    
    // Add Tier (Create Form)
    $(document).on('click', '.btn-add-tier', function() {
        const newTier = `
            <div class="row price-tier mb-3">
                <div class="col-md-5">
                    <input type="number" class="form-control" name="tiers[${tierIndex}][days]" placeholder="e.g., 20" required>
                </div>
                <div class="col-md-5">
                    <input type="number" class="form-control" name="tiers[${tierIndex}][price]" placeholder="e.g., 60" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger w-100 btn-remove-tier">-</button>
                </div>
            </div>
        `;
        $('#tiersContainer').append(newTier);
        tierIndex++;
    });
    
    // Remove Tier
    $(document).on('click', '.btn-remove-tier', function() {
        if ($('.price-tier').length > 1) {
            $(this).closest('.price-tier').remove();
        } else {
            alert('At least one price tier is required');
        }
    });
    
    // Submit Package Form
    $('#packageForm').submit(function(e) {
        e.preventDefault();
        
        const isGlobal = $('#is_global').is(':checked');
        
        if (!isGlobal && !selectedCountryId) {
            alert('Please select a country first or enable Global Package');
            return;
        }
        
        let tiers = [];
        $('#tiersContainer .price-tier').each(function() {
            const days = $(this).find('input[name*="[days]"]').val();
            const price = $(this).find('input[name*="[price]"]').val();
            if (days && price) {
                tiers.push({ days: parseInt(days), price: parseFloat(price) });
            }
        });
        
        const formData = {
            _token: token,
            name: $('input[name="name"]').val(),
            tagline: $('input[name="tagline"]').val(),
            description: $('#desc').val(),
            is_global: isGlobal ? 1 : 0
        };
        
        if (isGlobal) {
            // For global packages, send tiers directly
            formData.tiers = tiers;
        } else {
            // For country-specific packages
            formData.country_prices = [{
                country_id: selectedCountryId,
                tiers: tiers
            }];
        }
        
        // Debug: Log form data
        console.log('Submitting form data:', formData);
        
        $.ajax({
            url: '{{ route("admin.addpackage") }}',
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(formData),
            headers: {
                'X-CSRF-TOKEN': token
            },
            success: function(response) {
            if (response.success) {
                $('#successMessage').slideDown();
                $('#packageForm')[0].reset();
                $('#is_global').prop('checked', false);
                tierIndex = 1;
                $('#tiersContainer').html(`
                    <div class="row price-tier mb-3">
                        <div class="col-md-5">
                            <label class="form-label">Days</label>
                            <input type="number" class="form-control" name="tiers[0][days]" placeholder="e.g., 10" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Price ($)</label>
                            <input type="number" class="form-control" name="tiers[0][price]" placeholder="e.g., 30" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-success w-100 btn-add-tier">+</button>
                        </div>
                    </div>
                `);
                
                // Reload packages
                loadPackagesForCountry(selectedCountryId);
                
                setTimeout(() => {
                    $('#successMessage').slideUp();
                }, 3000);
            }
        },
        error: function(xhr) {
            alert('Error creating package: ' + (xhr.responseJSON?.message || 'Unknown error'));
        }
        });
    });
    
    // Edit Package
    $(document).on('click', '.editPackage', function() {
        const packageId = $(this).data('id');
        const isGlobal = $(this).data('is-global');
        const countryName = $('#countryFilter option:selected').text();
        
        $('#editCountryName').text(countryName);
        $('#edit_country_id').val(selectedCountryId);
        
        $.get(`{{ url('admin/package') }}/${packageId}`, function(pkg) {
            $('#edit_package_id').val(pkg.id);
            $('#edit_name').val(pkg.name);
            $('#edit_tagline').val(pkg.tagline);
            $('#edit_description').val(pkg.description);
            $('#edit_is_global').prop('checked', pkg.is_global);
            
            let tiers = [];
            
            if (pkg.is_global) {
                // For global packages, use price_tiers from package
                tiers = typeof pkg.price_tiers === 'string' ? JSON.parse(pkg.price_tiers) : pkg.price_tiers;
            } else {
                // Load tiers for selected country
                const countryPrice = (pkg.country_prices || pkg.countryPrices || []).find(cp => cp.country_id == selectedCountryId);
                if (countryPrice && countryPrice.price_tiers) {
                    tiers = JSON.parse(countryPrice.price_tiers);
                }
            }
            
            $('#editTiersContainer').html('');
            
            if (tiers && tiers.length) {
                tiers.forEach((tier, index) => {
                    const tierHtml = `
                        <div class="row price-tier mb-2">
                            <div class="col-md-5">
                                <input type="number" class="form-control" value="${tier.days}" placeholder="Days" required>
                            </div>
                            <div class="col-md-5">
                                <input type="number" class="form-control" value="${tier.price}" placeholder="Price" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger w-100 btn-remove-edit-tier">-</button>
                            </div>
                        </div>
                    `;
                    $('#editTiersContainer').append(tierHtml);
                });
            }
            
            $('#editPackageModal').modal('show');
        });
    });
    
    // Add Tier in Edit Modal
    $(document).on('click', '.btn-add-edit-tier', function() {
        const newTier = `
            <div class="row price-tier mb-2">
                <div class="col-md-5">
                    <input type="number" class="form-control" placeholder="Days" required>
                </div>
                <div class="col-md-5">
                    <input type="number" class="form-control" placeholder="Price" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger w-100 btn-remove-edit-tier">-</button>
                </div>
            </div>
        `;
        $('#editTiersContainer').append(newTier);
    });
    
    // Remove Edit Tier
    $(document).on('click', '.btn-remove-edit-tier', function() {
        if ($('#editTiersContainer .price-tier').length > 1) {
            $(this).closest('.price-tier').remove();
        } else {
            alert('At least one price tier is required');
        }
    });
    
    // Save Package Changes
    $('#savePackageChanges').click(function() {
        const $btn = $(this);
        const originalText = $btn.html();
        const isGlobal = $('#edit_is_global').is(':checked');
        
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        
        let tiers = [];
        $('#editTiersContainer .price-tier').each(function() {
            const days = $(this).find('input').eq(0).val();
            const price = $(this).find('input').eq(1).val();
            if (days && price) {
                tiers.push({ days: parseInt(days), price: parseFloat(price) });
            }
        });
        
        // Debug: Log collected tiers
        console.log('Collected tiers:', tiers);
        console.log('Is Global:', isGlobal);
        console.log('Selected Country ID:', selectedCountryId);
        
        const formData = {
            _token: token,
            rid: $('#edit_package_id').val(),
            name: $('#edit_name').val(),
            tagline: $('#edit_tagline').val(),
            description: $('#edit_description').val(),
            is_global: isGlobal ? 1 : 0
        };
        
        if (isGlobal) {
            // For global packages, send tiers directly
            formData.tiers = tiers;
        } else {
            // For country-specific packages
            formData.country_prices = [{
                country_id: selectedCountryId,
                tiers: tiers
            }];
        }
        
        // Debug: Log form data
        console.log('Updating form data:', formData);
        
        $.ajax({
            url: "{{ route('admin.updatepackage') }}",
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(formData),
            headers: {
                'X-CSRF-TOKEN': token
            }
        })
        .done(function(response) {
            if (response.success) {
                $('#editPackageModal .modal-body').prepend(`
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fa fa-check-circle"></i> Package updated successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `);
                
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        })
        .fail(function(xhr) {
            $btn.prop('disabled', false).html(originalText);
            
            let errorMsg = 'An error occurred while saving.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            
            $('#editPackageModal .modal-body').prepend(`
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fa fa-exclamation-triangle"></i> ${errorMsg}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
        });
    });
    
    // Delete Package
    $(document).on('click', '.deletePackage', function() {
        if (confirm('Are you sure you want to delete this package?')) {
            const packageId = $(this).data('id');
            
            $.post("{{ route('admin.delpackage') }}", {
                _token: token,
                rid: packageId
            }, function(response) {
                if (response == 'success') {
                    $(`#row${packageId}`).fadeOut(function() {
                        $(this).remove();
                    });
                }
            });
        }
    });
    
    // Collapse toggle animation
    $('#createPackageCollapse').on('shown.bs.collapse', function() {
        $(this).prev('.card-header').find('.fa-chevron-down').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    }).on('hidden.bs.collapse', function() {
        $(this).prev('.card-header').find('.fa-chevron-up').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });
    
    // Modal close handler
    $('.close-modal-btn').click(function() {
        $('#editPackageModal').modal('hide');
    });
    
});
</script>

<script src="https://cdn.tiny.cloud/1/3arl7kd7bi1emf429o89drj6b16cmrwsmvdfwmidj6z90k59/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<script>
  tinymce.init({
    selector: 'textarea#desc, textarea#edit_description',
    plugins: 'code table lists image',
    toolbar: 'undo redo | image | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table'
  });
</script>
@endpush