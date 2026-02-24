@extends('admin.layout.master')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="{{smart_asset('assets/libs/simple-datatables/style.css')}}" rel="stylesheet" type="text/css" />
<link href="{{smart_asset('assets/libs/mobius1-selectr/selectr.min.css')}}" rel="stylesheet" type="text/css" />
  <style>
  span.input{display:none;}

  .form-check-input {
    margin-left: 0rem;
}

  /* Prevent Select2 hover from auto-selecting */
  .select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #5897fb;
    color: white;
}

  </style>
@endpush

@section('content')

 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Auctions</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage cities effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active"><a href="{{ route('admin.auctions.index') }}">Auctions</a></li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>


<div class="container-fluid mt-2 mb-2">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title" style="float:left;    margin-top: 6px;">Bids for Auction Spot #{{ $auction->spot_number }}</h4>
                    <div class="card-tools">
                        <a style="float:right" href="{{ route('admin.auctions.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="auction-info mb-4">
                        <h5>Auction Details</h5>
                        <p><strong>City:</strong> {{ $auction->city->name }}</p>
                        <p><strong>Gender:</strong> {{ ucfirst($auction->gender) }}</p>
                        <p><strong>Current Price:</strong> ${{ number_format($auction->current_price, 2) }}</p>
                        <p><strong>End Date:</strong> {{ $auction->end_date->format('Y-m-d H:i') }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($auction->status) }}</p>
                    </div>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Bidder</th>
                                <th>Amount</th>
                                <th>Placed At</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($auction->bids as $bid)
                            <tr>
                                <td>{{ $bid->id }}</td>
                                <td>{{ $bid->profile->name ?? 'Unknown' }}</td>
                                <td>${{ number_format($bid->amount, 2) }}</td>
                                <td>{{ $bid->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <span class="badge badge-{{ $bid->status === 'won' ? 'success' : ($bid->status === 'lost' ? 'danger' : 'info') }}">
                                        {{ ucfirst($bid->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($auction->status !== 'ended')
                                        <form action="{{ route('admin.auctions.award', [$auction, $bid]) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" 
                                                    onclick="return confirm('Are you sure you want to award this spot to {{ $bid->profile->name ?? 'this bidder' }}?')">
                                                <i class="fas fa-trophy"></i> Award
                                            </button>
                                        </form>
                                    @else
                                        @if($bid->status === 'won')
                                            <span class="badge badge-success"><i class="fas fa-trophy"></i> Winner</span>
                                        @else
                                            -
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No bids placed yet.</td>
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