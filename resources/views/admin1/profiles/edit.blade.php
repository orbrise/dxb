@extends("admin.layout.master")

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

    .form-group {    margin-bottom: 20px;}
    label {
    color: var(--bs-label-color);
    vertical-align: middle;
    margin-bottom: 5px;
    }
</style>
@endpush

@section("content")
<div class="container">

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
        
        <div class="card">
            <div class="card-header">
                <h5>Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $profile->name }}" required>
                        </div>

                        <div class="form-group">
                            <label>Listing Type</label>
                            <select name="listing" class="form-control ">
                                @foreach($listings as $listing)
                                    <option value="{{$listing->id}}" {{ $profile->listing_id == $listing->id ? 'selected' : '' }}>
                                        {{$listing->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>City</label>
                            <select name="city" class="form-control select2">
                                @foreach($cities as $city)
                                    <option value="{{$city->id}}" {{ $city->id == $profile->city ? 'selected' : '' }}>
                                        {{$city->name}}
                                    </option>
                                @endforeach
                            </select>
                          
                        </div>

                        <div class="form-group">
                            <label>About</label>
                            <textarea name="about" class="form-control" rows="4" required>{{ $profile->about }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Contact Information -->
                        <div class="form-group">
                            <label>Primary Phone</label>
                            <div class="input-group">
                                <select class="" name="country_code" id="listing_phone_numbers_attributes_0_calling_code" style="padding:7px; width:34%;">
                                    <option value="">Intl. code</option>
                                    @foreach($countries as $code)
                                    <option value="{{$code->phonecode}}" @if($code->phonecode == $profile->country_code) selected @endif>+{{$code->phonecode}} - {{$code->nicename}}</option>
                                    @endforeach
                                  </select>
                                <input type="text" name="phone" class="form-control" value="{{ $profile->phone }}">
                            </div>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="iswhatsapp" value="1" {{ $profile->iswhatsapp ? 'checked' : '' }}>
                            <label>WhatsApp</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="iswechat" value="1" {{ $profile->iswechat ? 'checked' : '' }}>
                            <label>WeChat</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="istelegram" value="1" {{ $profile->istelegram ? 'checked' : '' }}>
                            <label>Telegram</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="issignal" value="1" {{ $profile->issignal ? 'checked' : '' }}>
                            <label>Signal</label>
                        </div>

                        <!-- Secondary Phone -->
                        <div class="form-group mt-3">
                            <label>Secondary Phone</label>
                            <div class="input-group">
                                <select class="" name="country_code2" id="listing_phone_numbers_attributes_0_calling_code" style="padding:7px; width:34%;">
                                    <option value="">Intl. code</option>
                                    @foreach($countries as $code)
                                    <option value="{{$code->phonecode}}" @if($code->phonecode == $profile->country_code) selected @endif>+{{$code->phonecode}} - {{$code->nicename}}</option>
                                    @endforeach
                                  </select>
                                <input type="text" name="phone2" class="form-control" value="{{ $profile->phone2 }}">
                            </div>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="iswhatsapp2" value="1" {{ $profile->iswhatsapp2 ? 'checked' : '' }}>
                            <label>WhatsApp 2</label>
                        </div>
                        <!-- Repeat for other messaging apps -->
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Profile Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender" class="form-control">
                                @foreach($genders as $gender)
                                    <option value="{{$gender->id}}" {{ $profile->gender == $gender->id ? 'selected' : '' }}>
                                        {{$gender->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Age</label>
                            <input type="number" name="age" class="form-control" value="{{ $profile->age }}" required>
                        </div>

                        <div class="form-group">
                            <label>Height</label>
                            <input type="number" name="height" class="form-control" value="{{ $profile->height }}">
                        </div>

                        <div class="form-group">
                            <label>Ethnicity</label>
                            <select name="ethnicity" class="form-control">
                                @foreach($ethnicities as $ethnicity)
                                    <option value="{{$ethnicity->id}}" {{ $profile->ethnicity == $ethnicity->id ? 'selected' : '' }}>
                                        {{$ethnicity->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Pricing -->
                        <div class="form-check">
                            <input type="checkbox" name="incall" value="1" {{ $profile->incall ? 'checked' : '' }}>
                            <label>Incall</label>
                        </div>

                        <div class="form-group">
                            <label>Incall Price</label>
                            <div class="input-group">
                                <input type="number" name="incallprice" class="form-control" value="{{ $profile->incallprice }}">
                                <select name="incallcurr" class="form-control">
                                    @foreach($currencies as $currency)
                                        <option value="{{$currency->code}}" {{ $profile->incallcurr == $currency->code ? 'selected' : '' }}>
                                            {{$currency->code}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="outcall" value="1" {{ $profile->outcall ? 'checked' : '' }}>
                            <label>Outcall</label>
                        </div>

                        <div class="form-group">
                            <label>Outcall Price</label>
                            <div class="input-group">
                                <input type="number" name="outcallprice" class="form-control" value="{{ $profile->outcallprice }}">
                                <select name="outcallcurr" class="form-control">
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
                <h5>Services</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($services as $service)
    <div class="col-md-3">
        <div class="form-check">
            <input type="checkbox" 
                   name="services[]" 
                   value="{{$service->id}}"
                   {{ $profile->services->where('service_id', $service->id)->count() > 0 ? 'checked' : '' }}>
            <label>{{$service->name}}</label>
        </div>
    </div>
@endforeach
                </div>
            </div>
        </div>

        <!-- Languages Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>Languages</h5>
            </div>
            <div class="card-body">
                @for($i = 1; $i <= 5; $i++)
                    @php
                        $userLanguage = $profile->languages->where('language_id', '!=', null)->values()->get($i-1);
                    @endphp
                    <div class="row mb-3">
                        <div class="col-md-6">
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
                        <div class="col-md-6">
                            <div class="form-check form-check-inline">
                                <input type="radio" name="expert{{$i}}" value="Fluent"
                                    {{ $userLanguage && $userLanguage->expert == 'Fluent' ? 'checked' : '' }}>
                                <label>Fluent</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="expert{{$i}}" value="Good"
                                    {{ $userLanguage && $userLanguage->expert == 'Good' ? 'checked' : '' }}>
                                <label>Good</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="expert{{$i}}" value="Basic"
                                    {{ $userLanguage && $userLanguage->expert == 'Basic' ? 'checked' : '' }}>
                                <label>Basic</label>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Additional Features -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>Additional Features</h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Video Link</label>
                    <input type="text" name="video" class="form-control" value="{{ $profile->video }}">
                </div>

                <div class="form-check">
                    <input type="checkbox" name="smoke" value="1" {{ $profile->smoke ? 'checked' : '' }}>
                    <label>Smoker</label>
                </div>

                <div class="form-group">
                    <label>Shaved</label>
                    <select name="shaved" class="form-control">
                        <option value="no" {{ $profile->shaved == 'no' ? 'selected' : '' }}>No</option>
                        <option value="partially" {{ $profile->shaved == 'partially' ? 'selected' : '' }}>Partially</option>
                        <option value="yes" {{ $profile->shaved == 'yes' ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Package</label>
                    <select name="package" class="form-control">
                        <option value="">None</option>
                        @foreach($packages as $package)
                        <option value="{{$package->id}}" @if($profile->package_id == $package->id and $package->is_featured ==1) selected @endif>{{$package->name}}</option>
                        @endforeach
                    </select>
                </div>


            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </div>
    </form>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@endpush