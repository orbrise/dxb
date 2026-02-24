@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid"> 
        <a class="back-link" href="{{url('/')}}" wire:navigate>
            <i class="fa fa-angle-left fa-fw"></i><span class="hidden-xs">Escorts in Dubai</span></a>
        <div class="title">
            <h1><a href="/sign-in">Change your password</a></h1></div>
    </div>
</div>
@endsection
@section("registertype")
@include('components.layouts.registertype')
@endsection
<div id="">
    @if (session()->has('message'))
                <div class="alert alert-success mt-2 mb-2">
                    {{ session('message') }}
                </div>
            @endif
          <h2>Change your password</h2>
          <form class="simple_form validate change-password-form" id="new_account" wire:submit.prevent="changePass">
           
            <div class="form-group password optional account_password">
              <label class="password optional control-label" for="account_password">New password</label>
              <input class="password optional form-control input-lg validate" data-validations="presence length(5,80)" maxlength="80" size="80" type="password" name="password" id="account_password" wire:model="password"/>
              @error('password')
              <span class="validation-error">{{ $message }}</span>
          @enderror
            </div>
            <div class="form-group password optional account_password_confirmation">
              <label class="password optional control-label" for="account_password_confirmation">Confirm new password</label>
              <input class="password optional form-control input-lg validate" data-validations="presence length(5,80)" type="password" name="password_confirmation" id="account_password_confirmation" wire:model='password_confirmation' />
              @error('password_confirmation')
              <span class="validation-error">{{ $message }}</span>
          @enderror
            </div>
            <p>
              <button class="btn btn-primary btn-lg btn-xs-block" data-btn-submit="" type="submit">Change my password</button>
            </p>
          </form>
          <div class="auth-other-links padding-bottom">
            <div class="list-group list-group-dark">
              <a class="list-group-item" href="{{route('sign-in')}}">Sign in</a>
              <a class="list-group-item " href="#" id="registerpopup">Register</a>
              <a class="list-group-item" href="/accounts/confirmation/new">Didn&#39;t receive confirmation email?</a>
            </div>
          </div>
        </div>