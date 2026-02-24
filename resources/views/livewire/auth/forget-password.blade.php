@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid"> 
        <a class="back-link" href="{{url('/')}}" wire:navigate>
            <i class="fa fa-angle-left fa-fw"></i><span class="hidden-xs">Escorts in Dubai</span></a>
        <div class="title">
            <h1><a href="/sign-in">Forget your password?</a></h1></div>
    </div>
</div>
@endsection

@section("registertype")
@include('components.layouts.registertype')
@endsection

<div class="row">
    <div class="col-md-6 col-md-offset-3">
      <h2>Forgot your password?</h2>
      <div class="fancy-box margin-bottom">
        @if (session()->has('success'))
        <div class="alert alert-success mt-2 mb-2" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-weight: normal;">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
    <div class="alert alert-danger mt-2 mb-2" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-weight: normal;">
        {{ session('error') }}
    </div>
@endif
        <form class="simple_form form-horizontal form-dark" id="new_account" wire:submit.prevent="forgetPassword">
          <input name="utf8" type="hidden" value="&#x2713;" />
          <input type="hidden" name="authenticity_token" value="iDhrNIm1VQB4RbaxvMTcEl45UjvJPJAcz00IFmqUkOiPByv2EGHNXIE/lqn7evAhPQ3SZfPc3OUIA3nQC89eBQ==" />
          <div class="input-group input-group-lg">
            <input wire:model="email" class="string email optional form-control flat-bottom form-control" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-weight: normal;" maxlength="191" placeholder="Your Email" type="email" size="191"  id="account_email" />
            <div class="input-group-btn">
              <button class="btn btn-primary btn-lg" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;" data-btn-submit="" type="submit">Send</button>
            </div>
          </div>
        </form>
      </div>
      <div class="auth-other-links padding-bottom">
        <div class="list-group list-group-dark">
          <a class="list-group-item" href="{{url('sign-in')}}">Sign in</a>
          <a class="list-group-item " href="#" id="registerpopup">Register</a>
          <a class="list-group-item" href="/accounts/confirmation/new">Didn&#39;t receive confirmation email?</a>
        </div>
      </div>
    </div>
  </div>

  
