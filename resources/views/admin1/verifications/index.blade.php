@extends('admin.layout.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card row">
                <div class="card-header">
                    <h4 class="card-title">Pending Photo Verifications</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="verificationTable">
                            <thead>
                                <tr>
                                    <th>Profile</th>
                                    <th>Verification Photo</th>
                                    <th>Profile Photos</th>
                                    <th>Photo Code</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($photos as $photo)
                                    <tr id="row-{{$photo->id}}">
                                        <td>
                                            <a href="/{{ strtolower($photo->profile->ggender->name) }}-escorts-in-{{ strtolower($photo->profile->gcity->name) }}/{{ $photo->profile->id }}/{{ $photo->profile->name }}" target="_blank">
                                                {{ $photo->profile->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <img style="    width: 86px;
    max-height: 135px;
    object-fit: contain;
}" src="{{ Storage::url('userimages/'.$photo->user_id.'/verification/'.$photo->photo) }}" 
                                                 class="img-preview" 
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#photoModal{{ $photo->id }}">
                                        </td>
                                        <td>
                                            <div class="profile-photos">
                                         
                                                @if($photo->profile && $photo->profile->singleimg->image)
                                                <img src="{{smart_asset("storage/userimages/".$photo->profile->user_id."/".$photo->profile->singleimg->image)}}" 
                                                class="img-thumbnail">
                                                @else
                                                    <span class="text-muted">No profile images</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td> <div class="badge bg-warning " style="font-size:20px" >{{$photo->profile->photo_code}}</span></td>
                                        <td>{{ $photo->created_at->diffForHumans() }}</td>
                                        <td>
                                            <button class="btn btn-success btn-sm approve-btn" 
                                                    data-id="{{ $photo->id }}">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            
                                            <button class="btn btn-danger btn-sm reject-btn" 
                                                    data-id="{{ $photo->id }}">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        {{ $photos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($photos as $photo)
<div class="modal fade" id="photoModal{{ $photo->id }}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <img src="{{ Storage::url('userimages/'.$photo->user_id.'/verification/'.$photo->photo) }}" 
                     class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('css')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
.img-preview {
    max-width: 200px;
    cursor: pointer;
}
.profile-photos img {
    width: 100px;
    margin-right: 10px;
}
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        }
    });


    $('.approve-btn').click(function() {
        const id = $(this).data('id');
        const row = $(`#row-${id}`);
        
        $.ajax({
            url: `/admin/verifications/${id}/approve`,
            type: 'POST',
            success: function() {
                row.fadeOut();
                toastr.success('Photo verified successfully');
            }
        });
    });

    $('.reject-btn').click(function() {
    const id = $(this).data('id');
    const row = $(`#row-${id}`);
    
    Swal.fire({
        title: 'Rejection Reason',
        input: 'text',
        inputPlaceholder: 'Enter reason for rejection',
        showCancelButton: true,
        confirmButtonText: 'Reject',
        confirmButtonColor: '#d33',
        showLoaderOnConfirm: true,
        preConfirm: (reason) => {
            return $.ajax({
                url: `/admin/verifications/${id}/reject`,
                type: 'POST',
                data: {reason: reason}
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            row.fadeOut();
            toastr.success('Photo has been rejected');
        }
    }).catch(error => {
        toastr.error('Error rejecting photo');
    });
});
});
</script>
@endpush