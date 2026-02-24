@extends('admin.layout.master')

@section('content')
<div class="row page-title clearfix">
    <div class="page-title-left">
        <h5 class="mr-0 mr-r-5">Reports Dashboard</h5>
        <p class="mr-0 text-muted d-none d-md-inline-block">Comprehensive reports for wallet activities, transfers, and paid ads</p>
    </div>
    <div class="page-title-right d-none d-sm-inline-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Reports</li>
        </ol>
    </div>
</div>

<div class="row mt-3">
    <!-- Quick Stats Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-truncate font-size-14 mb-2">Wallet Reports</p>
                        <h4 class="mb-2">{{ \App\Models\WalletTransaction::count() }}</h4>
                        <p class="text-muted mb-0">Total Transactions</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="fa fa-wallet font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.reports.wallet') }}" class="btn btn-primary btn-sm">View Details</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-truncate font-size-14 mb-2">Balance Transfers</p>
                        <h4 class="mb-2">{{ \App\Models\WalletTransaction::where('type', 'transfer')->count() }}</h4>
                        <p class="text-muted mb-0">Total Transfers</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="fa fa-exchange-alt font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.reports.balance-transfers') }}" class="btn btn-success btn-sm">View Details</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-truncate font-size-14 mb-2">Paid Ads</p>
                        <h4 class="mb-2">{{ \App\Models\WalletTransaction::where('type', 'package_purchase')->count() }}</h4>
                        <p class="text-muted mb-0">Ad Purchases</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="fa fa-bullhorn font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.reports.paid-ads') }}" class="btn btn-warning btn-sm">View Details</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-truncate font-size-14 mb-2">Revenue</p>
                        <h4 class="mb-2">$ {{ number_format(\App\Models\WalletTransaction::where('status', 'completed')->sum('amount'), 2) }}</h4>
                        <p class="text-muted mb-0">Total Revenue</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="fa fa-dollar-sign font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-info btn-sm">View Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-truncate font-size-14 mb-2">Profile Reports</p>
                        <h4 class="mb-2">{{ \App\Models\Report::where('status', 'pending')->count() }}</h4>
                        <p class="text-muted mb-0">Pending Reports</p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-danger rounded-3">
                                <i class="fa fa-flag font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.profile-reports.index') }}" class="btn btn-danger btn-sm">View Reports</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Reports Menu</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 h-100">
                            <h6><i class="fa fa-wallet text-primary"></i> Wallet Reports</h6>
                            <p class="text-muted">View detailed wallet transaction reports with filters for date range, transaction type, and status.</p>
                            <a href="{{ route('admin.reports.wallet') }}" class="btn btn-primary btn-sm">Open Report</a>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 h-100">
                            <h6><i class="fa fa-exchange-alt text-success"></i> Balance Transfers</h6>
                            <p class="text-muted">Track all balance transfer activities with success rates and amounts transferred.</p>
                            <a href="{{ route('admin.reports.balance-transfers') }}" class="btn btn-success btn-sm">Open Report</a>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 h-100">
                            <h6><i class="fa fa-bullhorn text-warning"></i> Paid Ads Reports</h6>
                            <p class="text-muted">Monitor paid advertisement purchases, package popularity, and revenue generation.</p>
                            <a href="{{ route('admin.reports.paid-ads') }}" class="btn btn-warning btn-sm">Open Report</a>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 h-100">
                            <h6><i class="fa fa-flag text-danger"></i> Profile Reports</h6>
                            <p class="text-muted">View and manage user-submitted profile reports for inappropriate or fake content.</p>
                            <a href="{{ route('admin.profile-reports.index') }}" class="btn btn-danger btn-sm">Open Report</a>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6 mb-3">
                        <div class="border rounded p-3 h-100">
                            <h6><i class="fa fa-chart-line text-info"></i> Dashboard Overview</h6>
                            <p class="text-muted">Get a comprehensive overview of all activities with summary statistics and recent transactions.</p>
                            <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-info btn-sm">Open Dashboard</a>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="border rounded p-3 h-100">
                            <h6><i class="fa fa-download text-secondary"></i> Export Data</h6>
                            <p class="text-muted">Export reports to CSV format for external analysis and record keeping.</p>
                            <div class="btn-group">
                                <a href="{{ route('admin.reports.export', ['type' => 'wallet']) }}" class="btn btn-secondary btn-sm">Wallet CSV</a>
                                <a href="{{ route('admin.reports.export', ['type' => 'transfers']) }}" class="btn btn-secondary btn-sm">Transfers CSV</a>
                                <a href="{{ route('admin.reports.export', ['type' => 'ads']) }}" class="btn btn-secondary btn-sm">Ads CSV</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
