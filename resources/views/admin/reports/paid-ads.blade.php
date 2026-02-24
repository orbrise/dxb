@extends('admin.layout.master')

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Paid Ads Reports</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Monitor paid advertisement purchases and revenue</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('admin.reports.index')}}">Reports</a></li>
            <li class="breadcrumb-item active">Paid Ads</li>
        </ol>
    </div>
</div>

<!-- Filters Section -->
<div class="row mt-2">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Filters</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.paid-ads') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Package</label>
                                <select name="package_id" class="form-control">
                                    <option value="">All Packages</option>
                                    @foreach($packages as $package)
                                        <option value="{{ $package->id }}" {{ request('package_id') == $package->id ? 'selected' : '' }}>
                                            {{ $package->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="{{ route('admin.reports.paid-ads') }}" class="btn btn-secondary">Reset</a>
                            <a href="{{ route('admin.reports.export', ['type' => 'ads'] + request()->all()) }}" class="btn btn-success">Export CSV</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Stats -->
<div class="row mt-2">
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-truncate font-size-14 mb-2">Total Ad Purchases</p>
                        <h4 class="mb-2">{{ $stats['total_purchases'] }}</h4>
                        <p class="text-muted mb-0">All time</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="fa fa-bullhorn font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-truncate font-size-14 mb-2">Total Revenue</p>
                        <h4 class="mb-2">${{ number_format($stats['total_revenue'], 2) }}</h4>
                        <p class="text-muted mb-0">From ads</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="fa fa-dollar-sign font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-truncate font-size-14 mb-2">Active Promotions</p>
                        <h4 class="mb-2">{{ $stats['active_promotions'] }}</h4>
                        <p class="text-muted mb-0">Currently running</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="fa fa-star font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-truncate font-size-14 mb-2">Average Purchase</p>
                        <h4 class="mb-2">${{ number_format($stats['average_purchase'], 2) }}</h4>
                        <p class="text-muted mb-0">Per transaction</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="fa fa-calculator font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Package Popularity Chart -->
<div class="row mt-2">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Package Popularity</h5>
            </div>
            <div class="card-body">
                <canvas id="packageChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Revenue by Package</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Paid Ads Table -->
<div class="row mt-2">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Paid Advertisement Purchases</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Package</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Promotion Status</th>
                                <th>Purchase Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->id }}</td>
                                    <td>
                                        @if($purchase->wallet && $purchase->wallet->user)
                                            <strong>{{ $purchase->wallet->user->name }}</strong>
                                            <br><small class="text-muted">{{ $purchase->wallet->user->email }}</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($purchase->package)
                                            <strong>{{ $purchase->package->name }}</strong>
                                            <br><small class="text-muted">{{ $purchase->package->duration }} days</small>
                                        @else
                                            <span class="text-muted">Package not found</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>${{ number_format($purchase->amount, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $purchase->status == 'completed' ? 'success' : 
                                            ($purchase->status == 'failed' ? 'danger' : 'warning') 
                                        }}">
                                            {{ ucfirst($purchase->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($purchase->promotion_status)
                                            @if($purchase->promotion_status['is_active'])
                                                <span class="badge bg-success">Active</span>
                                                <br><small class="text-muted">Until {{ $purchase->promotion_status['expires_at'] }}</small>
                                            @else
                                                <span class="badge bg-secondary">Expired</span>
                                            @endif
                                        @else
                                            <span class="badge bg-light text-dark">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $purchase->created_at->format('M d, Y') }}
                                        <br><small class="text-muted">{{ $purchase->created_at->format('H:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if($purchase->wallet && $purchase->wallet->user)
                                                @php
                                                    $profile = \App\Models\UsersProfile::where('user_id', $purchase->wallet->user->id)->first();
                                                @endphp
                                                @if($profile)
                                                    <a href="{{ url('my-profile/' . $profile->slug . '/' . $profile->id) }}" class="btn btn-outline-info" title="View Profile" target="_blank">
                                                        <i class="fa fa-user"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-outline-secondary" title="Profile Not Found" disabled>
                                                        <i class="fa fa-user"></i>
                                                    </button>
                                                @endif
                                            @else
                                                <button class="btn btn-outline-secondary" title="User Not Found" disabled>
                                                    <i class="fa fa-user"></i>
                                                </button>
                                            @endif
                                            <button class="btn btn-outline-primary" title="View Details">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            @if($purchase->status == 'completed' && $purchase->promotion_status && $purchase->promotion_status['is_active'])
                                                <button class="btn btn-outline-danger" title="Stop Promotion">
                                                    <i class="fa fa-stop"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fa fa-bullhorn fa-3x text-muted mb-3"></i>
                                        <br>No paid advertisement purchases found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($purchases->hasPages())
                    <div class="mt-3">
                        {{ $purchases->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Top Packages Table -->
<div class="row mt-2">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Top Performing Packages</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Package</th>
                                <th>Price</th>
                                <th>Duration</th>
                                <th>Total Sales</th>
                                <th>Revenue</th>
                                <th>Avg. Monthly Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['package_stats'] as $packageStat)
                                <tr>
                                    <td><strong>{{ $packageStat['name'] }}</strong></td>
                                    <td>$ {{ number_format($packageStat['price'], 2) }}</td>
                                    <td>{{ $packageStat['duration'] }} days</td>
                                    <td>{{ $packageStat['total_sales'] }}</td>
                                    <td><strong>${{ number_format($packageStat['revenue'], 2) }}</strong></td>
                                    <td>{{ number_format($packageStat['avg_monthly_sales'], 1) }}</td>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Package popularity chart
const packageCtx = document.getElementById('packageChart').getContext('2d');
const packageChart = new Chart(packageCtx, {
    type: 'doughnut',
    data: {
        labels: @json($stats['package_names'] ?? []),
        datasets: [{
            data: @json($stats['package_counts'] ?? []),
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Revenue by package chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: @json($stats['package_names'] ?? []),
        datasets: [{
            label: 'Revenue ($)',
            data: @json($stats['package_revenues'] ?? []),
            backgroundColor: '#28a745'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value;
                    }
                }
            }
        }
    }
});
</script>
@endsection
