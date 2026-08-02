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

            @php
                $pp = request('perPage', $questions->perPage());
                $statusLabels = ['1' => 'Approved', '0' => 'Pending'];
                $answerLabels = ['yes' => 'Answered', 'no' => 'Unanswered'];
                $activeCount = collect(['id','user_id','profile_id','q','status','has_answer','date_from','date_to'])
                    ->filter(fn($k) => request()->filled($k))
                    ->count();
            @endphp

            <div class="card-header q-filter-bar py-2">
                <form method="GET" action="{{ route('questions.index') }}" id="qFiltersForm" class="d-flex align-items-center flex-wrap" style="gap:6px;">
                    <input type="hidden" name="perPage" value="{{ $pp }}">

                    <span class="text-muted mr-1"><i class="fa fa-filter"></i></span>

                    {{-- ID --}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm {{ request('id') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                            ID{{ request('id') ? ': '.request('id') : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                        </button>
                        <div class="dropdown-menu p-2" style="min-width:220px;">
                            <input type="number" name="id" value="{{ request('id') }}" class="form-control form-control-sm mb-2" placeholder="Question ID" min="1">
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

                    {{-- Search --}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm {{ request('q') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                            Search{{ request('q') ? ': "'.\Illuminate\Support\Str::limit(request('q'), 20).'"' : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                        </button>
                        <div class="dropdown-menu p-2" style="min-width:280px;">
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm mb-2" placeholder="Search question or answer...">
                            <button type="submit" class="btn btn-sm btn-primary btn-block">Apply</button>
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

                    {{-- Answer --}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm {{ request()->filled('has_answer') ? 'btn-primary' : 'btn-outline-dark' }} dropdown-toggle" data-toggle="dropdown">
                            Answer{{ request()->filled('has_answer') ? ': '.($answerLabels[request('has_answer')] ?? '') : '' }} <i class="fa fa-chevron-down filter-caret"></i>
                        </button>
                        <div class="dropdown-menu p-2" style="min-width:180px;">
                            <select name="has_answer" class="form-control form-control-sm mb-2" onchange="this.form.submit()">
                                <option value="">Any answer</option>
                                <option value="yes" {{ request('has_answer') === 'yes' ? 'selected' : '' }}>Answered</option>
                                <option value="no" {{ request('has_answer') === 'no' ? 'selected' : '' }}>Unanswered</option>
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
                        <a href="{{ route('questions.index') }}" class="btn btn-sm btn-outline-danger" title="Reset all filters">
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
                /* Force readable black text on inactive filter pills — the admin
                   theme was rendering btn-outline-* as low-contrast gray. */
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
                /* Active (filter applied) pills keep the solid blue treatment,
                   but ensure white text is readable too. */
                .q-filter-bar .btn-primary { color: #fff !important; font-weight: 500; }
                .q-filter-bar .btn-primary .filter-caret { color: #fff; opacity: 0.9; }
            </style>
            <script>
                (function() {
                    // Prevent Bootstrap 4 dropdown from closing when clicking inside
                    // its input (Bootstrap treats any menu click as "select" by default).
                    document.querySelectorAll('.q-filter-bar .dropdown-menu').forEach(function(m) {
                        m.addEventListener('click', function(e) { e.stopPropagation(); });
                    });
                })();
            </script>

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