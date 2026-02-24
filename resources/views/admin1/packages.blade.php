@extends("admin.layout.master")

@section("content")

 <div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Packages</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage packages effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Packages</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>



<div class="row mt-3 mb-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Create Package</h5>
            </div>
            <div class="card-body">
                <div id="successMessage" style="display:none;" class="alert alert-success alert-dismissible fade show">
                    Package created successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>

            
                <form id="packageForm" method="post" action="{{route('admin.addpackage')}}">


                    {{csrf_field()}}
                    <div class="mb-3">
                        <label class="form-label">Package Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tag Line</label>
                        <input type="text" class="form-control" name="tagline" required>
                    </div>
                    
                    <div id="priceTiers">
                        <div class="row price-tier mb-3">
                            <div class="col-md-5">
                                <label class="form-label">Promotion Days</label>
                                <input type="number" class="form-control" name="tiers[0][days]" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Price</label>
                                <input type="number" class="form-control" name="tiers[0][price]" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-success btn-add-tier form-control">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="desc" name="description"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Package</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Packages</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Package Name</th>
                                <th>Tag Line</th>
                                <th>Price Tiers</th>

                                <th>Description</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($packages as $package)
                            <tr id="row{{$package->id}}">
                                <td>{{$package->name}}</td>
                                <td>{{$package->tagline}}</td>
                                <td>
                                    @foreach(json_decode($package->price_tiers) as $tier)
                                        <div class="mb-1">{{$tier->days}} days - ${{$tier->price}}</div>
                                    @endforeach
                                </td>
                                <td>{!! $package->description !!}</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm editPackage" data-id="{{$package->id}}">Edit</button>
                                    <button type="button" class="btn btn-danger btn-sm deletePackage" data-id="{{$package->id}}">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Edit Package Modal -->
<div class="modal fade" id="editPackageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Package</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editPackageForm">
                    <input type="hidden" id="edit_package_id">
                    <div class="mb-3">
                        <label class="form-label">Package Name</label>
                        <input type="text" class="form-control" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tag Line</label>
                        <input type="text" class="form-control" id="edit_tagline" required>
                    </div>
                    
                    <div id="editPriceTiers">
                        <!-- Price tiers will be dynamically added here -->
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="savePackageChanges">Save changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
var token = "{{ csrf_token() }}";

$(document).ready(function() {
    let tierCount = 1;
    
    // Add new tier row
    $('.btn-add-tier').click(function() {
        const newTier = `
            <div class="row price-tier mb-3">
                <div class="col-md-5">
                    <label class="form-label">Promotion Days</label>
                    <input type="number" class="form-control" name="tiers[${tierCount}][days]" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Price</label>
                    <input type="number" class="form-control" name="tiers[${tierCount}][price]" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label d-block">&nbsp;</label>
                    <button type="button" class="btn btn-danger w-100 btn-remove-tier">Remove</button>
                </div>
            </div>`;
        $('#priceTiers').append(newTier);
        tierCount++;
    });

    // Remove tier row
    $(document).on('click', '.btn-remove-tier', function() {
        $(this).closest('.price-tier').remove();
    });

    // Edit Package
    $('.editPackage').click(function() {
        var id = $(this).data('id');
        
        $.get(`{{ url('admin/package') }}/${id}`, function(package) {



            $('#edit_package_id').val(package.id);
            $('#edit_name').val(package.name);
            $('#edit_tagline').val(package.tagline);
            $('#edit_description').val(package.description);
            
            // Clear existing tiers
            $('#editPriceTiers').html('<h6 class="mb-3">Pricing Tiers</h6>');
            
            // Add tier rows
            let tiers = JSON.parse(package.price_tiers);
            tiers.forEach((tier, index) => {
                $('#editPriceTiers').append(`
                    <div class="row price-tier mb-3">
                        <div class="col-md-5">
                            <label class="form-label">Promotion Days</label>
                            <input type="number" class="form-control" name="edit_tiers[${index}][days]" value="${tier.days}" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Price</label>
                            <input type="number" class="form-control" name="edit_tiers[${index}][price]" value="${tier.price}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label d-block">&nbsp;</label>
                            <button type="button" class="btn btn-danger w-100 btn-remove-tier">Remove</button>
                        </div>
                    </div>
                `);
            });
            
            // Add button for new tiers
            $('#editPriceTiers').append(`
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-success btn-add-edit-tier">Add More</button>
                    </div>
                </div>
            `);
            
            $('#editPackageModal').modal('show');
        });
    });

    // Add new tier in edit modal
    $(document).on('click', '.btn-add-edit-tier', function() {
        let editTierCount = $('#editPriceTiers .price-tier').length;
        $('#editPriceTiers').find('.row:last').before(`
            <div class="row price-tier mb-3">
                <div class="col-md-5">
                    <label class="form-label">Promotion Days</label>
                    <input type="number" class="form-control" name="edit_tiers[${editTierCount}][days]" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Price</label>
                    <input type="number" class="form-control" name="edit_tiers[${editTierCount}][price]" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label d-block">&nbsp;</label>
                    <button type="button" class="btn btn-danger w-100 btn-remove-tier">Remove</button>
                </div>
            </div>
        `);
    });

    // Save Package Changes
    $('#savePackageChanges').click(function() {
        let tiers = [];
        $('#editPriceTiers .price-tier').each(function() {
            tiers.push({
                days: $(this).find('input[name*="[days]"]').val(),
                price: $(this).find('input[name*="[price]"]').val()
            });
        });

        $.post("{{ route('admin.updatepackage') }}", {
            _token: token,
            rid: $('#edit_package_id').val(),
            name: $('#edit_name').val(),
            tagline: $('#edit_tagline').val(),
            description: $('#edit_description').val(),
            tiers: tiers
        }, function(response) {
            if(response == 'success') {
                location.reload();
            }
        });
    });

    // Delete Package
    $('.deletePackage').click(function() {
        if(confirm('Are you sure you want to delete this package?')) {
            var id = $(this).data('id');
            $.post("{{ route('admin.delpackage') }}", {
                _token: token,
                rid: id
            }, function(response) {
                if(response == 'success') {
                    $(`#row${id}`).remove();
                }
            });
        }
    });

    $('#packageForm').submit(function(e) {
    e.preventDefault();
    let formData = $(this).serialize();
    
    $.post($(this).attr('action'), formData, function(response) {
        if(response.success) {
            $('#successMessage').show();
            $('#packageForm')[0].reset();
            
            // Fetch and update packages table
            $.get("{{ route('admin.getallpackages') }}", function(packages) {
                let tableBody = '';
                packages.forEach(package => {
                    tableBody += `
                        <tr id="row${package.id}">
                            <td>${package.name}</td>
                             <td>${package.tagline}</td>
                            <td>
                                ${JSON.parse(package.price_tiers).map(tier => 
                                    `<div class="mb-1">${tier.days} days - $${tier.price}</div>`
                                ).join('')}
                            </td>
                            <td>${package.description || ''}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm editPackage" data-id="${package.id}">Edit</button>
                                <button type="button" class="btn btn-danger btn-sm deletePackage" data-id="${package.id}">Delete</button>
                            </td>
                        </tr>
                    `;
                });
                $('table tbody').html(tableBody);
            });

            setTimeout(() => {
                $('#successMessage').fadeOut();
            }, 3000);
        }
    });
});




});

</script>

<script src="https://cdn.tiny.cloud/1/3arl7kd7bi1emf429o89drj6b16cmrwsmvdfwmidj6z90k59/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<script>
  tinymce.init({
    selector: 'textarea#desc', // Replace this CSS selector to match the placeholder element for TinyMCE
    plugins: 'code table lists image',
    toolbar: 'undo redo | image | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table'
  });
</script>
@endpush