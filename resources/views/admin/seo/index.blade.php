@extends('admin.layout.master')

@section('content')

 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">SEO Management</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage SEO content for categories, cities, and countries</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">SEO</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>

<div class="row mt-3 mb-3">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0 float-left" style="float:left">SEO Content Management</h5>
                <a href="{{ route('seo.create') }}" style="float:right" class="btn btn-primary float-right">Add New SEO Content</a>
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
                    <strong>Need default SEO fallbacks?</strong> Set up <a href="{{ route('default-seo.index') }}" class="alert-link">Default SEO Settings</a> to ensure your site always has appropriate SEO content when no specific matches are found.
                </div>

                <!-- Filter Section -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label for="gender_filter">Gender</label>
                        <select id="gender_filter" class="form-control">
                            <option value="">All Genders</option>
                            @foreach(\App\Models\Gender::all() as $gender)
                                <option value="{{ $gender->id }}">{{ ucfirst($gender->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="city_filter_search">City</label>
                        <div class="position-relative">
                            <input type="text" id="city_filter_search" class="form-control" placeholder="Search for a city..." autocomplete="off">
                            <input type="hidden" id="city_filter" value="">
                            <div id="city_filter_results" class="dropdown-menu" style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 1000; max-height: 200px; overflow-y: auto;"></div>
                        </div>
                        <small id="selected_filter_city" class="text-muted">All cities</small>
                    </div>
                    <div class="col-md-3">
                        <label for="country_filter">Country</label>
                        <select id="country_filter" class="form-control">
                            <option value="">All Countries</option>
                            @foreach(\App\Models\Country::all() as $country)
                                <option value="{{ $country->id }}">{{ $country->nicename }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button id="filter_btn" class="btn btn-info flex-fill">Filter</button>
                            <button id="reset_filter_btn" class="btn btn-secondary flex-fill">Reset</button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="seo_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Gender</th>
                                <th>City</th>
                                <th>Country</th>
                                <th>Page</th>
                                <th>Title</th>
                                <th>Keywords</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($seoKeywords as $seo)
                            <tr data-gender="{{ $seo->gender_id }}" data-city="{{ $seo->city_id }}" data-country="{{ $seo->country_id }}">
                                <td>{{ $seo->gender ? ucfirst($seo->gender->name) : '-' }}</td>
                                <td>{{ $seo->city ? $seo->city->name : '-' }}</td>
                                <td>{{ $seo->country ? $seo->country->nicename : '-' }}</td>
                                <td>{{ $seo->page ?? '-' }}</td>
                                <td>{{ Str::limit($seo->title, 50) }}</td>
                                <td>{{ Str::limit($seo->keywords, 30) }}</td>
                                <td>{{ Str::limit($seo->description, 50) }}</td>
                                <td>
                                    <a href="{{ route('seo.edit', $seo->id) }}" class="btn btn-info btn-sm">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </a>
                                    <form action="{{ route('seo.destroy', $seo->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                            <i class="fa-solid fa-trash-can"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
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
    let searchTimeout;
    
    // Function to search cities
    function searchCities(query) {
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            $('#city_filter_results').hide();
            return;
        }
        
        searchTimeout = setTimeout(function() {
            $.ajax({
                url: '{{ route("admin.cities.search") }}',
                type: 'POST',
                data: {
                    search: query,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    // Clear previous results
                    $('#city_filter_results').empty();
                    
                    if (data.length === 0) {
                        $('#city_filter_results').append('<div class="dropdown-item">No cities found</div>');
                    } else {
                        // Add "All Cities" option
                        const allItem = $('<div class="dropdown-item city-filter-item"></div>')
                            .text('All Cities')
                            .data('id', '')
                            .data('name', 'All Cities');
                        $('#city_filter_results').append(allItem);
                        
                        // Add each city to the results
                        $.each(data, function(index, city) {
                            const cityName = city.name + (city.country ? ` (${city.country})` : '');
                            const item = $('<div class="dropdown-item city-filter-item"></div>')
                                .text(cityName)
                                .data('id', city.id)
                                .data('name', city.name);
                            
                            $('#city_filter_results').append(item);
                        });
                    }
                    
                    // Show the results dropdown
                    $('#city_filter_results').show();
                },
                error: function() {
                    $('#city_filter_results').html('<div class="dropdown-item text-danger">Error loading cities</div>').show();
                }
            });
        }, 300);
    }
    
    // Event listener for city search input
    $('#city_filter_search').on('input', function() {
        const query = $(this).val().trim();
        if (query === '') {
            $('#city_filter').val('');
            $('#selected_filter_city').text('All cities').removeClass('text-success').addClass('text-muted');
            $('#city_filter_results').hide();
        } else {
            searchCities(query);
        }
    });
    
    // Event listener for clicking on a city item
    $(document).on('click', '.city-filter-item', function() {
        const cityId = $(this).data('id');
        const cityName = $(this).data('name');
        
        $('#city_filter').val(cityId);
        $('#city_filter_search').val(cityName === 'All Cities' ? '' : cityName);
        $('#selected_filter_city').text(cityName).removeClass('text-muted').addClass('text-success');
        $('#city_filter_results').hide();
    });
    
    // Event listener for clicking outside the dropdown
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.position-relative').length) {
            $('#city_filter_results').hide();
        }
    });
    
    // Filter functionality
    $('#filter_btn').click(function() {
        const genderFilter = $('#gender_filter').val();
        const cityFilter = $('#city_filter').val();
        const countryFilter = $('#country_filter').val();
        
        let visibleCount = 0;
        
        $('#seo_table tbody tr').each(function() {
            const row = $(this);
            const rowGender = row.data('gender') || '';
            const rowCity = row.data('city') || '';
            const rowCountry = row.data('country') || '';
            
            let showRow = true;
            
            if (genderFilter && genderFilter != rowGender) {
                showRow = false;
            }
            
            if (cityFilter && cityFilter != rowCity) {
                showRow = false;
            }
            
            if (countryFilter && countryFilter != rowCountry) {
                showRow = false;
            }
            
            if (showRow) {
                row.show();
                visibleCount++;
            } else {
                row.hide();
            }
        });
        
        // Show filter results count
        if (genderFilter || cityFilter || countryFilter) {
            if (visibleCount === 0) {
                if (!$('#no-results-message').length) {
                    $('#seo_table tbody').append('<tr id="no-results-message"><td colspan="8" class="text-center text-muted">No SEO entries match the selected filters</td></tr>');
                }
            } else {
                $('#no-results-message').remove();
            }
        } else {
            $('#no-results-message').remove();
        }
    });
    
    // Reset filter functionality
    $('#reset_filter_btn').click(function() {
        $('#gender_filter').val('');
        $('#city_filter').val('');
        $('#city_filter_search').val('');
        $('#country_filter').val('');
        $('#selected_filter_city').text('All cities').removeClass('text-success').addClass('text-muted');
        $('#seo_table tbody tr').show();
        $('#no-results-message').remove();
    });
    
    // Auto reset when all filters are cleared
    $('#gender_filter, #country_filter').change(function() {
        if (!$('#gender_filter').val() && !$('#city_filter').val() && !$('#country_filter').val()) {
            $('#seo_table tbody tr').show();
            $('#no-results-message').remove();
        }
    });
});
</script>
@endpush