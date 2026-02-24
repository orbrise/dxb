@extends("admin.layout.master")

@push('css')
<style>
    .citys {
        width: 21.5%;
        height: 179px;
        position: absolute;
        background: #474747;
        padding: 10px;
        overflow: hidden;
        display: none;
    }
    .opt {
        font-size:14px;
        margin-bottom: 10px;
    }
    .flg {
        margin-right:10px;
    }

    .form-group {    
        margin-bottom: 20px;
    }
    
    label {
        color: var(--bs-label-color);
        vertical-align: middle;
        margin-bottom: 8px;
        font-weight: 500;
        font-size: 14px;
    }
    
    .card {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 24px;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .card-header {
        background: #4d77af;
        color: white !important;
        padding: 16px 24px;
        border-bottom: none;
    }
    
    .card-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 18px;
    }
    
    .card-body {
        padding: 24px;
    }
    
    .form-control, .form-select {
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    .form-check {
        padding: 8px 12px;
        background: #f8f9fa;
        border-radius: 6px;
        margin-bottom: 8px;
        display: inline-flex;
        align-items: center;
        margin-right: 12px;
    }
    
    .form-check input {
        margin-right: 8px;
        cursor: pointer;
    }
    
    .form-check label {
        margin-bottom: 0;
        cursor: pointer;
        font-weight: 400;
    }
    
    .input-group {
        border-radius: 6px;
        overflow: hidden;
    }
    
    .input-group select {
        border-right: 1px solid #e0e0e0;
    }
    
    .input-group-text {
        background: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-right: none;
        padding: 10px 14px;
        font-weight: 500;
        color: #667eea;
    }
    
    .section-divider {
        border-top: 2px solid #f0f0f0;
        margin: 24px 0;
    }
    
    .btn-primary {
        background:#667eea
        border: none;
        padding: 12px 32px;
        font-weight: 600;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }
    
    .messaging-apps {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }
    
    .badge-info-custom {
        background: #e3f2fd;
        color: #1976d2;
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .card-footer {
        background: #f8f9fa;
        padding: 16px 24px;
        border-top: 1px solid #e0e0e0;
    }
    
    .required-field::after {
        content: " *";
        color: #dc3545;
        font-weight: bold;
    }
    
    @media (max-width: 768px) {
        .card-body {
            padding: 16px;
        }
    }

   .h5 {

    color: white !important;
}

/* Image Management Styles */
.admin-image-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
}

.admin-image-item {
    position: relative;
    width: 150px;
    height: 150px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    cursor: move;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.admin-image-item:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-color: #667eea;
}

.admin-image-item.drag-over {
    border: 2px dashed #667eea;
    background-color: rgba(102, 126, 234, 0.1);
}

.admin-image-item.dragging {
    opacity: 0.5;
}

.admin-image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.admin-image-item .image-actions {
    position: absolute;
    top: 5px;
    right: 5px;
    display: flex;
    gap: 5px;
}

.admin-image-item .btn-delete-image {
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s;
}

.admin-image-item .btn-delete-image:hover {
    background: #dc3545;
    transform: scale(1.1);
}

.admin-image-item .image-badge {
    position: absolute;
    bottom: 5px;
    left: 5px;
    background: #28a745;
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
}

.admin-image-item .image-order {
    position: absolute;
    top: 5px;
    left: 5px;
    background: rgba(0,0,0,0.6);
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
}

.admin-upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 40px 20px;
    text-align: center;
    background: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
}

.admin-upload-area:hover,
.admin-upload-area.dragover {
    border-color: #667eea;
    background: rgba(102, 126, 234, 0.05);
}

.admin-upload-area .upload-icon {
    font-size: 48px;
    color: #6c757d;
    margin-bottom: 15px;
}

.admin-upload-area .upload-text {
    color: #6c757d;
    margin-bottom: 10px;
}

.admin-upload-area .btn-upload {
    background: #667eea;
    color: white;
    border: none;
    padding: 10px 24px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s;
}

.admin-upload-area .btn-upload:hover {
    background: #5a6fd6;
    transform: translateY(-2px);
}

.upload-progress {
    margin-top: 15px;
    display: none;
}

.upload-progress .progress {
    height: 8px;
    border-radius: 4px;
}

/* Custom Searchable Select */
.custom-select-wrapper {
    position: relative;
    width: 100%;
}

.custom-select-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    cursor: pointer;
    min-height: 42px;
    user-select: none;
}

.custom-select-trigger:hover {
    border-color: #667eea;
}

.custom-select-trigger.open {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
}

.custom-select-trigger .selected-text {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #495057;
    font-size: 14px;
}

.custom-select-trigger .selected-text.placeholder {
    color: #6c757d;
}

.custom-select-trigger .arrow {
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 5px solid #6c757d;
    margin-left: 10px;
    transition: transform 0.2s ease;
}

.custom-select-trigger.open .arrow {
    transform: rotate(180deg);
}

.custom-select-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #667eea;
    border-top: none;
    border-bottom-left-radius: 6px;
    border-bottom-right-radius: 6px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1000;
    display: none;
    max-height: 300px;
    overflow: hidden;
}

.custom-select-dropdown.show {
    display: block;
}

.custom-select-search {
    padding: 10px;
    border-bottom: 1px solid #e0e0e0;
}

.custom-select-search input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    font-size: 14px;
    outline: none;
}

.custom-select-search input:focus {
    border-color: #667eea;
}

.custom-select-options {
    max-height: 240px;
    overflow-y: auto;
}

.custom-select-option {
    padding: 10px 14px;
    cursor: pointer;
    font-size: 14px;
    color: #495057;
}

.custom-select-option:hover,
.custom-select-option.highlighted {
    background: #f0f4ff;
}

.custom-select-option.selected {
    background: #667eea;
    color: #fff;
}

.custom-select-option.no-results {
    color: #6c757d;
    font-style: italic;
    cursor: default;
}

.custom-select-option.no-results:hover {
    background: transparent;
}

/* Hide the original select */
.custom-select-hidden {
    position: absolute;
    opacity: 0;
    pointer-events: none;
    height: 0;
    width: 0;
}

/* Contact Action Buttons */
.contact-actions {
    display: flex;
    gap: 8px;
    margin-top: 10px;
}

.contact-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
}

.contact-btn i {
    font-size: 16px;
}

.contact-btn.whatsapp {
    background: #25D366;
    color: #fff;
}

.contact-btn.whatsapp:hover {
    background: #1da851;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
    color: #fff;
    text-decoration: none;
}

.contact-btn.telegram {
    background: #0088cc;
    color: #fff;
}

.contact-btn.telegram:hover {
    background: #006699;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 136, 204, 0.4);
    color: #fff;
    text-decoration: none;
}

.contact-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

</style>
@endpush

@section("content")
<div class="container">
<div class="row page-title clearfix">
                <div class="page-title-left">
                    <h5 class="mr-0 mr-r-5">Profiles</h5>
                    <p class="mr-0 text-muted d-none d-md-inline-block">Manage profiles effectively</p>
                </div>
                <!-- /.page-title-left -->
                <div class="page-title-right d-none d-sm-inline-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('admin/dashboard')}}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Profiles</li>
                    </ol>
                   
                </div>
                <!-- /.page-title-right -->
        </div>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


    <form action="{{ route('admin.profiles.update', $profile->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- User Information -->
        <div class="card mb-4 mt-3">
            <div class="card-header">
                <h5 style="margin:0px; color: white !important;  ">Profile Creator Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>User Name:</strong> {{ $profile->user->name ?? 'N/A' }}</p>
                        <p class="mb-2"><strong>Email:</strong> {{ $profile->user->email ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>User ID:</strong> {{ $profile->user->id ?? 'N/A' }}</p>
                        <p class="mb-2"><strong>Created At:</strong> {{ $profile->created_at ? $profile->created_at->format('d M Y, h:i A') : 'N/A' }}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <p class="mb-2"><strong>IP Address:</strong> {{ $profile->ip_address ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong>IP Country:</strong> {{ $profile->ip_country ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
         
        <div class="card">
            <div class="card-header">
                <h5 style="color: white !important;">📝 Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required-field">Profile Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $profile->name }}" required placeholder="Enter profile name">
                        </div>

                        <div class="form-group">
                            <label class="required-field">Listing Type</label>
                            <select name="listing" class="form-control" required>
                                <option value="">Select Listing Type</option>
                                @foreach($listings as $listing)
                                    <option value="{{$listing->id}}" {{ $profile->listing == $listing->id ? 'selected' : '' }}>
                                        {{$listing->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="required-field">City</label>
                            <select name="city" class="form-control custom-searchable-select" required>
                                <option value="">Select City</option>
                                @foreach($cities as $city)
                                    <option value="{{$city->id}}" {{ $city->id == $profile->city ? 'selected' : '' }}>
                                        {{$city->name}}, {{$city->country}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>IP Address</label>
                                    <input type="text" class="form-control" value="{{ $profile->ip_address ?? 'N/A' }}" readonly style="background-color: #f8f9fa;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>IP Country</label>
                                    <input type="text" class="form-control" value="{{ $profile->ip_country ?? 'N/A' }}" readonly style="background-color: #f8f9fa;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="required-field">About Me</label>
                            <textarea name="about" class="form-control" rows="8" required placeholder="Write a compelling description about yourself...">{{ $profile->about }}</textarea>
                            <small class="text-muted">Minimum 100 characters recommended</small>
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <h6 class="mb-3 text-muted">📞 Contact Information</h6>
                <div class="row">
                    <div class="col-md-6">
                        <!-- Contact Information -->
                        <div class="form-group">
                            <label>Primary Phone</label>
                            <div class="input-group">
                                <select class="form-control" name="country_code" style="max-width: 150px;">
                                    <option value="">Intl. code</option>
                                    @foreach($countries as $code)
                                    <option value="{{$code->phonecode}}" @if($code->phonecode == $profile->country_code) selected @endif>+{{$code->phonecode}} - {{$code->nicename}}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="phone" class="form-control" value="{{ $profile->phone }}" placeholder="Phone number">
                            </div>
                        </div>

                        {{-- Quick Contact Buttons --}}
                        @if($profile->phone && $profile->country_code)
                        <div class="contact-actions">
                            @if($profile->iswhatsapp)
                            <a href="https://wa.me/{{ $profile->country_code }}{{ preg_replace('/[^0-9]/', '', $profile->phone) }}" 
                               target="_blank" 
                               class="contact-btn whatsapp"
                               title="Send WhatsApp message">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                            @endif
                            @if($profile->istelegram)
                            <a href="https://t.me/+{{ $profile->country_code }}{{ preg_replace('/[^0-9]/', '', $profile->phone) }}" 
                               target="_blank" 
                               class="contact-btn telegram"
                               title="Send Telegram message">
                                <i class="fab fa-telegram-plane"></i> Telegram
                            </a>
                            @endif
                        </div>
                        @endif

                        <div class="messaging-apps">
                            <div class="form-check">
                                <input type="checkbox" name="iswhatsapp" value="1" {{ $profile->iswhatsapp ? 'checked' : '' }} id="iswhatsapp">
                                <label for="iswhatsapp">📱 WhatsApp</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="iswechat" value="1" {{ $profile->iswechat ? 'checked' : '' }} id="iswechat">
                                <label for="iswechat">💬 WeChat</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="istelegram" value="1" {{ $profile->istelegram ? 'checked' : '' }} id="istelegram">
                                <label for="istelegram">✈️ Telegram</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="issignal" value="1" {{ $profile->issignal ? 'checked' : '' }} id="issignal">
                                <label for="issignal">🔒 Signal</label>
                            </div>
                        </div>

                        <!-- Secondary Phone -->
                        <div class="form-group mt-4">
                            <label>Secondary Phone <small class="text-muted">(Optional)</small></label>
                            <div class="input-group">
                                <select class="form-control" name="country_code2" style="max-width: 150px;">
                                    <option value="">Intl. code</option>
                                    @foreach($countries as $code)
                                    <option value="{{$code->phonecode}}" @if($code->phonecode == $profile->country_code2) selected @endif>+{{$code->phonecode}} - {{$code->nicename}}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="phone2" class="form-control" value="{{ $profile->phone2 }}" placeholder="Secondary phone number">
                            </div>
                        </div>

                        {{-- Quick Contact Buttons for Secondary Phone --}}
                        @if($profile->phone2 && $profile->country_code2)
                        <div class="contact-actions">
                            @if($profile->iswhatsapp2)
                            <a href="https://wa.me/{{ $profile->country_code2 }}{{ preg_replace('/[^0-9]/', '', $profile->phone2) }}" 
                               target="_blank" 
                               class="contact-btn whatsapp"
                               title="Send WhatsApp message">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                            @endif
                            @if($profile->istelegram2)
                            <a href="https://t.me/+{{ $profile->country_code2 }}{{ preg_replace('/[^0-9]/', '', $profile->phone2) }}" 
                               target="_blank" 
                               class="contact-btn telegram"
                               title="Send Telegram message">
                                <i class="fab fa-telegram-plane"></i> Telegram
                            </a>
                            @endif
                        </div>
                        @endif

                        <div class="messaging-apps">
                            <div class="form-check">
                                <input type="checkbox" name="iswhatsapp2" value="1" {{ $profile->iswhatsapp2 ? 'checked' : '' }} id="iswhatsapp2">
                                <label for="iswhatsapp2">📱 WhatsApp</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="iswechat2" value="1" {{ $profile->iswechat2 ? 'checked' : '' }} id="iswechat2">
                                <label for="iswechat2">💬 WeChat</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="istelegram2" value="1" {{ $profile->istelegram2 ? 'checked' : '' }} id="istelegram2">
                                <label for="istelegram2">✈️ Telegram</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="issignal2" value="1" {{ $profile->issignal2 ? 'checked' : '' }} id="issignal2">
                                <label for="issignal2">🔒 Signal</label>
                            </div>
                        </div>

                        <div class="section-divider"></div>

                       

                        <div class="form-group">
                            <label>Website <small class="text-muted">(Optional)</small></label>
                            <input type="url" name="website" class="form-control" value="{{ $profile->website }}" placeholder="https://example.com">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 style="color: white !important;">👤 Profile Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="required-field">Gender</label>
                            <select name="gender" class="form-control" required>
                                <option value="">Select Gender</option>
                                @foreach($genders as $gender)
                                    <option value="{{$gender->id}}" {{ $profile->gender == $gender->id ? 'selected' : '' }}>
                                        {{$gender->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Orientation</label>
                            <select name="orientation" class="form-control">
                                <option value="">Select Orientation</option>
                                <option value="1" {{ $profile->orientation == 1 ? 'selected' : '' }}>Straight</option>
                                <option value="2" {{ $profile->orientation == 2 ? 'selected' : '' }}>Gay/Lesbian</option>
                                <option value="3" {{ $profile->orientation == 3 ? 'selected' : '' }}>Bisexual</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="required-field">Age</label>
                            <input type="number" name="age" class="form-control" value="{{ $profile->age }}" min="18" max="99" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Height (cm)</label>
                            <input type="number" name="height" class="form-control" value="{{ $profile->height }}" placeholder="170">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ethnicity</label>
                            <select name="ethnicity" class="form-control">
                                <option value="">Select Ethnicity</option>
                                @foreach($ethnicities as $ethnicity)
                                    <option value="{{$ethnicity->id}}" {{ $profile->ethnicity == $ethnicity->id ? 'selected' : '' }}>
                                        {{$ethnicity->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Bust/Cup Size</label>
                            <select name="bust" class="form-control">
                                <option value="">Select Size</option>
                                @foreach($busts as $bust)
                                    <option value="{{$bust->id}}" {{ $profile->bust == $bust->id ? 'selected' : '' }}>
                                        {{$bust->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Hair Color</label>
                            <select name="haircolor" class="form-control">
                                <option value="">Select Color</option>
                                @foreach($hairs as $hair)
                                    <option value="{{$hair->id}}" {{ $profile->haircolor == $hair->id ? 'selected' : '' }}>
                                        {{$hair->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Nationality</label>
                            <select name="nationality" class="form-control custom-searchable-select">
                                <option value="">Select Nationality</option>
                                @foreach($countries as $country)
                                    <option value="{{$country->id}}" {{ $profile->nationality == $country->id ? 'selected' : '' }}>
                                        {{$country->nicename}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <h6 class="mb-3 text-muted">Pricing Information</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input type="checkbox" name="incall" value="1" {{ $profile->incall ? 'checked' : '' }}>
                            <label>Incall Available</label>
                        </div>

                        <div class="form-group">
                            <label>Incall Price (per hour)</label>
                            <div class="input-group">
                                <input type="number" name="incallprice" class="form-control" value="{{ $profile->incallprice }}" placeholder="0">
                                <select name="incallcurr" class="form-control" style="max-width: 120px;">
                                    @foreach($currencies as $currency)
                                        <option value="{{$currency->code}}" {{ $profile->incallcurr == $currency->code ? 'selected' : '' }}>
                                            {{$currency->code}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input type="checkbox" name="outcall" value="1" {{ $profile->outcall ? 'checked' : '' }}>
                            <label>Outcall Available</label>
                        </div>

                        <div class="form-group">
                            <label>Outcall Price (per hour)</label>
                            <div class="input-group">
                                <input type="number" name="outcallprice" class="form-control" value="{{ $profile->outcallprice }}" placeholder="0">
                                <select name="outcallcurr" class="form-control" style="max-width: 120px;">
                                    @foreach($currencies as $currency)
                                        <option value="{{$currency->code}}" {{ $profile->outcallcurr == $currency->code ? 'selected' : '' }}>
                                            {{$currency->code}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 style="color: white !important;">💼 Services Offered</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($services as $service)
                    <div class="col-md-4 col-lg-3">
                        <div class="form-check mb-2">
                            <input type="checkbox" 
                                   name="services[]" 
                                   value="{{$service->id}}"
                                   id="service_{{$service->id}}"
                                   {{ $profile->services->where('service_id', $service->id)->count() > 0 ? 'checked' : '' }}>
                            <label for="service_{{$service->id}}">{{$service->name}}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Languages Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 style="color: white !important;">🌍 Languages</h5>
            </div>
            <div class="card-body">
                @for($i = 1; $i <= 3; $i++)
                    @php
                        $userLanguage = $profile->languages->where('language_id', '!=', null)->values()->get($i-1);
                    @endphp
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-5">
                            <label class="mb-2">Language {{$i}}</label>
                            <select name="language{{$i}}" class="form-control">
                                <option value="">Select Language</option>
                                @foreach($languages as $language)
                                    <option value="{{$language->id}}"
                                        {{ $userLanguage && $userLanguage->language_id == $language->id ? 'selected' : '' }}>
                                        {{$language->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="mb-2">Proficiency Level</label>
                            <div class="d-flex gap-2">
                                <div class="form-check">
                                    <input type="radio" name="expert{{$i}}" value="Fluent" id="fluent{{$i}}"
                                        {{ $userLanguage && $userLanguage->expert == 'Fluent' ? 'checked' : '' }}>
                                    <label for="fluent{{$i}}">✨ Fluent</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="expert{{$i}}" value="Good" id="good{{$i}}"
                                        {{ $userLanguage && $userLanguage->expert == 'Good' ? 'checked' : '' }}>
                                    <label for="good{{$i}}">👍 Good</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="expert{{$i}}" value="Basic" id="basic{{$i}}"
                                        {{ $userLanguage && $userLanguage->expert == 'Basic' ? 'checked' : '' }}>
                                    <label for="basic{{$i}}">📚 Basic</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($i < 3)
                    <hr class="my-3">
                    @endif
                @endfor
            </div>
        </div>

        <!-- Additional Features -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 style="color: white !important;">⚙️ Additional Features</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Video Link <small class="text-muted">(YouTube, Vimeo, etc.)</small></label>
                            <input type="url" name="video" class="form-control" value="{{ $profile->video }}" placeholder="https://youtube.com/watch?v=...">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Shaved</label>
                            <select name="shaved" class="form-control">
                                <option value="">Select Option</option>
                                <option value="no" {{ $profile->shaved == 'no' ? 'selected' : '' }}>No</option>
                                <option value="partially" {{ $profile->shaved == 'partially' ? 'selected' : '' }}>Partially</option>
                                <option value="yes" {{ $profile->shaved == 'yes' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input type="checkbox" name="smoke" value="1" {{ $profile->smoke ? 'checked' : '' }} id="smoke">
                            <label for="smoke">🚬 I smoke</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_active" value="1" {{ $profile->is_active ? 'checked' : '' }} id="is_active">
                            <label for="is_active">✅ Profile Active</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                            <label>Package</label>
                            <select name="package" class="form-control" id="package_select">
                                <option value="">None</option>
                                @foreach($packages as $package)
                                <option value="{{$package->id}}" 
                                        @if($profile->package_id == $package->id) selected @endif>
                                    {{$package->name}} - {{$package->tagline ?? ''}}
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Select package type first</small>
                        </div>
                        <div class="form-group">
                            <label>Duration & Price</label>
                            @if($profile->package_id && $profile->package_expires_at)
                                <div class="alert alert-success py-2 mb-2">
                                    <i class="fas fa-check-circle"></i> 
                                    <strong>{{ $profile->getpackage->name ?? 'Package' }}</strong> active until 
                                    <strong>{{ \Carbon\Carbon::parse($profile->package_expires_at)->format('M d, Y') }}</strong>
                                </div>
                            @endif
                            <select name="package_tier" class="form-control" id="package_tier_select">
                                <option value="">{{ $profile->package_id ? 'Keep current duration' : 'Select package first' }}</option>
                            </select>
                            <small class="text-muted d-block mt-1">
                                @if($profile->package_id && $profile->package_expires_at)
                                    Only select if you want to change/extend the duration
                                @else
                                    Duration and price will be set automatically
                                @endif
                            </small>
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <h6 class="mb-3 text-muted">Social Media</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Twitter/X Username <small class="text-muted">(without @)</small></label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" name="twitter" class="form-control" value="{{ $profile->twitter }}" placeholder="username">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Instagram Username <small class="text-muted">(without @)</small></label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" name="instagram" class="form-control" value="{{ $profile->instagram }}" placeholder="username">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Management Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 style="color: white !important;">📷 Profile Images</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    <i class="fas fa-info-circle"></i> 
                    Drag images to reorder. The first image will be the main/cover image.
                    Maximum 10 images, 5MB each.
                </p>
                
                <!-- Current Images -->
                <div class="admin-image-container" id="image-container" data-profile-id="{{ $profile->id }}">
                    @foreach($profile->allImages as $index => $image)
                    <div class="admin-image-item" 
                         draggable="true" 
                         data-id="{{ $image->id }}" 
                         data-position="{{ $index }}">
                        <span class="image-order">#{{ $index + 1 }}</span>
                        <img src="{{ smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$image->image) }}" 
                             alt="Profile image {{ $index + 1 }}">
                        <div class="image-actions">
                            <button type="button" 
                                    class="btn-delete-image" 
                                    data-image-id="{{ $image->id }}"
                                    title="Delete image">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        @if($index === 0)
                        <span class="image-badge">Main</span>
                        @endif
                    </div>
                    @endforeach
                </div>

                @if($profile->allImages->count() == 0)
                <div class="alert alert-info" id="no-images-alert">
                    <i class="fas fa-image"></i> No images uploaded yet.
                </div>
                @endif

                <!-- Upload Area -->
                <div class="admin-upload-area" id="upload-area">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <p class="upload-text">Drag & drop images here or click to browse</p>
                    <button type="button" class="btn-upload" onclick="document.getElementById('image-upload').click()">
                        <i class="fas fa-plus"></i> Add Images
                    </button>
                    <input type="file" 
                           id="image-upload" 
                           name="images[]" 
                           multiple 
                           accept="image/jpeg,image/png,image/jpg,image/webp"
                           style="display: none;">
                    <p class="text-muted small mt-2">Supports: JPG, PNG, WebP (Max 5MB each)</p>
                </div>

                <div class="upload-progress" id="upload-progress">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" 
                             style="width: 0%"></div>
                    </div>
                    <p class="text-center small mt-1" id="upload-status">Uploading...</p>
                </div>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.profiles.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Back to Profiles
            </a>
            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Update Profile
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        // Custom Searchable Select Implementation
        function initCustomSearchableSelect(selectElement) {
            const $select = $(selectElement);
            const options = [];
            let selectedValue = $select.val();
            let selectedText = '';
            let placeholder = 'Select...';
            
            // Extract options from original select
            $select.find('option').each(function() {
                const $opt = $(this);
                const val = $opt.val();
                const text = $opt.text().trim();
                
                if (val === '' || val === null) {
                    placeholder = text || 'Select...';
                } else {
                    options.push({ value: val, text: text });
                    if (val == selectedValue) {
                        selectedText = text;
                    }
                }
            });
            
            // Create wrapper
            const $wrapper = $('<div class="custom-select-wrapper"></div>');
            
            // Create trigger
            const displayText = selectedText || placeholder;
            const isPlaceholder = !selectedText;
            const $trigger = $(`
                <div class="custom-select-trigger">
                    <span class="selected-text ${isPlaceholder ? 'placeholder' : ''}">${displayText}</span>
                    <span class="arrow"></span>
                </div>
            `);
            
            // Create dropdown
            const $dropdown = $(`
                <div class="custom-select-dropdown">
                    <div class="custom-select-search">
                        <input type="text" placeholder="Search..." autocomplete="off">
                    </div>
                    <div class="custom-select-options"></div>
                </div>
            `);
            
            const $optionsContainer = $dropdown.find('.custom-select-options');
            const $searchInput = $dropdown.find('input');
            
            // Render options
            function renderOptions(filter = '') {
                $optionsContainer.empty();
                const filterLower = filter.toLowerCase();
                let hasResults = false;
                let highlightedIndex = -1;
                
                options.forEach((opt, index) => {
                    if (filter === '' || opt.text.toLowerCase().includes(filterLower)) {
                        hasResults = true;
                        const isSelected = opt.value == selectedValue;
                        const $option = $(`<div class="custom-select-option ${isSelected ? 'selected' : ''}" data-value="${opt.value}" data-index="${index}">${opt.text}</div>`);
                        $optionsContainer.append($option);
                    }
                });
                
                if (!hasResults) {
                    $optionsContainer.append('<div class="custom-select-option no-results">No results found</div>');
                }
            }
            
            // Initial render
            renderOptions();
            
            // Hide original select and insert custom one
            $select.addClass('custom-select-hidden');
            $wrapper.append($trigger).append($dropdown);
            $select.after($wrapper);
            
            // Toggle dropdown
            $trigger.on('click', function(e) {
                e.stopPropagation();
                const isOpen = $dropdown.hasClass('show');
                
                // Close all other dropdowns
                $('.custom-select-dropdown').removeClass('show');
                $('.custom-select-trigger').removeClass('open');
                
                if (!isOpen) {
                    $dropdown.addClass('show');
                    $trigger.addClass('open');
                    $searchInput.val('').focus();
                    renderOptions();
                }
            });
            
            // Search functionality
            $searchInput.on('input', function() {
                renderOptions($(this).val());
            });
            
            // Keyboard navigation
            let highlightedIdx = -1;
            $searchInput.on('keydown', function(e) {
                const $visibleOptions = $optionsContainer.find('.custom-select-option:not(.no-results)');
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    highlightedIdx = Math.min(highlightedIdx + 1, $visibleOptions.length - 1);
                    $visibleOptions.removeClass('highlighted');
                    $visibleOptions.eq(highlightedIdx).addClass('highlighted');
                    scrollToHighlighted();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    highlightedIdx = Math.max(highlightedIdx - 1, 0);
                    $visibleOptions.removeClass('highlighted');
                    $visibleOptions.eq(highlightedIdx).addClass('highlighted');
                    scrollToHighlighted();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    const $highlighted = $visibleOptions.eq(highlightedIdx);
                    if ($highlighted.length) {
                        selectOption($highlighted.data('value'), $highlighted.text());
                    }
                } else if (e.key === 'Escape') {
                    closeDropdown();
                }
            });
            
            function scrollToHighlighted() {
                const $highlighted = $optionsContainer.find('.highlighted');
                if ($highlighted.length) {
                    const container = $optionsContainer[0];
                    const option = $highlighted[0];
                    if (option.offsetTop < container.scrollTop) {
                        container.scrollTop = option.offsetTop;
                    } else if (option.offsetTop + option.offsetHeight > container.scrollTop + container.offsetHeight) {
                        container.scrollTop = option.offsetTop + option.offsetHeight - container.offsetHeight;
                    }
                }
            }
            
            // Option click
            $optionsContainer.on('click', '.custom-select-option:not(.no-results)', function() {
                const $opt = $(this);
                selectOption($opt.data('value'), $opt.text());
            });
            
            function selectOption(value, text) {
                selectedValue = value;
                selectedText = text;
                $select.val(value).trigger('change');
                $trigger.find('.selected-text').text(text).removeClass('placeholder');
                closeDropdown();
                renderOptions();
            }
            
            function closeDropdown() {
                $dropdown.removeClass('show');
                $trigger.removeClass('open');
                highlightedIdx = -1;
            }
            
            // Close on outside click
            $(document).on('click', function(e) {
                if (!$(e.target).closest($wrapper).length) {
                    closeDropdown();
                }
            });
            
            // Prevent search input from closing dropdown
            $searchInput.on('click', function(e) {
                e.stopPropagation();
            });
        }
        
        // Initialize all custom searchable selects
        $('.custom-searchable-select').each(function() {
            initCustomSearchableSelect(this);
        });
        
        // Setup AJAX defaults with CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Function to populate tier dropdown based on selected package
        function populateTiers(packageId) {
            console.log('Loading package:', packageId);
            const tierSelect = $('#package_tier_select');
            
            // Clear existing options
            tierSelect.html('<option value="">Loading...</option>').prop('disabled', true);
            
            if (!packageId) {
                tierSelect.html('<option value="">Select package first</option>');
                return;
            }
            
            // Fetch package data via AJAX
            $.ajax({
                url: '{{ url("admin/package") }}/' + packageId,
                method: 'GET',
                dataType: 'json',
                success: function(pkg) {
                    console.log("Package data received:", pkg);
                    
                    let tiers = [];
                    
                    // Check for country-specific pricing first
                    if (pkg.country_prices && pkg.country_prices.length > 0) {
                        let countryPrice = pkg.country_prices[0];
                        if (countryPrice.price_tiers) {
                            try {
                                tiers = typeof countryPrice.price_tiers === 'string' 
                                    ? JSON.parse(countryPrice.price_tiers) 
                                    : countryPrice.price_tiers;
                            } catch(e) {
                                console.error('Error parsing country price tiers:', e);
                            }
                        }
                    }
                    
                    // Fallback to default price_tiers if no country pricing
                    if (tiers.length === 0 && pkg.price_tiers) {
                        try {
                            tiers = typeof pkg.price_tiers === 'string' 
                                ? JSON.parse(pkg.price_tiers) 
                                : pkg.price_tiers;
                        } catch(e) {
                            console.error('Error parsing price tiers:', e);
                        }
                    }
                    
                    console.log("Price tiers:", tiers);
                    
                    // Clear and populate options
                    tierSelect.html('<option value="">Select duration</option>');
                    
                    if (tiers && tiers.length > 0) {
                        var currentDays = {{ $profile->package_days ?? 'null' }};
                        tiers.forEach(function(tier) {
                            var isSelected = (currentDays && parseInt(tier.days) === currentDays) ? ' selected' : '';
                            tierSelect.append(
                                '<option value="' + tier.days + '" data-price="' + tier.price + '"' + isSelected + '>' +
                                    tier.days + ' days - $' + tier.price +
                                '</option>'
                            );
                        });
                        tierSelect.prop('disabled', false);
                        console.log('Options added. Current days:', currentDays);
                    } else {
                        tierSelect.html('<option value="">No pricing available</option>');
                        console.log('No tiers available for this package');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching package data:", error);
                    console.error("Status:", status);
                    console.error("Response:", xhr.responseText);
                    tierSelect.html('<option value="">Error loading prices</option>');
                }
            });
        }
        
        // When package is selected, populate tier options
        $('#package_select').on('change', function() {
            const packageId = $(this).val();
            populateTiers(packageId);
        });
        
        // Initialize on page load if package is already selected
        const initialPackage = $('#package_select').val();
        if (initialPackage) {
            console.log('Initial package detected:', initialPackage);
            // Small delay to ensure DOM is fully ready
            setTimeout(function() {
                populateTiers(initialPackage);
            }, 100);
        }

        // =============================================
        // IMAGE MANAGEMENT - Upload, Delete, Drag & Drop
        // =============================================
        
        const profileId = {{ $profile->id }};
        const imageContainer = document.getElementById('image-container');
        const uploadArea = document.getElementById('upload-area');
        const imageInput = document.getElementById('image-upload');
        const uploadProgress = document.getElementById('upload-progress');
        const noImagesAlert = document.getElementById('no-images-alert');
        
        // CSRF Token for AJAX requests
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        // ============ FILE UPLOAD ============
        
        // Handle file input change
        imageInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                uploadImages(e.target.files);
            }
        });
        
        // Drag and drop on upload area
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('dragover');
            
            if (e.dataTransfer.files.length > 0) {
                uploadImages(e.dataTransfer.files);
            }
        });
        
        function uploadImages(files) {
            const formData = new FormData();
            
            for (let i = 0; i < files.length; i++) {
                formData.append('images[]', files[i]);
            }
            
            // Show progress
            uploadProgress.style.display = 'block';
            const progressBar = uploadProgress.querySelector('.progress-bar');
            const statusText = document.getElementById('upload-status');
            
            $.ajax({
                url: `/admin/profiles/${profileId}/images/upload`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            var percent = Math.round((e.loaded / e.total) * 100);
                            progressBar.style.width = percent + '%';
                            statusText.textContent = `Uploading... ${percent}%`;
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    progressBar.style.width = '100%';
                    statusText.textContent = response.message;
                    
                    // Add new images to container
                    response.images.forEach(function(image) {
                        addImageToContainer(image);
                    });
                    
                    // Hide no images alert
                    if (noImagesAlert) {
                        noImagesAlert.style.display = 'none';
                    }
                    
                    // Reset progress after delay
                    setTimeout(function() {
                        uploadProgress.style.display = 'none';
                        progressBar.style.width = '0%';
                    }, 2000);
                    
                    // Reset input
                    imageInput.value = '';
                    
                    // Reinitialize drag-drop
                    initDragDrop();
                    updateImageNumbers();
                },
                error: function(xhr) {
                    progressBar.classList.add('bg-danger');
                    statusText.textContent = 'Upload failed: ' + (xhr.responseJSON?.message || 'Unknown error');
                    
                    setTimeout(function() {
                        uploadProgress.style.display = 'none';
                        progressBar.style.width = '0%';
                        progressBar.classList.remove('bg-danger');
                    }, 3000);
                }
            });
        }
        
        function addImageToContainer(image) {
            const imageCount = imageContainer.querySelectorAll('.admin-image-item').length;
            const isMain = imageCount === 0;
            
            const div = document.createElement('div');
            div.className = 'admin-image-item';
            div.draggable = true;
            div.dataset.id = image.id;
            div.dataset.position = image.order;
            
            div.innerHTML = `
                <span class="image-order">#${imageCount + 1}</span>
                <img src="${image.url}" alt="Profile image">
                <div class="image-actions">
                    <button type="button" class="btn-delete-image" data-image-id="${image.id}" title="Delete image">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                ${isMain ? '<span class="image-badge">Main</span>' : ''}
            `;
            
            imageContainer.appendChild(div);
        }
        
        // ============ DELETE IMAGE ============
        
        $(document).on('click', '.btn-delete-image', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const imageId = $(this).data('image-id');
            const imageItem = $(this).closest('.admin-image-item');
            
            if (!confirm('Are you sure you want to delete this image?')) {
                return;
            }
            
            $.ajax({
                url: `/admin/profile-images/${imageId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    imageItem.fadeOut(300, function() {
                        $(this).remove();
                        updateImageNumbers();
                        
                        // Show no images alert if no images left
                        if (imageContainer.querySelectorAll('.admin-image-item').length === 0) {
                            if (noImagesAlert) {
                                noImagesAlert.style.display = 'block';
                            }
                        }
                    });
                },
                error: function(xhr) {
                    alert('Failed to delete image: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            });
        });
        
        // ============ DRAG & DROP REORDER ============
        
        let draggedItem = null;
        
        function initDragDrop() {
            const items = imageContainer.querySelectorAll('.admin-image-item');
            
            items.forEach(function(item) {
                item.addEventListener('dragstart', handleDragStart);
                item.addEventListener('dragend', handleDragEnd);
                item.addEventListener('dragover', handleDragOver);
                item.addEventListener('drop', handleDrop);
                item.addEventListener('dragenter', handleDragEnter);
                item.addEventListener('dragleave', handleDragLeave);
            });
        }
        
        function handleDragStart(e) {
            draggedItem = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.innerHTML);
        }
        
        function handleDragEnd(e) {
            this.classList.remove('dragging');
            
            imageContainer.querySelectorAll('.admin-image-item').forEach(function(item) {
                item.classList.remove('drag-over');
            });
            
            // Save new order
            saveImageOrder();
        }
        
        function handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        }
        
        function handleDragEnter(e) {
            if (this !== draggedItem) {
                this.classList.add('drag-over');
            }
        }
        
        function handleDragLeave(e) {
            this.classList.remove('drag-over');
        }
        
        function handleDrop(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (this !== draggedItem) {
                // Get positions
                const allItems = Array.from(imageContainer.querySelectorAll('.admin-image-item'));
                const draggedIdx = allItems.indexOf(draggedItem);
                const targetIdx = allItems.indexOf(this);
                
                // Swap positions in DOM
                if (draggedIdx < targetIdx) {
                    this.parentNode.insertBefore(draggedItem, this.nextSibling);
                } else {
                    this.parentNode.insertBefore(draggedItem, this);
                }
                
                updateImageNumbers();
            }
            
            this.classList.remove('drag-over');
        }
        
        function updateImageNumbers() {
            const items = imageContainer.querySelectorAll('.admin-image-item');
            
            items.forEach(function(item, index) {
                // Update order number
                const orderSpan = item.querySelector('.image-order');
                if (orderSpan) {
                    orderSpan.textContent = '#' + (index + 1);
                }
                
                // Update main badge
                const existingBadge = item.querySelector('.image-badge');
                if (index === 0) {
                    if (!existingBadge) {
                        const badge = document.createElement('span');
                        badge.className = 'image-badge';
                        badge.textContent = 'Main';
                        item.appendChild(badge);
                    }
                } else {
                    if (existingBadge) {
                        existingBadge.remove();
                    }
                }
            });
        }
        
        function saveImageOrder() {
            const items = imageContainer.querySelectorAll('.admin-image-item');
            const order = [];
            
            items.forEach(function(item) {
                order.push(parseInt(item.dataset.id));
            });
            
            $.ajax({
                url: `/admin/profiles/${profileId}/images/reorder`,
                type: 'POST',
                data: JSON.stringify({ order: order }),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    console.log('Order saved:', response.message);
                },
                error: function(xhr) {
                    console.error('Failed to save order:', xhr.responseJSON?.message);
                    alert('Failed to save image order. Please refresh and try again.');
                }
            });
        }
        
        // Initialize drag-drop on page load
        initDragDrop();
    });
</script>
@endpush