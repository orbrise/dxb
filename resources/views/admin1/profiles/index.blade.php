@extends("admin.layout.master")

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    .w-5{
        display:none
    }

     .navbar-filters {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px 20px;
        margin-bottom: 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        position: relative;
        width: 100%;
        z-index: 6;
    }

    .navbar-filters ul.nav {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;
        margin: 0;
        padding: 0;
        list-style: none;
        justify-content: flex-start;
    }

    .navbar-filters .filter-icon {
        background: #6c757d;
        border-radius: 8px;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
    }

    .navbar-filters .dropdown {
        position: relative;
        width:130px;
    }

    .navbar-filters .dropdown-toggle {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 8px 16px;
        color: #495057;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .navbar-filters .dropdown-toggle:hover {
        background: #f8f9fa;
        border-color: #adb5bd;
        color: #495057;
        text-decoration: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .navbar-filters .dropdown-toggle::after {
        border-top-color: #6c757d;
        margin-left: 8px;
    }

    .navbar-filters .dropdown-toggle i {
        color: #6c757d;
        font-size: 12px;
    }

    .navbar-filters .dropdown-menu {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        padding: 15px;
        min-width: 250px;
        margin-top: 5px;
        position: absolute;
        z-index: 1000;
    }

    .backpack-filter {
        padding: 0;
    }

    .backpack-filter .input-group {
        border-radius: 6px;
        overflow: hidden;
    }

    .backpack-filter .form-control {
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 14px;
        transition: all 0.2s ease;
        color: #495057;
    }

    .backpack-filter .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .backpack-filter .input-group-text {
        background: #6c757d;
        border: 1px solid #6c757d;
        border-left: none;
    }

    .backpack-filter .input-group-text button {
        color: white;
        border: none;
        background: none;
    }

    .date-filter-container {
        padding: 0;
    }

    .date-filter-container .form-control {
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 8px 12px;
        margin-bottom: 10px;
        font-size: 14px;
        transition: all 0.2s ease;
        color: #495057;
    }

    .date-filter-container .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .date-filter-container .btn-primary {
        background: #007bff;
        border: 1px solid #007bff;
        border-radius: 6px;
        padding: 6px 16px;
        font-size: 13px;
        font-weight: 500;
        color: white;
    }

    .select-filter select {
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 14px;
        background: white;
        transition: all 0.2s ease;
        width: 100%;
        color: #495057;
    }

    .select-filter select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        outline: none;
    }

    .reset-filter-btn {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 8px 16px;
        color: #dc3545;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .reset-filter-btn:hover {
        background: #e9ecef;
        border-color: #adb5bd;
        color: #dc3545;
        text-decoration: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 6px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
        color: #495057;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 8px;
    }

    .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .navbar-filters {
            padding: 10px 15px;
        }
        
        .navbar-filters ul.nav {
            flex-direction: column;
            gap: 10px;
            align-items: stretch;
        }
        
        .navbar-filters .dropdown-toggle {
            width: 100%;
            justify-content: center;
        }
        
        .navbar-filters .dropdown-menu {
            min-width: 100%;
            position: static !important;
            transform: none !important;
            margin-top: 10px;
        }
        
        .navbar-filters .filter-icon {
            align-self: center;
        }
    }

    /* Pagination Info Styling */
    .dataTables_info {
        color: #6c757d;
        font-size: 16px;
        font-weight: 500;
        margin: 0;
        padding: 6px 0;
    }

    @media (max-width: 768px) {
        .dataTables_info {
            text-align: center;
            font-size: 13px;
        }
    }

    /* Bulk Delete Styling */
    .clickable-cell {
        cursor: pointer;
    }

    #bulk-actions {
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .table tbody tr.selected {
        background-color: #e3f2fd;
    }

    .profile-checkbox {
        cursor: pointer;
    }

    #select-all {
        cursor: pointer;
    }

    .form-check-input {
    margin-left: 0.25rem;
    }

    .img-thumbnail {
    border: 0px solid #ddd;
    }

    /* Per Page Dropdown Styling */
    .dataTables_length {
        color: #6c757d;
        font-size: 14px;
        font-weight: 500;
    }

    .dataTables_length label {
        margin: 0;
        font-weight: normal;
    }

    .dataTables_length .form-select {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 4px 8px;
        font-size: 14px;
        color: #495057;
        background-color: white;
        cursor: pointer;
    }

    .dataTables_length .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        outline: none;
    }

    @media (max-width: 768px) {
        .dataTables_length {
            text-align: center;
            margin-top: 10px;
        }
    }


</style>
@endpush

@section("content")
 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Profiles</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage profiles effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Profiles</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>

<div class="row mt-3 mb-3">
<div class="col-md-12">
<div class="card">
<div class="card-body">
<nav class="navbar navbar-expand-lg navbar-filters">
    <form id="filter-form" action="{{ route('admin.profiles.index') }}" method="GET">
        <ul class="nav">
            <!-- Filter Icon -->
            <li class="nav-item">
                <div class="filter-icon">
                    <i class="fas fa-filter"></i>
                </div>
            </li>

            <!-- ID Filter -->
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-hashtag"></i> ID/Reference
                </a>
                <div class="dropdown-menu">
                    <div class="backpack-filter">
                        <div class="input-group">
                            <input type="text" name="id" id="text-filter-id" class="form-control" value="{{ request('id') }}" placeholder="Enter ID">
                            {{-- <span class="input-group-text">
                                <button type="submit" class="btn btn-link p-0">
                                    <i class="fas fa-search"></i>
                                </button>
                            </span> --}}
                        </div>
                    </div>
                </div>
            </li>

            <!-- Title Filter -->
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-heading"></i> Title
                </a>
                <div class="dropdown-menu">
                    <div class="backpack-filter">
                        <div class="input-group">
                            <input type="text" name="title" class="form-control" value="{{ request('title') }}" placeholder="Enter title">
                            {{-- <span class="input-group-text">
                                <button type="submit" class="btn btn-link p-0">
                                    <i class="fas fa-search"></i>
                                </button>
                            </span> --}}
                        </div>
                    </div>
                </div>
            </li>

            <!-- City Filter -->
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-map-marker-alt"></i> City
                </a>
                <div class="dropdown-menu">
                    <div class="select-filter">
                        <select name="city" class="js-example-basic-single">
                            <option value="">All Cities</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ request('city') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </li>

            <!-- Status Filter -->
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-toggle-on"></i> Status
                </a>
                <div class="dropdown-menu">
                    <div class="select-filter">
                        <select name="status">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </li>

            <!-- Premium Filter -->
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-crown"></i> Package
                </a>
                <div class="dropdown-menu">
                    <div class="select-filter">
                        <select name="premium">
                            <option value="">All Packages</option>
                            <option value="19" {{ request('premium') == '19' ? 'selected' : '' }}>Basic</option>
                            <option value="20" {{ request('premium') == '20' ? 'selected' : '' }}>Standard</option>
                            <option value="21" {{ request('premium') == '21' ? 'selected' : '' }}>Premium</option>
                        </select>
                    </div>
                </div>
            </li>

            <!-- Date Range Filter -->
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-calendar-alt"></i> Date Range
                </a>
                <div class="dropdown-menu">
                    <div class="date-filter-container">
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Start Date">
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="End Date">
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                        </div>
                    </div>
                </div>
            </li>

            <!-- Reset Filters -->
            <li class="nav-item">
                <a href="{{ route('admin.profiles.index') }}" class="reset-filter-btn">
                    <i class="fas fa-eraser"></i> Reset
                </a>
            </li>
        </ul>
    </form>
</nav>
</div>
</div>
</div>


    <div class="col-lg-12">
        <div class="card-body">
            
        </div>
<div class="col-md-12">
<div class="row mb-3">
<div class="col-md-6">
    <div class="row">
        <div class="col-sm-12 col-md-12">
           
        </div>
        <div class="col-sm-12 col-md-12 text-end">
            <div class="dataTables_length">
                <label class="d-inline-flex align-items-center">
                    Show 
                    <select name="per_page" id="per-page-select" class="form-select form-select-sm mx-2" style="width: auto;">
                        <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                        <option value="250" {{ request('per_page', 50) == 250 ? 'selected' : '' }}>250</option>
                        <option value="500" {{ request('per_page', 50) == 500 ? 'selected' : '' }}>500</option>
                        <option value="1000" {{ request('per_page', 50) == 1000 ? 'selected' : '' }}>1000</option>
                    </select>
                    entries
                </label>
            </div>
        </div>
    </div>
    </div>
    
    <div class="col-md-6" style="text-align: right;">
                <a href="{{ route('admin.verifications') }}" class="btn btn-primary">Pending Profile Photo Verify</a>
                </div>
    </div>
        <div class="card row">
            <div class="card-header">
                <div class="float-start mb-2" >
                    <div class="d-flex">
                        <div style="margin-right:13px">
                            <h5 class="card-title mt-2">All Profiles</h5>
                        </div>
                        <div class="">
                             <div class="dataTables_info" id="entries-info">
                @if($profiles->total() > 0)
                    Showing {{ $profiles->firstItem() }} to {{ $profiles->lastItem() }} of {{ number_format($profiles->total()) }} entries
                @else
                    Showing 0 to 0 of 0 entries
                @endif
            </div>
                        </div>
                    </div>
                
                </div>
                
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{session('success')}}</div>
                @endif
                
                <div id="table-container">
                    @include('admin.profiles.table')
                </div>



            </div>
        </div>
    </div>
</div>
@endsection

@push('js')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        function updateURL(params) {
            const url = new URL(window.location);
            Object.entries(params).forEach(([key, value]) => {
                if (value) {
                    url.searchParams.set(key, value);
                } else {
                    url.searchParams.delete(key);
                }
            });
            window.history.pushState({}, '', url);
        }
    
        function fetchProfiles(url) {
            const filterData = {
                id: $('#text-filter-id').val(),
                title: $('input[name="title"]').val(),
                city: $('select[name="city"]').val(),
                status: $('select[name="status"]').val(),
                premium: $('select[name="premium"]').val(),
                start_date: $('input[name="start_date"]').val(),
                end_date: $('input[name="end_date"]').val(),
                per_page: $('#per-page-select').val()
            };

            $.ajax({
                url: url || "{{ route('admin.profiles.index') }}",
                type: 'GET',
                data: filterData,
                success: function(response) {
                    $('#table-container').html(response);
                    updateURL(filterData);
                },
                error: function(xhr, status, error) {
                    console.error('Filter error:', error);
                }
            });
        }
    
        // Initialize Select2 with modern styling
        $('.js-example-basic-single').select2({
            width: '100%',
            allowClear: true,
            placeholder: "Select a city...",
            dropdownParent: $('.js-example-basic-single').closest('.dropdown-menu')
        });

        // ID Filter
        $('#text-filter-id').closest('.dropdown-menu').find('button[type="submit"]').on('click', function(e) {
            e.preventDefault();
            fetchProfiles();
            $(this).closest('.dropdown-menu').removeClass('show');
        });
    
        $('#text-filter-id').on('keypress', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                fetchProfiles();
                $(this).closest('.dropdown-menu').removeClass('show');
            }
        });
    
        // Title Filter with debounce
        let titleTimeout = null;
        $('input[name="title"]').on('keyup', function() {
            clearTimeout(titleTimeout);
            titleTimeout = setTimeout(() => {
                fetchProfiles();
                $(this).closest('.dropdown-menu').removeClass('show');
            }, 500);
        });

        // Title Filter search button
        $('input[name="title"]').closest('.dropdown-menu').find('button[type="submit"]').on('click', function(e) {
            e.preventDefault();
            fetchProfiles();
            $(this).closest('.dropdown-menu').removeClass('show');
        });

        // Title Filter Enter key
        $('input[name="title"]').on('keypress', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                fetchProfiles();
                $(this).closest('.dropdown-menu').removeClass('show');
            }
        });
    
        // Select Filters
        $('select[name="city"], select[name="status"], select[name="premium"]').on('change', function() {
            fetchProfiles();
            $(this).closest('.dropdown-menu').removeClass('show');
        });

        // Per Page Dropdown
        $('#per-page-select').on('change', function() {
            fetchProfiles();
        });
    
        // Date Range Filters
        $('input[name="start_date"], input[name="end_date"]').on('change', function() {
            fetchProfiles();
        });

        // Date Range Apply Button
        $('.date-filter-container .btn-primary').on('click', function(e) {
            e.preventDefault();
            fetchProfiles();
            $(this).closest('.dropdown-menu').removeClass('show');
        });
    
        // Pagination
        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            fetchProfiles($(this).attr('href'));
        });
    
        // Reset All Filters
        $('.reset-filter-btn').on('click', function(e) {
            e.preventDefault();
            $('#filter-form')[0].reset();
            $('.js-example-basic-single').val(null).trigger('change');
            $('#per-page-select').val('50').trigger('change');
            window.history.pushState({}, '', "{{ route('admin.profiles.index') }}");
            fetchProfiles();
        });

        // Enhanced dropdown behavior
        $('.dropdown-toggle').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Close other dropdowns
            $('.dropdown-menu').not($(this).next()).removeClass('show');
            
            // Toggle current dropdown
            $(this).next('.dropdown-menu').toggleClass('show');
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown-menu').removeClass('show');
            }
        });

        // Prevent dropdown from closing when clicking inside
        $('.dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });

        // Bulk Delete Functionality
        function updateBulkActions() {
            const checkedBoxes = $('.profile-checkbox:checked');
            const count = checkedBoxes.length;
            
            if (count > 0) {
                $('#bulk-actions').show();
                $('#selected-count').text(count + ' selected');
            } else {
                $('#bulk-actions').hide();
            }
            
            // Update select all checkbox state
            const totalBoxes = $('.profile-checkbox').length;
            const selectAllCheckbox = $('#select-all');
            
            if (count === 0) {
                selectAllCheckbox.prop('indeterminate', false);
                selectAllCheckbox.prop('checked', false);
            } else if (count === totalBoxes) {
                selectAllCheckbox.prop('indeterminate', false);
                selectAllCheckbox.prop('checked', true);
            } else {
                selectAllCheckbox.prop('indeterminate', true);
                selectAllCheckbox.prop('checked', false);
            }
        }

        // Select All functionality
        $(document).on('change', '#select-all', function() {
            const isChecked = $(this).is(':checked');
            $('.profile-checkbox').prop('checked', isChecked);
            $('.profile-checkbox').closest('tr').toggleClass('selected', isChecked);
            updateBulkActions();
        });

        // Individual checkbox functionality
        $(document).on('change', '.profile-checkbox', function() {
            $(this).closest('tr').toggleClass('selected', $(this).is(':checked'));
            updateBulkActions();
        });

        // Row click functionality (excluding checkboxes and action buttons)
        $(document).on('click', '.clickable-cell', function() {
            const row = $(this).closest('tr');
            const profileId = row.data('profile-id');
            const profile = @json($profiles->items());
            const profileData = profile.find(p => p.id == profileId);
            
            if (profileData && profileData.ggender && profileData.gcity) {
                const url = `/${profileData.ggender.name.toLowerCase()}-escorts-in-${profileData.gcity.name.toLowerCase()}/${profileData.id}/${profileData.slug}`;
                window.open(url, '_blank');
            }
        });

        // Bulk Delete functionality
        $(document).on('click', '#bulk-delete-btn', function() {
            const checkedBoxes = $('.profile-checkbox:checked');
            const profileIds = [];
            
            checkedBoxes.each(function() {
                profileIds.push($(this).val());
            });
            
            if (profileIds.length === 0) {
                alert('Please select profiles to delete');
                return;
            }
            
            if (confirm(`Are you sure you want to delete ${profileIds.length} selected profile(s)? This action cannot be undone.`)) {
                // Create a form and submit it
                const form = $('<form>', {
                    method: 'POST',
                    action: '{{ route("admin.profiles.bulk-delete") }}'
                });
                
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: '{{ csrf_token() }}'
                }));
                
                form.append($('<input>', {
                    type: 'hidden',
                    name: '_method',
                    value: 'DELETE'
                }));
                
                profileIds.forEach(function(id) {
                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'profile_ids[]',
                        value: id
                    }));
                });
                
                $('body').append(form);
                form.submit();
            }
        });

        // Reset bulk selection when filters are applied
        function resetBulkSelection() {
            $('#select-all').prop('checked', false);
            $('#select-all').prop('indeterminate', false);
            $('#bulk-actions').hide();
        }

        // Update fetchProfiles to reset bulk selection
        const originalFetchProfiles = fetchProfiles;
        fetchProfiles = function(url) {
            originalFetchProfiles.call(this, url);
            resetBulkSelection();
        };
    });
</script>
@endpush