@extends("admin.layout.master")

@section('content')

 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Auctions</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage auctions effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{url('admin/auctions')}}">Auctions</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Auction</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>


<div class="container-fluid mt-3 mb-3">
    <div class="row">
    <div class="col-md-12"><div class="card-tools float-right mb-2">
                        <a href="{{ route('admin.auctions.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                    </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Edit Auction Spot #{{ $auction->spot_number }}</h5>

                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    
                    <form action="{{ route('admin.auctions.update', $auction) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="spot_number">Spot Number</label>
                            <select name="spot_number" id="spot_number" class="form-control @error('spot_number') is-invalid @enderror" required>
                                @for($i = 1; $i <= 6; $i++)
                                    <option value="{{ $i }}" {{ old('spot_number', $auction->spot_number) == $i ? 'selected' : '' }}>
                                        Spot #{{ $i }}
                                    </option>
                                @endfor
                            </select>
                            @error('spot_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mt-2">
                            <label for="city_id">City</label>
                            <div class="position-relative">
                                <input type="text" id="city_search" class="form-control" placeholder="Search for a city..." autocomplete="off" value="{{ $auction->city->name ?? '' }}">
                                <input type="hidden" name="city_id" id="city_id" value="{{ old('city_id', $auction->city_id) }}" required>
                                <div id="city_results" class="position-absolute w-100 bg-white shadow-sm border rounded" style="max-height: 300px; overflow-y: auto; z-index: 1000; display: none;"></div>
                            </div>
                            <small id="selected_city_name" class="form-text text-muted">{{ old('city_id', $auction->city_id) ? ($cities->find(old('city_id', $auction->city_id))->name ?? 'Unknown city') : 'No city selected' }}</small>
                            @error('city_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mt-2">
                            <label for="gender">Gender</label>
                            <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror" required>
                                <option value="female" {{ old('gender', $auction->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="male" {{ old('gender', $auction->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="shemale" {{ old('gender', $auction->gender) == 'shemale' ? 'selected' : '' }}>Shemale</option>
                            </select>
                            @error('gender')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mt-2">
                            <label for="current_price">Current Price (€)</label>
                            <input type="number" name="current_price" id="current_price" class="form-control @error('current_price') is-invalid @enderror" value="{{ old('current_price', $auction->current_price) }}" min="0" step="10" required>
                            @error('current_price')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mt-2">
                            <label for="end_date">End Date</label>
                            <input type="datetime-local" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $auction->end_date->format('Y-m-d\TH:i')) }}" required>
                            @error('end_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mt-2">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $auction->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="ended" {{ old('status', $auction->status) == 'ended' ? 'selected' : '' }}>Ended</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mt-2">
                            <button type="submit" class="btn btn-primary">Update Auction</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection 

@push('js')
<script>
    $(document).ready(function() {
        let timeout;
        
        $('#city_search').on('input', function() {
            clearTimeout(timeout);
            const searchTerm = $(this).val().trim();
            
            if (searchTerm.length < 2) {
                $('#city_results').hide();
                return;
            }
            
            timeout = setTimeout(() => {
                $.ajax({
                    url: "{{ route('admin.cities.search') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        search: searchTerm
                    },
                    success: function(data) {
                        let html = '';
                        
                        if (data.length > 0) {
                            data.forEach(city => {
                                html += `<div class="city-item p-2 border-bottom" style="cursor: pointer;" data-id="${city.id}" data-name="${city.name}">
                                    ${city.name} ${city.country ? `(${city.country})` : ''}
                                </div>`;
                            });
                        } else {
                            html = '<div class="p-2">No cities found</div>';
                        }
                        
                        $('#city_results').html(html).show();
                    },
                    error: function() {
                        $('#city_results').html('<div class="p-2">Error loading cities</div>').show();
                    }
                });
            }, 300);
        });
        
        $(document).on('click', '.city-item', function() {
            const cityId = $(this).data('id');
            const cityName = $(this).data('name');
            
            $('#city_id').val(cityId);
            $('#city_search').val(cityName);
            $('#selected_city_name').text(cityName);
            $('#city_results').hide();
        });
        
        // Hide dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.position-relative').length) {
                $('#city_results').hide();
            }
        });
        
        // Focus event to show dropdown again if there's content
        $('#city_search').on('focus', function() {
            const searchTerm = $(this).val().trim();
            if (searchTerm.length >= 2) {
                // Trigger the input event to reload results
                $(this).trigger('input');
            }
        });
    });
</script>
@endpush