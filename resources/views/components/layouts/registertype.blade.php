
<div class="modal" id="registertype" style="display: none">
    <div class="modal-md modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button class="close" type="button"  id="closereg">
            <i class="fa fa-times" ></i>
          </button>
          <h2 class="modal-title">Register now</h2>
        </div>
        <div class="modal-body">
          <div class="row" id="register-account-type-selection">
            <div class="col-xs-6 text-center">
              <div class="account-type" id="account-type-user">
                <h1>User</h1>
                <a href="/register">
                  <span class="register-graphic user-graphic"></span>
                </a>
                <p>Keep updated on <br> activity in your area! </p>
                <a class="btn btn-lg btn-primary btn-xs-block" href="{{route('register')}}" wire:navigate>Register</a>
              </div>
            </div>
            <div class="col-xs-6 text-center">
              <div class="account-type border-left" id="account-type-escort">
                <h1>Advertiser</h1>
                <a href="/escort-sign-up">
                  <span class="register-graphic escort-graphic"></span>
                </a>
                <p>Get listed <br> for free today! </p>
                <a class="btn btn-lg btn-primary btn-xs-block" href="{{route('escort.signup')}}" wire:navigate>Register</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    $("a#registerpopup").click(function(e){
      e.preventDefault();
      $("div#registertype").css('display', 'block');
    });
    
    $("button#closereg").click(function(){
    
      $("div#registertype").css('display', 'none');
    });
      </script>