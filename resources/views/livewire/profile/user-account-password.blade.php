@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid"> 
        <a class="back-link" href="/my-account">
            <i class="fa fa-angle-left fa-fw"></i>
            <span class="hidden-xs">My Account</span>
        </a>
        <div class="title">
            <h1><a href="/my-password/edit">Change password</a></h1>
        </div>
    </div>
</div>
@endsection

<div class="container-fluid">
    <div class="content-wrapper no-sidebar">
        <div id="content">
            @include('components.account-nav')
            <div class="row">
                <div class="col-md-8">
                    @if (session()->has('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="updatePassword">
                        <div class="form-group">
                            <label class="control-label" for="current_password">Current password</label>
                            <input wire:model="current_password" type="password" class="form-control input-lg" id="current_password">
                            @error('current_password') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="password">New password</label>
                            <input wire:model="password" type="password" class="form-control input-lg" id="password">
                            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-xs-block">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>