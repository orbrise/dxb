@extends('admin.layout.master')

@section('content')
 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Wallet</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage wallet effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Wallet</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>

<div class="row mt-3 mb-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Top Up User Wallet</h5>
            </div>
            <div class="card-body">
                <form id="topupForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Select User</label>
                            <select class="form-select" name="user_id" id="user_select" required>
                                <option value="">Choose User</option>
                                @foreach($users as $user)
                                    <option value="{{$user->id}}" uname="{{$user->name}}">{{$user->name}} - {{$user->email}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Amount (AED)</label>
                            <input type="number" class="form-control" name="amount" min="1" step="0.01" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Note</label>
                            <input type="text" class="form-control" name="note">
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Top Up Wallet</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Transaction History Table -->
<div class="row mb-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">All Transactions <span id="uname"></span></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="transactionsTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Transaction History Table -->
<div class="row mb-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Transactions</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Note</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr>
                                <td>{{$transaction->wallet->user->name ?? ''}}</td>
                                <td>AED {{number_format($transaction->amount, 2)}}</td>
                                <td>{{$transaction->type}}</td>
                                <td>{{$transaction->description}}</td>
                                <td>{{$transaction->created_at->format('d M Y H:i')}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
$(document).ready(function() {
    let token = "{{ csrf_token() }}";
    
    $('#topupForm').submit(function(e) {
        e.preventDefault();
        let formData = $(this).serialize() + '&_token=' + token;
        
        $.post("{{ route('admin.wallet.topup') }}", formData, function(response) {
            if(response.success) {
                location.reload();
            }
        });
    });


    $('#user_select').change(function() {
        let userId = $(this).val();
        let element = $(this).find('option:selected'); 
        let uname = element.attr("uname"); 
        $("span#uname").text('of user '+uname);
        if(userId != '') {
            $.get("{{ url('admin/wallet/transactions') }}/" + userId, function(transactions) {
                let html = '';
                transactions.forEach(transaction => {
                    html += `
                        <tr>
                            <td>${transaction.created_at}</td>
                            <td>${transaction.wallet.user.name}</td>
                            <td>AED ${parseFloat(transaction.amount).toFixed(2)}</td>
                            <td>
                                <span class="badge bg-${transaction.type === 'credit' ? 'success' : 'danger'}">
                                    ${transaction.type}
                                </span>
                            </td>
                            <td>${transaction.description}</td>
                            <td>${transaction.status}</td>
                        </tr>
                    `;
                });
                $('table#transactionsTable tbody').html(html);
            });
        }
    });

    
});
</script>
@endpush
@endsection