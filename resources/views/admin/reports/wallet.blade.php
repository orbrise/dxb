@extends('admin.layout.master')

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Wallet Reports</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Detailed wallet transaction analysis</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('admin.reports.index')}}">Reports</a></li>
            <li class="breadcrumb-item active">Wallet Reports</li>
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
                <form method="GET" action="{{ route('admin.reports.wallet') }}">
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
                                <label>Transaction Type</label>
                                <select name="transaction_type" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="debit" {{ request('transaction_type') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                                    <option value="credit" {{ request('transaction_type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                    <option value="package_purchase" {{ request('transaction_type') == 'package_purchase' ? 'selected' : '' }}>Package Purchase</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : 'selected' }}>Completed</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="{{ route('admin.reports.wallet') }}" class="btn btn-secondary">Reset</a>
                            <a href="{{ route('admin.reports.export', ['type' => 'wallet'] + request()->all()) }}" class="btn btn-success">Export CSV</a>
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
                        <p class="text-truncate font-size-14 mb-2">Total Transactions</p>
                        <h4 class="mb-2">{{ $stats['total_transactions'] }}</h4>
                        <p class="text-muted mb-0">All time</p>
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
                        <p class="text-truncate font-size-14 mb-2">Total Amount</p>
                        <h4 class="mb-2">${{ number_format($stats['total_amount'], 2) }}</h4>
                        <p class="text-muted mb-0">All transactions</p>
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
                        <p class="text-truncate font-size-14 mb-2">Completed</p>
                        <h4 class="mb-2">{{ $stats['completed_transactions'] }}</h4>
                        <p class="text-muted mb-0">{{ number_format($stats['completion_rate'], 1) }}% success rate</p>
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
                        <p class="text-truncate font-size-14 mb-2">Average Amount</p>
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

<!-- Transaction Table -->
<div class="row mt-2">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Wallet Transactions</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Wallet ID</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>
                                        @if($transaction->wallet && $transaction->wallet->user)
                                            {{ $transaction->wallet->user->name }}
                                            <br><small class="text-muted">{{ $transaction->wallet->user->email }}</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->wallet_id }}</td>
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
                                    <td>{{ $transaction->description ?? 'N/A' }}</td>
                                    <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No transactions found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($transactions->hasPages())
                    <div class="mt-3">
                        {{ $transactions->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
