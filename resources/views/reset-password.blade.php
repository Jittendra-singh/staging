@extends('layouts.baselayout')
@section('content')
    
      <form class="js-validation-signin"  method="POST">
            @csrf
            <div class="py-3">
                  <div class="mb-4">
                        <input type="password" required class="form-control" name="reset_password" placeholder="New Password" />
                  </div>
                  <div class="mb-4">
                        <input type="password" required class="form-control" name="reset_password_confirmed" placeholder="Confirm Password" />
                  </div>
                  
            </div>
            <x-reusables.frontend.alert />
            <div class="row mb-4">
                  <div class="col-md-6 col-xl-5">
                  <button type="submit" class="btn w-100 btn-alt-primary">
                  <i class="fa fa-fw fa-sign-in-alt me-1 opacity-50"></i> Submit
                  </button>
                  </div>

            </div>
      </form>
             
@endsection