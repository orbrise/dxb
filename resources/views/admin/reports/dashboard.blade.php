@extends('admin.layout.master')

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Reports Dashboard</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Comprehensive overview of all activities</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('admin.reports.index')}}">Reports</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </div>
</div>

<!-- Summary Overview -->
<div class="row mt-2">
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-truncate font-size-14 mb-2">Total Transactions</p>
                        <h4 class="mb-2">{{ $summary['total_transactions'] }}</h4>
                        <p class="text-muted mb-0">
                            <span class="text-{{ $summary['transaction_trend'] > 0 ? 'success' : 'danger' }} mr-2">
                                <i class="fa fa-arrow-{{ $summary['transaction_trend'] > 0 ? 'up' : 'down' }}"></i>
                                {{ abs($summary['transaction_trend']) }}%
                            </span>
                            vs last month
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="fa fa-list font-size-24"></i>
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
                        <h4 class="mb-2">$ {{ number_format($summary['total_revenue'], 2) }}</h4>
                        <p class="text-muted mb-0">
                            <span class="text-{{ $summary['revenue_trend'] > 0 ? 'success' : 'danger' }} mr-2">
                                <i class="fa fa-arrow-{{ $summary['revenue_trend'] > 0 ? 'up' : 'down' }}"></i>
                                {{ abs($summary['revenue_trend']) }}%
                            </span>
                            vs last month
                        </p>
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
                        <p class="text-truncate font-size-14 mb-2">Active Users</p>
                        <h4 class="mb-2">{{ $summary['active_users'] }}</h4>
                        <p class="text-muted mb-0">Users with transactions</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="fa fa-users font-size-24"></i>
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
                        <h4 class="mb-2">{{ number_format($summary['success_rate'], 1) }}%</h4>
                        <p class="text-muted mb-0">Transaction success</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="fa fa-check-circle font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Breakdown -->
<div class="row mt-2">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Transaction Activity (Last 30 Days)</h5>
            </div>
            <div class="card-body">
                <canvas id="activityChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Transaction Types</h5>
            </div>
            <div class="card-body">
                <canvas id="typeChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Breakdown -->
<div class="row mt-2">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Wallet Activity</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Wallets</span>
                    <strong>{{ $breakdown['wallet']['total_wallets'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Active Wallets</span>
                    <strong>{{ $breakdown['wallet']['active_wallets'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Transactions</span>
                    <strong>{{ $breakdown['wallet']['total_transactions'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Volume</span>
                    <strong>${{ number_format($breakdown['wallet']['total_volume'], 2) }}</strong>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.reports.wallet') }}" class="btn btn-primary btn-sm w-100">View Details</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Balance Transfers</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Transfers</span>
                    <strong>{{ $breakdown['transfers']['total_transfers'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Successful</span>
                    <strong>{{ $breakdown['transfers']['successful'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Success Rate</span>
                    <strong>{{ number_format($breakdown['transfers']['success_rate'], 1) }}%</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Amount</span>
                    <strong>${{ number_format($breakdown['transfers']['total_amount'], 2) }}</strong>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.reports.balance-transfers') }}" class="btn btn-success btn-sm w-100">View Details</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Paid Advertisements</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Purchases</span>
                    <strong>{{ $breakdown['ads']['total_purchases'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Active Promotions</span>
                    <strong>{{ $breakdown['ads']['active_promotions'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Revenue</span>
                    <strong>${{ number_format($breakdown['ads']['total_revenue'], 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Avg. Purchase</span>
                    <strong>${{ number_format($breakdown['ads']['avg_purchase'], 2) }}</strong>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.reports.paid-ads') }}" class="btn btn-warning btn-sm w-100">View Details</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="row mt-2">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Transactions</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>
                                        @if($transaction->wallet && $transaction->wallet->user)
                                            {{ $transaction->wallet->user->name }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $transaction->type == 'deposit' ? 'success' : 
                                            ($transaction->type == 'withdrawal' ? 'danger' : 
                                            ($transaction->type == 'transfer' ? 'info' : 'warning')) 
                                        }}">
                                            {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($transaction->amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $transaction->status == 'completed' ? 'success' : 
                                            ($transaction->status == 'failed' ? 'danger' : 'warning') 
                                        }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $transaction->created_at->format('M d, H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No recent transactions</td>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Activity chart
const activityCtx = document.getElementById('activityChart').getContext('2d');
const activityChart = new Chart(activityCtx, {
    type: 'line',
    data: {
        labels: @json($charts['activity_labels'] ?? []),
        datasets: [{
            label: 'Transactions',
            data: @json($charts['activity_counts'] ?? []),
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4
        }, {
            label: 'Revenue ($)',
            data: @json($charts['activity_revenue'] ?? []),
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
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
                    text: 'Transaction Count'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Revenue ($)'
                },
                grid: {
                    drawOnChartArea: false,
                }
            }
        }
    }
});

// Transaction types chart
const typeCtx = document.getElementById('typeChart').getContext('2d');
const typeChart = new Chart(typeCtx, {
    type: 'doughnut',
    data: {
        labels: @json($charts['type_labels'] ?? []),
        datasets: [{
            data: @json($charts['type_counts'] ?? []),
            backgroundColor: [
                '#28a745',
                '#dc3545',
                '#17a2b8',
                '#ffc107'
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
</script>
@endsection
