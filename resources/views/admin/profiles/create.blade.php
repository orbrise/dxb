@extends('admin.layout.master')
@section('content')
<div class="container">
    <h2>Create Profile</h2>
    <form action="{{ route('admin.profiles.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Listing</label>
                    <select name="listing_id" class="form-control" required>
                        @foreach($listings as $listing)
                            <option value="{{ $listing->id }}">{{ $listing->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>About Me</label>
                    <textarea name="aboutme" class="form-control" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <div class="input-group">
                        <input type="text" name="countrycode" class="form-control" placeholder="+971">
                        <input type="text" name="phone" class="form-control">
                    </div>
                </div>

                <div class="form-check">
                    <input type="checkbox" name="iswhatsapp" class="form-check-input">
                    <label>WhatsApp</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender_id" class="form-control" required>
                        @foreach($genders as $gender)
                            <option value="{{ $gender->id }}">{{ $gender->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Age</label>
                    <input type="number" name="age" class="form-control" required min="18" max="60">
                </div>

                <div class="form-group">
                    <label>Height (cm)</label>
                    <input type="number" name="height" class="form-control">
                </div>

                <div class="form-check">
                    <input type="checkbox" name="incall" class="form-check-input">
                    <label>Incall</label>
                </div>

                <div class="form-group">
                    <label>Incall Price</label>
                    <input type="number" name="incallprice" class="form-control">
                </div>

                <div class="form-check">
                    <input type="checkbox" name="outcall" class="form-check-input">
                    <label>Outcall</label>
                </div>

                <div class="form-group">
                    <label>Outcall Price</label>
                    <input type="number" name="outcallprice" class="form-control">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Create Profile</button>
    </form>
</div>
@endsection