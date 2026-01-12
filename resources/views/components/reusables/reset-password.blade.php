    {{-- Password reset modal --}}
    <div id="forgot-password-modal" class="modal fade modal-md forgot-password-modal " tabindex="-1" aria-labelledby="forgot-password-modalLabel"
        aria-hidden="true" data-bs-scroll="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgot-password-modalLabel">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form  action="#" method="POST" class="resetPasswordValidateform">
                        @csrf
                        <input type="hidden" id="volunteer_id" name="volunteer_id" >
                        <div class="row">
                            <div class="col-sm-12 mt-4">
                                <label for="password" class="form-label">Password</label>
                                <input class="form-control" type="password" name="password" id="password" required>
                            </div>
                            <div class="col-sm-12 mt-4">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input class="form-control" type="password" name="password_confirmation" id="password_confirmation" required>
                            </div>    <div class="col-sm-12 mt-4 text-end">
                                    <button type="button" class="btn btn-light waves-effect me-3" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>