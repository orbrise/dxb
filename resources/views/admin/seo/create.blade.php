@extends('admin.layout.master')

@section('content')
 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">SEO Management</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Create SEO content for specific combinations</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('seo.index') }}">SEO</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>

<div class="row mt-3 mb-3">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title mb-0 float-left" style="float:left">Create New SEO Content</h5>
                <a href="{{ route('seo.index') }}" style="float:right" class="btn btn-secondary float-right">Back to List</a>
            </div><!-- end card header -->

            <div class="card-body">

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

                <form action="{{ route('seo.store') }}" method="POST">
                    @csrf
                    
                    <!-- Selection Criteria -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="gender_id" class="form-label">Gender <small class="text-muted">(Optional)</small></label>
                                <select name="gender_id" id="gender_id" class="form-control">
                                    <option value="">Select Gender</option>
                                    @foreach($genders as $gender)
                                        <option value="{{ $gender->id }}" {{ old('gender_id') == $gender->id ? 'selected' : '' }}>
                                            {{ $gender->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="city_search" class="form-label">City <small class="text-muted">(Optional)</small></label>
                                <div class="position-relative">
                                    <input type="text" id="city_search" class="form-control" placeholder="Search for a city..." autocomplete="off">
                                    <input type="hidden" name="city_id" id="city_id" value="{{ old('city_id') }}">
                                    <div id="city_results" class="dropdown-menu" style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 1000; max-height: 200px; overflow-y: auto;"></div>
                                </div>
                                <small id="selected_city_name" class="text-muted">No city selected</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="country_id" class="form-label">Country <small class="text-muted">(Optional)</small></label>
                                <select name="country_id" id="country_id" class="form-control">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                            {{ $country->nicename }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Legacy Page Field -->
                    <div class="mb-3">
                        <label for="page" class="form-label">Page <small class="text-muted">(Optional - for legacy page-based SEO)</small></label>
                        <input type="text" name="page" id="page" class="form-control" value="{{ old('page') }}" placeholder="e.g., login, register">
                        <small class="text-muted">Use this for specific pages that don't fit the category/city/country model</small>
                    </div>

                    <!-- SEO Content -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="keywords" class="form-label">Keywords</label>
                        <textarea name="keywords" id="keywords" class="form-control" rows="3" placeholder="Comma-separated keywords">{{ old('keywords') }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Meta Description</label>
                        <textarea name="description" id="description" class="form-control" rows="3" maxlength="250" placeholder="SEO meta description (max 250 characters)">{{ old('description') }}</textarea>
                        <small class="text-muted">Characters: <span id="desc_count">0</span>/250</small>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea name="content" id="content" class="form-control" rows="8" placeholder="Additional SEO content for the page">{{ old('content') }}</textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Save SEO Content</button>
                        <a href="{{ route('seo.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
$(document).ready(function() {
    let searchTimeout;
    
    // Character count for description
    $('#description').on('input', function() {
        const count = $(this).val().length;
        $('#desc_count').text(count);
        
        if (count > 250) {
            $('#desc_count').addClass('text-danger');
        } else {
            $('#desc_count').removeClass('text-danger');
        }
    });
    
    // Trigger character count on page load
    $('#description').trigger('input');
    
    // Function to search cities
    function searchCities(query) {
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            $('#city_results').hide();
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
                    $('#city_results').empty();
                    
                    if (data.length === 0) {
                        $('#city_results').append('<div class="dropdown-item">No cities found</div>');
                    } else {
                        // Add each city to the results
                        $.each(data, function(index, city) {
                            const cityName = city.name + (city.country ? ` (${city.country})` : '');
                            const item = $('<div class="dropdown-item city-item"></div>')
                                .text(cityName)
                                .data('id', city.id)
                                .data('name', city.name);
                            
                            $('#city_results').append(item);
                        });
                    }
                    
                    // Show the results dropdown
                    $('#city_results').show();
                },
                error: function() {
                    $('#city_results').html('<div class="dropdown-item text-danger">Error loading cities</div>').show();
                }
            });
        }, 300);
    }
    
    // Event listener for input changes
    $('#city_search').on('input', function() {
        searchCities($(this).val().trim());
    });
    
    // Event listener for clicking on a city item
    $(document).on('click', '.city-item', function() {
        const cityId = $(this).data('id');
        const cityName = $(this).data('name');
        
        $('#city_id').val(cityId);
        $('#city_search').val(cityName);
        $('#selected_city_name').text('Selected: ' + cityName).removeClass('text-muted').addClass('text-success');
        $('#city_results').hide();
    });
    
    // Event listener for clicking outside the dropdown
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.position-relative').length) {
            $('#city_results').hide();
        }
    });
});
</script>
@endpush