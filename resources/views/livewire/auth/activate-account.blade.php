<div>
    @if($done == 'success')
    <div class="jumbotron">
        <h1 class="display-4">Success!</h1>
        <p class="lead">Your Account is activated successfully, now you can login to your account</p>
        <hr class="my-4">
        
        <p class="lead">
          <a class="btn btn-primary btn-lg" href="{{route('sign-in')}}" role="button">Sign in</a>
        </p>
      </div>
    @else
    <h2>Error! Please try again</h2>
    @endif
</div>
