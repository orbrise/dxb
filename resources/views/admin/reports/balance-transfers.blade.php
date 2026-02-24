@extends('admin.layout.master')

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Balance Transfer Reports</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Track all balance transfer activities</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('admin.reports.index')}}">Reports</a></li>
            <li class="breadcrumb-item active">Balance Transfers</li>
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
                <form method="GET" action="{{ route('admin.reports.balance-transfers') }}">
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
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Amount Range</label>
                                <select name="amount_range" class="form-control">
                                    <option value="">All Amounts</option>
                                    <option value="0-50" {{ request('amount_range') == '0-50' ? 'selected' : '' }}>$ 0 - $50</option>
                                    <option value="51-100" {{ request('amount_range') == '51-100' ? 'selected' : '' }}>$ 51 - $ 100</option>
                                    <option value="101-500" {{ request('amount_range') == '101-500' ? 'selected' : '' }}>$ 101 - $ 500</option>
                                    <option value="500+" {{ request('amount_range') == '500+' ? 'selected' : '' }}>$ 500+</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="{{ route('admin.reports.balance-transfers') }}" class="btn btn-secondary">Reset</a>
                            <a href="{{ route('admin.reports.export', ['type' => 'transfers'] + request()->all()) }}" class="btn btn-success">Export CSV</a>
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
                        <p class="text-truncate font-size-14 mb-2">Total Transfers</p>
                        <h4 class="mb-2">{{ $stats['total_transfers'] }}</h4>
                        <p class="text-muted mb-0">All time</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="fa fa-exchange-alt font-size-24"></i>
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
                        <p class="text-truncate font-size-14 mb-2">Total Amount</p>
                        <h4 class="mb-2">${{ number_format($stats['total_amount'], 2) }}</h4>
                        <p class="text-muted mb-0">Transferred</p>
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
                        <p class="text-truncate font-size-14 mb-2">Success Rate</p>
                        <h4 class="mb-2">{{ number_format($stats['success_rate'], 1) }}%</h4>
                        <p class="text-muted mb-0">{{ $stats['successful_transfers'] }} successful</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="fa fa-check-circle font-size-24"></i>
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
                        <p class="text-truncate font-size-14 mb-2">Average Transfer</p>
                        <h4 class="mb-2">${{ number_format($stats['average_amount'], 2) }}</h4>
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

<!-- Transfer Table -->
<div class="row mt-2">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Balance Transfers</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Wallet ID</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th>Transfer Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transfers as $transfer)
                                <tr>
                                    <td>{{ $transfer->id }}</td>
                                    <td>
                                        @if($transfer->wallet && $transfer->wallet->user)
                                            <strong>{{ $transfer->wallet->user->name }}</strong>
                                            <br><small class="text-muted">{{ $transfer->wallet->user->email }}</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $transfer->wallet_id }}</span>
                                    </td>
                                    <td>
                                        <strong>${{ number_format($transfer->amount, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $transfer->status == 'completed' ? 'success' : 
                                            ($transfer->status == 'failed' ? 'danger' : 'warning') 
                                        }}">
                                            {{ ucfirst($transfer->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $transfer->description ?? 'Balance transfer' }}
                                    </td>
                                    <td>
                                        {{ $transfer->created_at->format('M d, Y') }}
                                        <br><small class="text-muted">{{ $transfer->created_at->format('H:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            {{-- No pending status actions --}}
                                            <button class="btn btn-outline-info" title="View Details">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                                        <br>No balance transfers found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($transfers->hasPages())
                    <div class="mt-3">
                        {{ $transfers->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Daily Transfer Chart -->
<div class="row mt-2">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Daily Transfer Activity (Last 30 Days)</h5>
            </div>
            <div class="card-body">
                <canvas id="transferChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Daily transfer chart
const ctx = document.getElementById('transferChart').getContext('2d');
const transferChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($stats['daily_labels'] ?? []),
        datasets: [{
            label: 'Transfer Count',
            data: @json($stats['daily_counts'] ?? []),
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4
        }, {
            label: 'Transfer Amount ($)',
            data: @json($stats['daily_amounts'] ?? []),
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Transfer Count'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Amount ($)'
                },
                grid: {
                    drawOnChartArea: false,
                }
            }
        }
    }
});
</script>
@endsection
