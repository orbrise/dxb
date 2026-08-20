@extends('admin.layout.master')

@section('content')
 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Reviews</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage reviews effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Reviews</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>


    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

   <div class="row mt-3 mb-3">
    <div class="col-lg-12">
        <div class="card">

            @php
                $pp = request('perPage', $reviews->perPage());
                $statusLabels = ['1' => 'Approved', '0' => 'Pending'];
                $replyLabels = ['yes' => 'With reply', 'no' => 'No reply'];
                $activeCount = collect(['id','user_id','profile_id','email','q','star','status','has_reply','date_from','date_to'])
                    ->filter(fn($k) => request()->filled($k))
                    ->count();
            @endphp

            <div class="card-header q-filter-bar py-2">
                <form method="GET" action="{{ route('reviews.index') }}" id="rFiltersForm" class="d-flex align-items-center flex-wrap" style="gap:6px;">
                    <input type="hidden" name="perPage" value="{{ $pp }}">

                    <span class="text-muted mr-1"><i class="fa fa-filter"></i></span>

                    {{-- ID --}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm {{ request('id') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                            ID{{ request('id') ? ': '.request('id') : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                        </button>
                        <div class="dropdown-menu p-2" style="min-width:220px;">
                            <input type="number" name="id" value="{{ request('id') }}" class="form-control form-control-sm mb-2" placeholder="Review ID" min="1">
                            <button type="submit" class="btn btn-sm btn-primary btn-block">Apply</button>
                        </div>
                    </div>

                    {{-- User ID --}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm {{ request('user_id') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                            User ID{{ request('user_id') ? ': '.request('user_id') : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                        </button>
                        <div class="dropdown-menu p-2" style="min-width:220px;">
                            <input type="number" name="user_id" value="{{ request('user_id') }}" class="form-control form-control-sm mb-2" placeholder="User ID" min="1">
                            <button type="submit" class="btn btn-sm btn-primary btn-block">Apply</button>
                        </div>
                    </div>

                    {{-- Profile ID --}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm {{ request('profile_id') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                            Profile ID{{ request('profile_id') ? ': '.request('profile_id') : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                        </button>
                        <div class="dropdown-menu p-2" style="min-width:220px;">
                            <input type="number" name="profile_id" value="{{ request('profile_id') }}" class="form-control form-control-sm mb-2" placeholder="Profile ID" min="1">
                            <button type="submit" class="btn btn-sm btn-primary btn-block">Apply</button>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm {{ request('email') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                            Email{{ request('email') ? ': '.\Illuminate\Support\Str::limit(request('email'), 20) : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                        </button>
                        <div class="dropdown-menu p-2" style="min-width:260px;">
                            <input type="text" name="email" value="{{ request('email') }}" class="form-control form-control-sm mb-2" placeholder="user@example.com">
                            <button type="submit" class="btn btn-sm btn-primary btn-block">Apply</button>
                        </div>
                    </div>

                    {{-- Search --}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm {{ request('q') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                            Search{{ request('q') ? ': "'.\Illuminate\Support\Str::limit(request('q'), 20).'"' : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                        </button>
                        <div class="dropdown-menu p-2" style="min-width:280px;">
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm mb-2" placeholder="Search review or reply...">
                            <button type="submit" class="btn btn-sm btn-primary btn-block">Apply</button>
                        </div>
                    </div>

                    {{-- Star --}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm {{ request()->filled('star') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                            Star{{ request()->filled('star') ? ': '.request('star').'★' : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                        </button>
                        <div class="dropdown-menu p-2" style="min-width:180px;">
                            <select name="star" class="form-control form-control-sm mb-2" onchange="this.form.submit()">
                                <option value="">All stars</option>
                                @for($s = 1; $s <= 5; $s++)
                                    <option value="{{ $s }}" {{ (string) request('star') === (string) $s ? 'selected' : '' }}>{{ $s }} ★</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm {{ request()->filled('status') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                            Status{{ request()->filled('status') ? ': '.($statusLabels[request('status')] ?? '') : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                        </button>
                        <div class="dropdown-menu p-2" style="min-width:180px;">
                            <select name="status" class="form-control form-control-sm mb-2" onchange="this.form.submit()">
                                <option value="">All statuses</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Approved</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>
                    </div>

                    {{-- Reply --}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm {{ request()->filled('has_reply') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                            Reply{{ request()->filled('has_reply') ? ': '.($replyLabels[request('has_reply')] ?? '') : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                        </button>
                        <div class="dropdown-menu p-2" style="min-width:180px;">
                            <select name="has_reply" class="form-control form-control-sm mb-2" onchange="this.form.submit()">
                                <option value="">Any reply</option>
                                <option value="yes" {{ request('has_reply') === 'yes' ? 'selected' : '' }}>With reply</option>
                                <option value="no" {{ request('has_reply') === 'no' ? 'selected' : '' }}>No reply</option>
                            </select>
                        </div>
                    </div>

                    {{-- Date range --}}
                    <div class="btn-group">
                        @php
                            $dateLabel = '';
                            if (request('date_from') && request('date_to')) $dateLabel = ': '.request('date_from').' → '.request('date_to');
                            elseif (request('date_from')) $dateLabel = ': from '.request('date_from');
                            elseif (request('date_to')) $dateLabel = ': to '.request('date_to');
                        @endphp
                        <button type="button" class="btn btn-sm {{ ($dateLabel !== '') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                            Date{{ $dateLabel }} <i class="fa fa-chevron-down filter-caret"></i>
                        </button>
                        <div class="dropdown-menu p-2" style="min-width:260px;">
                            <label class="small mb-1">From</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm mb-2">
                            <label class="small mb-1">To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm mb-2">
                            <button type="submit" class="btn btn-sm btn-primary btn-block">Apply</button>
                        </div>
                    </div>

                    @if($activeCount > 0)
                        <a href="{{ route('reviews.index') }}" class="btn btn-sm btn-outline-danger" title="Reset all filters">
                            <i class="fa fa-times"></i> Reset ({{ $activeCount }})
                        </a>
                    @endif

                    <div class="ml-auto d-flex align-items-center">
                        <label class="mr-2 mb-0 small text-muted">Per page</label>
                        <select name="perPage" class="form-control form-control-sm" style="width:auto;" onchange="this.form.submit()">
                            <option value="10" {{ $pp==10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $pp==25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $pp==50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $pp==100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </form>
            </div>

            <style>
                .q-filter-bar .dropdown-menu { padding: 10px; }
                .q-filter-bar .dropdown-menu input,
                .q-filter-bar .dropdown-menu select { cursor: auto; }
                .q-filter-bar .dropdown-toggle::after { display: none !important; }
                .q-filter-bar .filter-caret {
                    display: inline-block;
                    margin-left: 6px;
                    font-size: 10px;
                }
                .q-filter-bar .btn-outline-dark {
                    color: #000 !important;
                    border-color: #6c757d !important;
                    background: #fff !important;
                    font-weight: 500;
                }
                .q-filter-bar .btn-outline-dark:hover,
                .q-filter-bar .btn-outline-dark:focus {
                    color: #000 !important;
                    background: #f1f3f5 !important;
                    border-color: #343a40 !important;
                }
                .q-filter-bar .btn-outline-dark .filter-caret { color: #000; opacity: 0.75; }
                .q-filter-bar .btn-primary { color: #fff !important; font-weight: 500; }
                .q-filter-bar .btn-primary .filter-caret { color: #fff; opacity: 0.9; }
            </style>
            <script>
                (function() {
                    document.querySelectorAll('.q-filter-bar .dropdown-menu').forEach(function(m) {
                        m.addEventListener('click', function(e) { e.stopPropagation(); });
                    });
                })();
            </script>

            <div class="card-body">


    <table class="table mt-3 mb-3">
        <thead>
            <tr>
                <th>#</th>
                <th>User ID</th>
                <th>Profile ID</th>
                <th>Email</th>
                <th>Review</th>
                <th>Reply</th>
                <th>Star</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reviews as $review)
                <tr>
                    <td>{{ $review->id }}</td>
                    <td>{{ $review->user_id }}</td>
                    <td>{{ $review->profile_id }}</td>
                    <td>{{ $review->user->email ?? '-' }}</td>
                    <td>
                        <div class="review-preview" style="max-width: 300px;">
                            {{ Str::limit($review->review, 100) }}
                            @if(strlen($review->review) > 100)
                                <a href="javascript:void(0)" class="text-primary" onclick="showReviewModal({{ $review->id }}, `{{ addslashes($review->review) }}`, `{{ addslashes($review->reply ?? '') }}`)">
                                    <small>Read more...</small>
                                </a>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($review->reply)
                            <div class="reply-preview" style="max-width: 200px;">
                                {{ Str::limit($review->reply, 50) }}
                                @if(strlen($review->reply) > 50)
                                    <a href="javascript:void(0)" class="text-primary" onclick="showReviewModal({{ $review->id }}, `{{ addslashes($review->review) }}`, `{{ addslashes($review->reply) }}`)">
                                        <small>Read more...</small>
                                    </a>
                                @endif
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $review->star }}</td>
                    <td>
                        @if($review->status == 1)
                            <span class="badge badge-success">Approved</span>
                        @else
                            <span class="badge badge-warning">Pending</span>
                        @endif
                    </td>
                    <td>{{ optional($review->created_at)->format('Y-m-d H:i') ?? '-' }}</td>
                    <td>
                        <a href="javascript:void(0)" class="btn btn-info btn-sm" onclick="showReviewModal({{ $review->id }}, `{{ addslashes($review->review) }}`, `{{ addslashes($review->reply ?? '') }}`)">
                            <i class="fa fa-eye"></i> View
                        </a>
                        @if($review->status == 0)
                        <form action="{{ route('reviews.approve', $review->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-success btn-sm"><i class="fa-regular fa-floppy-disk"></i> Approve</button>
                        </form>
                        @endif
                        @if($review->status == 1)
                        <form action="{{ route('reviews.disapprove', $review->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-warning btn-sm">Disapprove</button>
                        </form>
                        @endif
                        <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash-can"></i> Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small">
            Showing {{ $reviews->firstItem() }} to {{ $reviews->lastItem() }} of {{ $reviews->total() }} entries
        </div>
        <div>
            {{ $reviews->appends(request()->query())->links() }}
        </div>
    </div>
    </div>
               
           
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Review Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h6 class="font-weight-bold">Review:</h6>
                    <div class="p-3 bg-light rounded" id="modalReviewText" style="white-space: pre-wrap;color:black"></div>
                </div>
                <div id="modalReplySection" style="display: none;">
                    <h6 class="font-weight-bold">Reply:</h6>
                    <div class="p-3 bg-light rounded" id="modalReplyText" style="white-space: pre-wrap;color:black"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showReviewModal(id, review, reply) {
    document.getElementById('modalReviewText').textContent = review;
    
    if (reply && reply.trim() !== '') {
        document.getElementById('modalReplyText').textContent = reply;
        document.getElementById('modalReplySection').style.display = 'block';
    } else {
        document.getElementById('modalReplySection').style.display = 'none';
    }
    
    $('#reviewModal').modal('show');
}
</script>

@endsection