<div class="p-4">
    <h3>Create Customer</h3>
   <form wire:submit="save"> 
        <div class="form-group">
    <label for="exampleInputEmail1">Name</label>
    <input type="text" wire:model="name" class="form-control" name="name" aria-describedby="emailHelp" placeholder="Enter email">
  
  </div>
  <div class="form-group">
    <label for="exampleInputEmail1">Email address</label>
    <input type="email" wire:model="email" class="form-control" name="email" aria-describedby="emailHelp" placeholder="Enter email">
  
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Password</label>
    <input type="password" wire:model="password" class="form-control" name="password" placeholder="Password">
  </div>
  <div class="form-check">
    <input type="checkbox" class="form-check-input" id="exampleCheck1">
    <label class="form-check-label" for="exampleCheck1">Check me out</label>
  </div>
  <button type="submit"  class="btn btn-primary">Submit</button>
</form>
</div>
