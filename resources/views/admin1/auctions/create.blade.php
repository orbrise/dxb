@extends("admin.layout.master")

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Auction Spot</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.auctions.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    
                    <form action="{{ route('admin.auctions.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="spot_number">Spot Number</label>
                            <select name="spot_number" id="spot_number" class="form-control @error('spot_number') is-invalid @enderror" required>
                                <option value="">Select Spot Number</option>
                                @for($i = 1; $i <= 6; $i++)
                                    <option value="{{ $i }}" {{ old('spot_number') == $i ? 'selected' : '' }}>Spot #{{ $i }}</option>
                                @endfor
                            </select>
                            @error('spot_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <!-- Custom City Search with jQuery -->
                        <div class="form-group mt-2">
                            <label for="city_search">City</label>
                            <div class="position-relative">
                                <input type="text" id="city_search" class="form-control @error('city_id') is-invalid @enderror" 
                                       placeholder="Type to search cities..." autocomplete="off">
                                <input type="hidden" name="city_id" id="city_id" value="{{ old('city_id') }}" required>
                                <div id="city_results" class="dropdown-menu w-100" style="display:none; max-height:250px; overflow-y:auto;"></div>
                            </div>
                            <small id="selected_city_name" class="form-text {{ old('city_id') ? 'text-success' : 'text-muted' }}">
                                {{ old('city_id') ? 'Selected: ' . ($cities->find(old('city_id'))->name ?? 'Unknown city') : 'No city selected' }}
                            </small>
                            @error('city_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mt-2">
                            <label for="gender">Gender</label>
                            <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror" required>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="shemale" {{ old('gender') == 'shemale' ? 'selected' : '' }}>Shemale</option>
                            </select>
                            @error('gender')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mt-2">
                            <label for="starting_price">Starting Price (€)</label>
                            <input type="number" name="starting_price" id="starting_price" class="form-control @error('starting_price') is-invalid @enderror" value="{{ old('starting_price', 300) }}" min="0" step="10" required>
                            @error('starting_price')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mt-2">
                            <label for="duration_days">Duration (Days)</label>
                            <select name="duration_days" id="duration_days" class="form-control @error('duration_days') is-invalid @enderror" required>
                                @foreach([1, 2, 3, 5, 7, 10, 14, 21, 30] as $days)
                                    <option value="{{ $days }}" {{ old('duration_days', 7) == $days ? 'selected' : '' }}>
                                        {{ $days }} {{ Str::plural('day', $days) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('duration_days')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">Create Auction Spot</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .city-item {
        padding: 8px 12px;
        cursor: pointer;
    }
    .city-item:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('js')
<script>
$(document).ready(function() {
    let searchTimeout;
    
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
    
    // Event listener for focusing on the search input
    $('#city_search').on('focus', function() {
        if ($(this).val().trim().length >= 2) {
            searchCities($(this).val().trim());
        }
    });
    
    // If there's a previously selected city, show it in the input
    if ($('#city_id').val()) {
        const cityName = $('#selected_city_name').text().replace('Selected: ', '');
        $('#city_search').val(cityName);
    }
});
</script>
@endpush