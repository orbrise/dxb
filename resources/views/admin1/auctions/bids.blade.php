@extends('admin.layout.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bids for Auction Spot #{{ $auction->spot_number }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.auctions.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="auction-info mb-4">
                        <h5>Auction Details</h5>
                        <p><strong>City:</strong> {{ $auction->city->name }}</p>
                        <p><strong>Gender:</strong> {{ ucfirst($auction->gender) }}</p>
                        <p><strong>Current Price:</strong> €{{ number_format($auction->current_price, 2) }}</p>
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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($auction->bids as $bid)
                            <tr>
                                <td>{{ $bid->id }}</td>
                                <td>{{ $bid->profile->name ?? 'Unknown' }}</td>
                                <td>€{{ number_format($bid->amount, 2) }}</td>
                                <td>{{ $bid->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <span class="">
                                        {{ ucfirst($bid->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No bids placed yet.</td>
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