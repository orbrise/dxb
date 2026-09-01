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
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="abandoned" {{ request('status') == 'abandoned' ? 'selected' : '' }}>Abandoned</option>
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
        <a href="{{ route('admin.reports.wallet', array_merge(request()->query(), ['status' => 'failed'])) }}" class="text-decoration-none">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Failed</p>
                            <h4 class="mb-2 text-danger">{{ $stats['failed_transactions'] }}</h4>
                            <p class="text-muted mb-0">Click to review &amp; contact users</p>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-light text-danger rounded-3">
                                    <i class="fa fa-exclamation-triangle font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
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
                                <th>Type</th>
                                <th>Method</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Reason / Description</th>
                                <th>Reference</th>
                                <th>Date</th>
                                <th>Contact</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                @php
                                    $txUser = ($transaction->wallet && $transaction->wallet->user)
                                        ? $transaction->wallet->user
                                        : $transaction->user;
                                @endphp
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>
                                        @if($txUser)
                                            {{ $txUser->name }}
                                            <br><small class="text-muted">{{ $txUser->email }}</small>
                                            @if(!empty($txUser->phone))
                                                <br><small class="text-muted">{{ $txUser->phone }}</small>
                                            @endif
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
                                    <td>
                                        @if($transaction->payment_method)
                                            <small>{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</small>
                                        @else
                                            <span class="text-muted">&mdash;</span>
                                        @endif
                                    </td>
                                    <td>${{ number_format($transaction->amount, 2) }}</td>
                                    <td>
                                        @php
                                            $badgeColor = [
                                                'completed' => 'success',
                                                'failed' => 'danger',
                                                'cancelled' => 'warning',
                                                'abandoned' => 'secondary',
                                                'pending' => 'info',
                                            ][$transaction->status] ?? 'warning';
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(in_array($transaction->status, ['failed', 'cancelled', 'abandoned']))
                                            @php
                                                $reasonColor = [
                                                    'failed' => 'text-danger',
                                                    'cancelled' => 'text-warning',
                                                    'abandoned' => 'text-muted',
                                                ][$transaction->status] ?? 'text-muted';
                                            @endphp
                                            <div class="{{ $reasonColor }}">
                                                <strong>{{ $transaction->error_message ?: ucfirst($transaction->status) }}</strong>
                                            </div>
                                            @if($transaction->error_code || $transaction->decline_code)
                                                <small class="text-muted">
                                                    @if($transaction->error_code) code: <code>{{ $transaction->error_code }}</code>@endif
                                                    @if($transaction->decline_code) &middot; decline: <code>{{ $transaction->decline_code }}</code>@endif
                                                </small>
                                            @endif
                                        @elseif($transaction->status === 'pending')
                                            <div class="text-info">
                                                <strong>Awaiting completion</strong>
                                                <small class="text-muted d-block">Started {{ $transaction->created_at->diffForHumans() }}</small>
                                            </div>
                                        @else
                                            {{ $transaction->description ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->reference)
                                            <small><code>{{ $transaction->reference }}</code></small>
                                        @else
                                            <span class="text-muted">&mdash;</span>
                                        @endif
                                    </td>
                                    <td><small>{{ $transaction->created_at->format('M d, Y H:i') }}</small></td>
                                    <td>
                                        @if($txUser && $txUser->email)
                                            @php
                                                $subject = rawurlencode('About your recent payment (' . ($transaction->reference ?: '#' . $transaction->id) . ')');
                                                $body = rawurlencode(
                                                    "Hi " . ($txUser->name ?: '') . ",\n\n" .
                                                    "We noticed a recent payment attempt on our site could not be completed" .
                                                    ($transaction->error_message ? " (reason: " . $transaction->error_message . ")" : "") .
                                                    ". We'd like to help you complete it.\n\nAmount: $" . number_format($transaction->amount, 2) . "\n" .
                                                    ($transaction->reference ? "Reference: " . $transaction->reference . "\n" : "") .
                                                    "\nPlease reply to this email if you need any assistance."
                                                );
                                            @endphp
                                            <a href="mailto:{{ $txUser->email }}?subject={{ $subject }}&body={{ $body }}"
                                               class="btn btn-sm btn-outline-primary" title="Email user">
                                                <i class="fa fa-envelope"></i>
                                            </a>
                                            @if(!empty($txUser->phone))
                                                @php $wa = preg_replace('/\D+/', '', $txUser->phone); @endphp
                                                <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"
                                                   class="btn btn-sm btn-outline-success" title="WhatsApp user">
                                                    <i class="fa fa-whatsapp"></i>
                                                </a>
                                            @endif
                                        @else
                                            <span class="text-muted">&mdash;</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No transactions found</td>
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
