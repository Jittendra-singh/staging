<div class="modal fade" id="cohostsManage" tabindex="-1" aria-labelledby="cohostsManageLabel" aria-modal="true"
    role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cohostsManageLabel">Manage Co-Hosts</h5>
                <button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i
                        class="uil uil-multiply"></i></button>
            </div>
            <div class="modal-body">
                <div class="model-content main-form">
                    <div class="main-card mt-4">
                        <div class="help-item-link d-flex justify-content-between align-items-center">
                            <p>loremispum@gmail.com</p>
                            <span class="p-2 badge bg-success">Accepted</span>
                        </div>
                        <div class="help-item-link d-flex justify-content-between align-items-center">
                            <p>Johndoe@gmail.com</p>
                            <span class="p-2 badge bg-dark">Pending</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group mt-4">
                                <label class="form-label">Invite Co-hosts</label>
                                <div class="position-relative">
                                    <input class="form-control h_50" type="text" placeholder="Enter Email Address"
                                        value="" fdprocessedid="3ecse">
                                    <button class="primary-btn w-fit mt-2" type="button"
                                        fdprocessedid="6k236q">Invite</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="modal-footer">
                <button type="button" class="primary-btn w-fit"
                    data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="primary-btn w-fit">Update</button>
            </div> --}}
        </div>
    </div>
</div>

{{-- Model For Email --}}
<div class="modal fade detailed-modal-popup" id="sendRsvpEmail" tabindex="-1" aria-labelledby="sendRsvpEmailLabel" aria-modal="true"
    role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendRsvpEmailLabel">Send RSVP Email</h5>
                <button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i
                        class="uil uil-multiply"></i></button>
            </div>
            <form method="POST" action="{{route('front-send-rsvp-emails', ['slug' => 'slug'])}}">
                @csrf
                <div class="modal-body">
                    <div class="model-content main-form">
                        <div class="row ">
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label for="">Subject</label>
                                    <input type="text" name="subject" class="form-control mt-2" />
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label for="">Email Content</label>
                                    <textarea name="content" class="form-control mt-2" id="" cols="30" rows="10"></textarea>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="primary-btn w-fit" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="primary-btn w-fit">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

