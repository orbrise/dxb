@extends("admin.layout.master")

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="{{smart_asset('admin/assets/libs/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css" rel="stylesheet" />

<style>
  span.input { display: none; }
  .dataTables_wrapper .dataTables_filter input {
    margin-left: 0.5em;
  }
  .dataTables_wrapper .dataTables_length select {
    padding: 4px 30px 4px 10px;
    min-width: 80px;
    width: auto;
  }

  @media (min-width: 576px) {
    .modal-dialog {
        max-width: 800px;
    }
}
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

<!-- Add User Button -->
<div class="row mt-3">
    <div class="col-lg-12">
        <button type="button" class="btn btn-primary" id="addUserBtn">
            <i class="fa fa-plus"></i> Add New User
        </button>
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
                <table id="usersTable" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th>Verified Email</th>
                            <th>Profiles</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr id="row{{$user->id}}">
                            <td>{{ $user->created_at ? $user->created_at->format('Y-m-d H:i') : 'N/A' }}</td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->email}}</td>
                            <td>
                                @if($user->country)
                                    <img src="https://flagcdn.com/16x12/{{ strtolower($user->country) }}.png" alt="{{ $user->country }}" style="margin-right: 5px;">
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge status-badge-{{$user->id}} {{ $user->status == 'active' ? 'bg-success' : 'bg-warning' }}">
                                    {{ ucfirst($user->status ?? 'pending') }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($user->verified == 1)
                                    <span class="badge bg-success">✓</span>
                                @else
                                    <span class="badge bg-danger">✗</span>
                                @endif
                                @if($user->google_id)
                                    <img src="https://www.google.com/favicon.ico" alt="Google" style="width: 16px; height: 16px; margin-left: 5px;" title="Google Account">
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="showProfiles({{$user->id}})">
                                    <i class="fa fa-eye"></i> View
                                </button>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton{{$user->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions <i class="fa fa-chevron-down"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{$user->id}}">
                                        <a class="dropdown-item edit-user" href="javascript:void(0)" data-id="{{$user->id}}">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <a class="dropdown-item toggle-status" href="javascript:void(0)" 
                                           data-id="{{$user->id}}" 
                                           data-status="{{$user->status}}">
                                            <i class="fa {{ $user->status == 'active' ? 'fa-ban' : 'fa-check-circle' }}"></i> 
                                            {{ $user->status == 'active' ? 'Deactivate' : 'Activate' }}
                                        </a>
                                        @if(!$user->email_verified_at && !$user->verified)
                                        <a class="dropdown-item send-verification" href="javascript:void(0)" data-id="{{$user->id}}" data-email="{{$user->email}}">
                                            <i class="fa fa-envelope"></i> Send Verification
                                        </a>
                                        @endif
                                        <a class="dropdown-item impersonate-user" href="javascript:void(0)" data-id="{{$user->id}}" data-name="{{$user->name}}">
                                            <i class="fa fa-sign-in"></i> Login as User
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger delete-user" href="javascript:void(0)" data-id="{{$user->id}}">
                                            <i class="fa fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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
                        <button type="submit" class="btn btn-primary mt-3"><i class="fa-solid fa-floppy-disk"></i> Save Profile</button>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="assignPackageForm">
                    <input type="hidden" id="profile_id" name="profile_id">
                    <div class="mb-3">
                        <label class="form-label">Select Package</label>
                        <select class="form-select form-control" name="package_id" id="package_id">
                            <option value="">Select Package</option>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}" 
                                        data-price="{{ $package->price }}" 
                                        data-days="{{ $package->promo_days }}">
                                    {{ $package->name }} - ${{ $package->price }} ({{ $package->promo_days }} days)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3" id="packageDetails" style="display: none;">
                        <div class="alert alert-info">
                            <strong>Package Details:</strong><br>
                            <span id="packageName"></span><br>
                            <strong>Price:</strong> $<span id="packagePrice"></span><br>
                            <strong>Duration:</strong> <span id="packageDays"></span> days
                        </div>
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="savePackageAssignment">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    @csrf
                    <input type="hidden" id="editUserId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editUserName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="editUserEmail" name="email" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Account Type</label>
                                <select class="form-control" id="editUserType" name="type">
                                    <option value="2">Individual advertiser</option>
                                    <option value="3">Agency</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" id="editUserStatus" name="status">
                                    <option value="pending">Pending</option>
                                    <option value="active">Active</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email Verified</label>
                                <select class="form-control" id="editUserVerified" name="verified">
                                    <option value="0">Not Verified</option>
                                    <option value="1">Verified</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Registration Country</label>
                                <input type="text" class="form-control" id="editUserRegistrationCountry" readonly disabled>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Country Code</label>
                                <select class="form-control" id="editUserCountryCode" name="country_code">
                                    <option value="">Select</option>
                                    @foreach($countries as $country)
                                        <option value="+{{ $country->phonecode }}">+{{ $country->phonecode }} ({{ $country->iso }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="editUserPhone" name="phone" placeholder="Phone number">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">About</label>
                        <textarea class="form-control" id="editUserAbout" name="about" rows="3" placeholder="User bio or description"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Created At</label>
                                <input type="text" class="form-control" id="editUserCreatedAt" readonly disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Google Account</label>
                                <input type="text" class="form-control" id="editUserGoogleId" readonly disabled>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Registration IP</label>
                                <input type="text" class="form-control" id="editUserRegistrationIp" readonly disabled>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h6 class="mb-3"><i class="fa fa-key"></i> Password Management</h6>
                    
                    <div class="alert alert-info py-2 mb-3">
                        <i class="fa fa-info-circle"></i> <strong>Note:</strong> Passwords are encrypted and cannot be viewed. You can only set a new password.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Set New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="editUserNewPassword" name="new_password" placeholder="Leave blank to keep current">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword" title="Show/Hide Password">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="editUserConfirmPassword" name="confirm_password" placeholder="Confirm new password">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-warning" id="generateRandomPassword">
                                <i class="fa fa-random"></i> Generate Random Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveUserEdit">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{route('admin.adduser')}}" id="addUserForm">
                    {{csrf_field()}}
                    <div class="mb-3">
                        <label for="userName" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Enter user name" required>
                        @error("name")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Enter user email" required>
                        @error("email")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="userPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Enter password" required>
                        @error("password")
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="addUserForm" class="btn btn-primary">Submit</button>
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
        console.log('Profiles data received:', data);
        console.log('Data type:', typeof data);
        console.log('Data length:', data.length);
        
        if (data && data.trim() !== '') {
            $('#profilesBody').html(data); // Update profiles list
        } else {
            $('#profilesBody').html('<tr><td colspan="5" class="text-center">No profiles found</td></tr>');
        }
        
        $('#profilesModal').modal('show');
    }).fail(function(xhr, status, error) {
        console.error('Error loading profiles:', error);
        console.error('Response:', xhr.responseText);
        $('#profilesBody').html('<tr><td colspan="5" class="text-center text-danger">Error loading profiles</td></tr>');
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
        $('#editProfileModal').modal('show');
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
            $('#editProfileModal').modal('hide');
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
    
    // Show package details if a package is already assigned
    if (packageId) {
        var selectedOption = $('#package_id option:selected');
        if (selectedOption.length) {
            $('#packageName').text(selectedOption.text());
            $('#packagePrice').text(selectedOption.data('price'));
            $('#packageDays').text(selectedOption.data('days'));
            $('#packageDetails').show();
        }
    }
    
    $('#assignPackageModal').modal('show');
});

// Update package details when selection changes
$('#package_id').change(function() {
    var selectedOption = $(this).find('option:selected');
    if (selectedOption.val()) {
        $('#packageName').text(selectedOption.text());
        $('#packagePrice').text(selectedOption.data('price'));
        $('#packageDays').text(selectedOption.data('days'));
        $('#packageDetails').show();
    } else {
        $('#packageDetails').hide();
    }
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
    }).fail(function(xhr) {
        alert('Error: ' + (xhr.responseJSON?.message || 'Failed to assign package'));
    });
});

$(document).on('click', '.edit-user', function() {
    var userId = $(this).data('id');
    
    // Fetch user data via AJAX
    $.ajax({
        url: "{{ route('admin.users') }}",
        type: 'GET',
        data: { id: userId },
        success: function(response) {
            // Find the user in the response
            var row = $('#row' + userId);
            
            // Make another AJAX call to get full user details
            $.ajax({
                url: '/admin/getuser/' + userId,
                type: 'GET',
                success: function(user) {
                    $('#editUserId').val(user.id);
                    $('#editUserName').val(user.name);
                    $('#editUserEmail').val(user.email);
                    $('#editUserType').val(user.type || '1');
                    $('#editUserStatus').val(user.status || 'pending');
                    $('#editUserVerified').val(user.verified || '0');
                    // Normalize country code - add + prefix if not present
                    var countryCode = user.country_code || '';
                    if (countryCode && !countryCode.startsWith('+')) {
                        countryCode = '+' + countryCode;
                    }
                    $('#editUserCountryCode').val(countryCode);
                    $('#editUserPhone').val(user.phone || '');
                    $('#editUserAbout').val(user.about || '');
                    $('#editUserCreatedAt').val(user.created_at ? new Date(user.created_at).toLocaleString() : 'N/A');
                    $('#editUserGoogleId').val(user.google_id ? 'Connected' : 'Not Connected');
                    $('#editUserRegistrationIp').val(user.registration_ip || 'N/A');
                    $('#editUserRegistrationCountry').val(user.registration_country || 'N/A');
                    
                    // Show password hash (first 20 chars) for reference
                    if (user.password_hash) {
                        $('#editUserCurrentPassword').val(user.password_hash);
                        $('#passwordHashInfo').text('Encrypted: ' + user.password_hash.substring(0, 15) + '...');
                    } else {
                        $('#editUserCurrentPassword').val('••••••••');
                        $('#passwordHashInfo').text('Password is set');
                    }
                    
                    // Clear new password fields
                    $('#editUserNewPassword').val('');
                    $('#editUserConfirmPassword').val('');
                    
                    $('#editUserModal').modal('show');
                },
                error: function() {
                    // Fallback to row data
                    var name = row.find('td:eq(1)').text();
                    var email = row.find('td:eq(2)').text();
                    var status = row.find('.status-badge-' + userId).text().trim().toLowerCase();
                    
                    $('#editUserId').val(userId);
                    $('#editUserName').val(name);
                    $('#editUserEmail').val(email);
                    $('#editUserStatus').val(status);
                    $('#editUserModal').modal('show');
                }
            });
        }
    });
});

$('#saveUserEdit').click(function() {
    var userId = $('#editUserId').val();
    var formData = {
        _token: token,
        id: userId,
        name: $('#editUserName').val(),
        email: $('#editUserEmail').val(),
        type: $('#editUserType').val(),
        status: $('#editUserStatus').val(),
        verified: $('#editUserVerified').val(),
        country_code: $('#editUserCountryCode').val(),
        phone: $('#editUserPhone').val(),
        about: $('#editUserAbout').val(),
        new_password: $('#editUserNewPassword').val()
    };
    
    // Validate password confirmation
    if (formData.new_password && formData.new_password !== $('#editUserConfirmPassword').val()) {
        alert('New password and confirmation do not match!');
        return;
    }
    
    if (formData.new_password && formData.new_password.length < 6) {
        alert('Password must be at least 6 characters!');
        return;
    }

    $.ajax({
        url: "{{ route('admin.updateuser', '') }}/" + userId,
        type: 'POST',
        data: formData,
        success: function(response) {
            $('#editUserModal').modal('hide');
            
            // Clear password fields
            $('#editUserNewPassword').val('');
            $('#editUserConfirmPassword').val('');
            
            // Update the table row
            var row = $('#row' + userId);
            row.find('td:eq(1)').text(formData.name);
            row.find('td:eq(2)').text(formData.email);
            
            // Update status badge
            var statusBadge = row.find('.status-badge-' + userId);
            statusBadge.removeClass('bg-success bg-warning bg-danger');
            if(formData.status === 'active') {
                statusBadge.addClass('bg-success');
            } else if(formData.status === 'suspended') {
                statusBadge.addClass('bg-danger');
            } else {
                statusBadge.addClass('bg-warning');
            }
            statusBadge.text(formData.status.charAt(0).toUpperCase() + formData.status.slice(1));
            
            // Update verified badge
            var verifiedCell = row.find('td:eq(5)');
            if(formData.verified == '1') {
                verifiedCell.find('.badge').removeClass('bg-danger').addClass('bg-success').text('✓');
            } else {
                verifiedCell.find('.badge').removeClass('bg-success').addClass('bg-danger').text('✗');
            }
            
            alert(response.success);
            location.reload(); // Reload to reflect all changes
        },
        error: function(xhr) {
            alert("Error: " + (xhr.responseJSON?.message || 'Failed to update user'));
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

// Send verification email
$(document).on('click', '.send-verification', function() {
    var userId = $(this).data('id');
    var userEmail = $(this).data('email');
    var button = $(this);
    
    if(confirm('Send verification email to ' + userEmail + '?')) {
        button.prop('disabled', true);
        button.html('<i class="fa fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: "{{ route('admin.sendverification') }}",
            type: 'POST',
            data: {
                _token: token,
                id: userId
            },
            success: function(response) {
                button.prop('disabled', false);
                button.html('<i class="fa fa-envelope"></i>');
                alert(response.success || 'Verification email sent successfully!');
            },
            error: function(xhr) {
                button.prop('disabled', false);
                button.html('<i class="fa fa-envelope"></i>');
                alert("Error: " + (xhr.responseJSON?.message || 'Failed to send verification email'));
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

// Add User Modal Trigger
$('#addUserBtn').click(function() {
    $('#addUserModal').modal('show');
});

// Toggle User Status (Activate/Deactivate)
$(document).on('click', '.toggle-status', function() {
    var userId = $(this).data('id');
    var currentStatus = $(this).data('status');
    var newStatus = currentStatus === 'active' ? 'pending' : 'active';
    var actionText = newStatus === 'active' ? 'activate' : 'deactivate';
    
    if(confirm('Are you sure you want to ' + actionText + ' this user?')) {
        $.ajax({
            url: "{{ route('admin.toggleuserstatus') }}",
            type: 'POST',
            data: {
                _token: token,
                id: userId,
                status: newStatus
            },
            success: function(response) {
                if(response.success) {
                    var row = $('#row' + userId);
                    
                    // Update button
                    var btn = $('button.toggle-status[data-id="' + userId + '"]');
                    btn.data('status', newStatus);
                    
                    if(newStatus === 'active') {
                        btn.removeClass('btn-success').addClass('btn-secondary');
                        btn.attr('title', 'Deactivate');
                        btn.html('<i class="fa fa-ban"></i>');
                    } else {
                        btn.removeClass('btn-secondary').addClass('btn-success');
                        btn.attr('title', 'Activate');
                        btn.html('<i class="fa fa-check-circle"></i>');
                    }
                    
                    // Update status badge
                    var badge = $('.status-badge-' + userId);
                    badge.removeClass('bg-success bg-warning');
                    badge.addClass(newStatus === 'active' ? 'bg-success' : 'bg-warning');
                    badge.text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                    
                    // Update email verified badge
                    var verifiedCell = row.find('td:eq(5)'); // Verified Email column
                    if(newStatus === 'active' && response.verified == 1) {
                        verifiedCell.html('<span class="badge bg-success">✓</span>');
                    } else {
                        verifiedCell.html('<span class="badge bg-danger">✗</span>');
                    }
                    
                    alert(response.success);
                }
            },
            error: function(xhr) {
                alert("Error: " + (xhr.responseJSON?.message || 'Failed to update status'));
            }
        });
    }
});

// Initialize DataTable
$(document).ready(function() {
    $('#usersTable').DataTable({
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "order": [[0, "desc"]], // Sort by date descending
        "columnDefs": [
            { "orderable": false, "targets": [6, 7] }, // Disable sorting for Profiles and Actions columns
            { "searchable": false, "targets": [6, 7] }
        ],
        "language": {
            "search": "Search:",
            "lengthMenu": "_MENU_ records per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "infoEmpty": "Showing 0 to 0 of 0 entries",
            "infoFiltered": "(filtered from _MAX_ total entries)",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        "responsive": true,
        "autoWidth": false
    });
    
    // Toggle password visibility for new password
    $('#toggleNewPassword').click(function() {
        var input = $('#editUserNewPassword');
        var icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Generate random password
    $('#generateRandomPassword').click(function() {
        var chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
        var password = '';
        for (var i = 0; i < 12; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        
        $('#editUserNewPassword').val(password).attr('type', 'text');
        $('#editUserConfirmPassword').val(password);
        $('#toggleNewPassword i').removeClass('fa-eye').addClass('fa-eye-slash');
        
        // Copy to clipboard
        navigator.clipboard.writeText(password).then(function() {
            alert('Generated password: ' + password + '\n\nPassword has been copied to clipboard!');
        }).catch(function() {
            alert('Generated password: ' + password + '\n\nPlease copy this password manually.');
        });
    });
    
    // Clear password fields when modal is closed
    $('#editUserModal').on('hidden.bs.modal', function() {
        $('#editUserNewPassword').val('').attr('type', 'password');
        $('#editUserConfirmPassword').val('');
        $('#toggleNewPassword i').removeClass('fa-eye-slash').addClass('fa-eye');
    });
});

</script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
@endpush