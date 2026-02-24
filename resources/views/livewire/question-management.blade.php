<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Questions Management</h4>
                    <div class="search-box">
                        <input wire:model.live="search" type="text" class="form-control" placeholder="Search questions...">
                    </div>
                </div>
                
                <div class="card-body">
                 

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Profile</th>
                                    <th>Question</th>
                                    <th>Status</th>
                                    <th>Answer</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questions as $question)
                                <tr>
                                    <td>{{ $question->getuser->name }}</td>
                                    <td>{{ $question->profile->name ?? ''}}</td>
                                    <td>{{ $question->question }}</td>
                                    <td>
                                        <span class="badge {{ $question->answer_status == '1' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $question->answer_status == 1 ? 'Answered' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td>{{ $question->answer }}</td>
                                    <td>
                                        @if(!$question->answer)
                                        <button type="button" 
                                        class="btn btn-primary btn-sm" 
                                        onclick="openAnswerModal({{$question->id}})"
                                        wire:click="$set('questionId', {{$question->id}})">
                                    Answer
                                </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{ $questions->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Answer Modal -->
    

    <div class="modal fade" id="answerModal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Answer</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form wire:submit.prevent="submitAnswer">
                    <div class="modal-body">
                        @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible show">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif
                        <textarea wire:model.defer="answer" 
                                  class="form-control" 
                                  rows="4" 
                                  required>{{$question->answer}}</textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


</div>

<!-- At the bottom of the file add -->
@push('js')
<script>
function openAnswerModal(id) {
    $('#answerModal').modal('show');
}

// Handle modal submission
$('#answerModal').on('hidden.bs.modal', function () {
    @this.reset('answer');
});

window.addEventListener('closeModal', event => {
    $('#answerModal').modal('hide');
});
</script>
@endpush