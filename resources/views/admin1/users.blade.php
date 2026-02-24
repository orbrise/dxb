@extends("admin.layout.master")

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="{{smart_asset('admin/assets/libs/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />

<style>
  span.input { display: none; }
</style>
@endpush

@section("content")

 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Users</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage users effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>


<!-- Add User Form -->
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Add New User</h5>
            </div>
            <div class="card-body">
                <form method="post" action="{{route('admin.adduser')}}">
                    {{csrf_field()}}
                    <div class="mb-3">
                        <label for="userName" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Enter user name">
                        @error("name")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Enter user email">
                        @error("email")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="userPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Enter password">
                        @error("password")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Users List -->
@if(!empty($users))
<div class="row mt-3 mb-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Users</h5>
            </div>
            <div class="card-body">
                <table id="datatable_1" class="table table-bordered dt-responsive nowrap">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Profiles</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr id="row{{$user->id}}">
                            <td>{{$user->name}}</td>
                            <td>{{$user->email}}</td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="showProfiles({{$user->id}})">View Profiles</button>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-user" data-id="{{$user->id}}">Edit</button>
                                <button class="btn btn-danger btn-sm delete-user" data-id="{{$user->id}}">Delete</button>
                                <button class="btn btn-success btn-sm impersonate-user" data-id="{{$user->id}}" data-name="{{$user->name}}">
                                    Login as User
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<!-- User Profiles Modal -->
<div class="modal fade" id="profilesModal" tabindex="-1" aria-labelledby="profilesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profilesModalLabel">User Profiles</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Add Profile Form -->
                <div id="addProfileSection" class="mb-4" style="display: none;">
                    <form id="addProfileForm">
                         @csrf
                        <input type="hidden" name="user_id" id="profileUserId">
                        <div class="row">
                            
                            <div class="col-md-4">
                                <label for="profileName" class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" id="profileName" required>
                            </div>
                            <div class="col-md-4">
                                <label for="profileCity" class="form-label">City</label>
                                <input type="text" class="form-control" name="city" id="profileCity">
                            </div>
                            <div class="col-md-4">
                                <label for="profilePhone" class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" id="profilePhone">
                            </div>
                            <div class="col-md-4">
                                <label for="profileGender" class="form-label">Gender</label>
                                <select class="form-select" name="gender" id="profileGender">
                                    <option value="" selected disabled>Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Save Profile</button>
                    </form>
                </div>

                <!-- Profiles List -->
                <div class="d-flex justify-content-between">
                    <h5>Profiles</h5>
                    <button class="btn btn-success btn-sm" onclick="showAddProfileForm()">Add Profile</button>
                </div>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>City</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="profilesBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editProfileForm">
                @csrf
                <input type="hidden" name="id" id="editProfileId">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editName" class="form-label">Name</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editCity" class="form-label">City</label>
                        <input type="text" name="city" id="editCity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editGender" class="form-label">Gender</label>
                        <input type="text" name="gender" id="editGender" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPhone" class="form-label">Phone</label>
                        <input type="text" name="phone" id="editPhone" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="assignPackageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Package</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignPackageForm">
                    <input type="hidden" id="profile_id" name="profile_id">
                    <div class="mb-3">
                        <label class="form-label">Select Package</label>
                        <select class="form-select" name="package_id" id="package_id">
                            <option value="">Select Package</option>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}">{{ $package->name }} - {{ $package->price }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                            <label class="form-check-label">Featured Profile</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="savePackageAssignment">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    @csrf
                    <input type="hidden" id="editUserId" name="id">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="editUserName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="editUserEmail" name="email" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveUserEdit">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
var token = "{{ csrf_token() }}";

// Show Add Profile Form
function showAddProfileForm() {
    $('#addProfileSection').slideToggle(); // Toggle the form display
}

// Submit New Profile
$("#addProfileForm").submit(function (e) {
    e.preventDefault();

    var formData = $(this).serialize(); // Serialize form inputs
    $.post("{{ route('admin.addprofile') }}", formData, function (response) {
        if (response.success) {
            alert(response.success);
            $('#addProfileForm')[0].reset(); // Reset form
            showProfiles($("#profileUserId").val()); // Reload profiles
        }
    }).fail(function (xhr) {
        alert("Error: " + xhr.responseJSON.message);
    });
});

// Load Profiles
function showProfiles(userId) {
    $("#profileUserId").val(userId); // Set user_id for form
    $("input#user_id").val(userId);
    $.post("{{ route('admin.getprofiles') }}", { _token: token, user_id: userId }, function (data) {
        $('#profilesBody').html(data); // Update profiles list
        console.log(data);
        $('#profilesModal').modal('show');
    });
}

// Delete User
{{-- $(document).on('click', '.delete-profile', function () {
    var profileId = $(this).data('id');
    if (confirm("Are you sure you want to delete this profile?")) {
         $.post("{{ route('admin.deleteprofile') }}", {_token: token, id: profileId}, function (response) {
            if (response.success) {
                alert(response.success);
                showProfiles($("#profileUserId").val()); // Reload profiles
            }
        }).fail(function (xhr) {
            alert("Error: " + xhr.responseJSON.message);
        });
    } 
});--}}

$(document).on('click', '.edit-profile', function () {
    var profileId = $(this).data('id');
    $.get("{{ route('admin.getprofile') }}", {id: profileId}, function (profile) {
        $("#editProfileId").val(profile.id);
        $("#editName").val(profile.name);
        $("#editCity").val(profile.city);
        $("#editGender").val(profile.gender);
        $("#editPhone").val(profile.phone);
        $('#editProfileModal').modal('show'); // Show modal
    }).fail(function () {
        alert("Failed to fetch profile data.");
    });
});

$("#editProfileForm").submit(function (e) {
    e.preventDefault();

    var formData = $(this).serialize();
    {{-- $.post("{{ route('admin.updateprofile') }}", formData, function (response) {
        if (response.success) {
            alert(response.success);
            $('#editProfileModal').modal('hide'); // Close modal
            showProfiles($("#profileUserId").val()); // Reload profiles
        }
    }).fail(function (xhr) {
        alert("Error: " + xhr.responseJSON.message);
    }); --}}
});
    // Edit User (similar logic can be applied for UserProfile CRUD)

    $(document).on('click', '.assign-package', function() {
    var profileId = $(this).data('id');
    var isFeatured = $(this).data('featured');
    var packageId = $(this).data('package');
    
    $('#profile_id').val(profileId);
    $('#package_id').val(packageId);
    $('#is_featured').prop('checked', isFeatured == 1);
    
    $('#assignPackageModal').modal('show');
});

$('#savePackageAssignment').click(function() {
    var formData = {
        _token: token,
        profile_id: $('#profile_id').val(),
        package_id: $('#package_id').val(),
        is_featured: $('#is_featured').is(':checked') ? 1 : 0
    };

    $.post("{{ route('admin.assignpackage') }}", formData, function(response) {
        if(response.success) {
            $('#assignPackageModal').modal('hide');
            showProfiles($("#profileUserId").val());
            alert(response.success);
        }
    });
});

$(document).on('click', '.edit-user', function() {
    var userId = $(this).data('id');
    var row = $('#row' + userId);
    var name = row.find('td:eq(0)').text();
    var email = row.find('td:eq(1)').text();
    
    $('#editUserId').val(userId);
    $('#editUserName').val(name);
    $('#editUserEmail').val(email);
    $('#editUserModal').modal('show');
});

$('#saveUserEdit').click(function() {
    var userId = $('#editUserId').val();
    var formData = {
        _token: token,
        id: userId,
        name: $('#editUserName').val(),
        email: $('#editUserEmail').val()
    };

    $.ajax({
        url: "{{ route('admin.updateuser', '') }}/" + userId,
        type: 'POST',
        data: formData,
        success: function(response) {
            $('#editUserModal').modal('hide');
            var row = $('#row' + userId);
            row.find('td:eq(0)').text(formData.name);
            row.find('td:eq(1)').text(formData.email);
            alert(response.success);
        },
        error: function(xhr) {
            alert("Error: " + xhr.responseJSON.message);
        }
    });
});

$(document).on('click', '.delete-user', function() {
    var userId = $(this).data('id');
    
    if(confirm('Are you sure you want to delete this user?')) {
        $.ajax({
            url: "{{ route('admin.deleteuser') }}",
            type: 'POST',
            data: {
                _token: token,
                id: userId
            },
            success: function(response) {
                $('#row' + userId).remove();
                alert('User deleted successfully');
            },
            error: function(xhr) {
                alert("Error: " + xhr.responseJSON.message);
            }
        });
    }
});

// Impersonate user functionality
$(document).on('click', '.impersonate-user', function() {
    var userId = $(this).data('id');
    var userName = $(this).data('name');
    
    if(confirm('Are you sure you want to login as "' + userName + '"? This will open a new window where you will be logged in as this user.')) {
        // Create a form and submit it to open in new window
        var form = $('<form>', {
            'method': 'POST',
            'action': "{{ route('admin.impersonate') }}",
            'target': '_blank'
        });
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': token
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'user_id',
            'value': userId
        }));
        
        $('body').append(form);
        form.submit();
        form.remove();
    }
});

// Impersonate user via profile functionality
$(document).on('click', '.impersonate-profile', function() {
    var userId = $(this).data('user-id');
    var profileId = $(this).data('profile-id');
    var profileName = $(this).data('name');
    
    if(confirm('Are you sure you want to login as "' + profileName + '" profile? This will open a new window where you will be logged in as this user.')) {
        // Create a form and submit it to open in new window
        var form = $('<form>', {
            'method': 'POST',
            'action': "{{ route('admin.impersonate') }}",
            'target': '_blank'
        });
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': token
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'user_id',
            'value': userId
        }));
        
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'profile_id',
            'value': profileId
        }));
        
        $('body').append(form);
        form.submit();
        form.remove();
    }
});

</script>
@endpush