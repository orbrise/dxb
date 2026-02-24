@extends('admin.layout.master')

@section('content')
 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Questions</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage questions effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Questions</li>
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
                    <h5 class="card-title mb-0">Questions</h5>
                    <form method="GET" action="{{ route('questions.index') }}" class="form-inline">
                        <label class="mr-2">Per page</label>
                        @php($pp = request('perPage', $questions->perPage()))
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
<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>User ID</th>
            <th>Profile ID</th>
            <th>Question</th>
            <th>Answer</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($questions as $question)
        <tr>
            <td>{{ $question->id }}</td>
            <td>{{ $question->user_id }}</td>
            <td>{{ $question->profile_id }}</td>
            <td>
                <div class="question-preview" style="max-width: 300px;">
                    {{ Str::limit($question->question, 100) }}
                    @if(strlen($question->question) > 100)
                        <a href="javascript:void(0)" class="text-primary" onclick="showQuestionModal({{ $question->id }}, `{{ addslashes($question->question) }}`, `{{ addslashes($question->answer ?? '') }}`)">
                            <small>Read more...</small>
                        </a>
                    @endif
                </div>
            </td>
            <td>
                @if($question->answer)
                    <div class="answer-preview" style="max-width: 200px;">
                        {{ Str::limit($question->answer, 50) }}
                        @if(strlen($question->answer) > 50)
                            <a href="javascript:void(0)" class="text-primary" onclick="showQuestionModal({{ $question->id }}, `{{ addslashes($question->question) }}`, `{{ addslashes($question->answer) }}`)">
                                <small>Read more...</small>
                            </a>
                        @endif
                    </div>
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>
            <td>
                @if($question->status == 1)
                    <span class="badge badge-success">Approved</span>
                @else
                    <span class="badge badge-warning">Pending</span>
                @endif
            </td>
            <td>
                <a href="javascript:void(0)" class="btn btn-info btn-sm" onclick="showQuestionModal({{ $question->id }}, `{{ addslashes($question->question) }}`, `{{ addslashes($question->answer ?? '') }}`)">
                    <i class="fa fa-eye"></i> View
                </a>
                @if($question->status == 0)
                <form action="{{ route('questions.approve', $question->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-success btn-sm"><i class="fa-regular fa-floppy-disk"></i> Approve</button>
                </form>
                @endif
                @if($question->status == 1)
                <form action="{{ route('questions.disapprove', $question->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-warning btn-sm">Disapprove</button>
                </form>
                @endif
                <form action="{{ route('questions.destroy', $question->id) }}" method="POST" style="display: inline;">
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
         Showing {{ $questions->firstItem() }} to {{ $questions->lastItem() }} of {{ $questions->total() }} entries
     </div>
     <div>
         {{ $questions->appends(request()->query())->links() }}
     </div>
 </div>
 </div>
               
           
        </div>
    </div>
</div>

<!-- Question Modal -->
<div class="modal fade" id="questionModal" tabindex="-1" role="dialog" aria-labelledby="questionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="questionModalLabel">Question Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h6 class="font-weight-bold">Question:</h6>
                    <div class="p-3 bg-light rounded" id="modalQuestionText" style="white-space: pre-wrap;color:black"></div>
                </div>
                <div id="modalAnswerSection" style="display: none;">
                    <h6 class="font-weight-bold">Answer:</h6>
                    <div class="p-3 bg-light rounded" id="modalAnswerText" style="white-space: pre-wrap;color:black"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showQuestionModal(id, question, answer) {
    document.getElementById('modalQuestionText').textContent = question;
    
    if (answer && answer.trim() !== '') {
        document.getElementById('modalAnswerText').textContent = answer;
        document.getElementById('modalAnswerSection').style.display = 'block';
    } else {
        document.getElementById('modalAnswerSection').style.display = 'none';
    }
    
    $('#questionModal').modal('show');
}
</script>

@endsection