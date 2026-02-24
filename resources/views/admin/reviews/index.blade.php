@extends('admin.layout.master')

@section('content')
 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Reviews</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage questions effectively</p>
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

            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Reviews</h5>
                    <form method="GET" action="{{ route('reviews.index') }}" class="form-inline">
                        <label class="mr-2">Per page</label>
                        @php($pp = request('perPage', $reviews->perPage()))
                        <select name="perPage" class="form-control" onchange="this.form.submit()">
                            <option value="10" {{ $pp==10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $pp==25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $pp==50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $pp==100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                </div>
            </div><!-- end card header -->

            <div class="card-body">


    <table class="table mt-3 mb-3">
        <thead>
            <tr>
                <th>#</th>
                <th>User ID</th>
                <th>Profile ID</th>
                <th>Review</th>
                <th>Reply</th>
                <th>Star</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reviews as $review)
                <tr>
                    <td>{{ $review->id }}</td>
                    <td>{{ $review->user_id }}</td>
                    <td>{{ $review->profile_id }}</td>
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