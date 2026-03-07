@php
    $pageName       = 'Manage Co-Hosts - '.$event->event_name;
    $parentPageName = $module;
    $parentRoute    = route('index-events'); 
    $cohosts        = $event->cohosts;
@endphp
@extends('layouts.backend_new', ['pageName' => $pageName, 'parentPageName' => $parentPageName, 'parentRoute' => $parentRoute,'breadcrumb' => [
    ['name' => 'Events', 'route' => route('index-events')],
    ['name' => 'Manage Co-Host', 'route' => ''],
]])
@section('css')
    <x-reusables.advanceformcss />
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('js')
    <x-reusables.advanceformjs />
    
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
@endsection
@section('content')
    <div class="row">
        <!-- Invite Volunteer Section -->
        <!-- <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('events.cohosts.invite', ['placeholder' => $event->encrypted_id]) }}" method="POST">
                        @csrf
                        <div class="d-flex justify-content-start gap-3">
                            <div class="form-group w-100">
                                <label for="new_volunteer">Invite Co-Host </label>
                                <input type="email" class="form-control" name="volunteer_email_address" required placeholder="Enter Email Address">
                            </div>
                            <div class="mt-2">
                                <label for="volunteer_id" class="form-label">&#32;</label>
                                <button type="submit" class="btn btn-primary">Send Invite</button>
                            </div>
                        </div> 
                    </form>
                </div>
            </div>
        </div> -->
        <!-- Cohosts Listing Section -->
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Co-Hosts List</h5>
                    <!-- <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#editCohostPermissions">Manage Permissions</button> -->
                    <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#addCohost">Add Co-Host</button>
                </div>
                <x-reusables.frontend.alert />
                <div class="card-body">
                    @if($cohosts->count())
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Volunteer</th>
                                    <th>Status</th>
                                    <th>Requested Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!isset($event->id) || (isset($event->id) && $event->cohostRecords->isEmpty()))
                                    <tr>
                                        <td colspan="4" class="text-center">No Co-Hosts Found</td>
                                    </tr>
                                @else
                                    @foreach ($event->cohostRecords as $cohost)
                                        <td>{{ $cohost->volunteer->name }} ({{ $cohost->volunteer->email_address }})</td>
                                        <td>{{ ucfirst($cohost->request_status) }}</td>
                                        <td>{{ date(config('app.dateFormat'),strtotime($cohost->created_at)) }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                @if($cohost->request_status != 'approved')
                                                    <span>
                                                        <a title="Resend Link" href="{{route('resend.cohost.invitation',['placeholder'=> encrypt($cohost->id)])}}">
                                                            <i class="fs-5 mdi mdi-share-all-outline"></i>
                                                        </a>
                                                    </span>
                                                @endif
                                                <span>
                                                        <a title="Delete Event" class="remove-cohost" href="javascript:void(0);" url="{{route('delete.cohost', ['placeholder'=> encrypt($cohost->id)])}}">
                                                            <i class="fs-5 mdi mdi-delete"></i>
                                                        </a>
                                                </span>
                                            </div>
                                        </td>
                                    @endforeach
                                @endif

                            </tbody>
                        </table>
                    @else
                        <p class="text-center">No Co-Hosts invited yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addCohost" tabindex="-1" aria-labelledby="addCohostLabel" aria-hidden="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
                <form action="{{route('events.cohosts.invite', ['placeholder' => $event->encrypted_id])}}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCohostLabel">Add Co-Host</h5>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="model-content main-form">
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Name*</label>
                                        <input class="form-control h_40" required type="text" name="name" id="co_host_name">						
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Email*</label>
                                        <input class="form-control h_40" required type="email" name="email" id="co_host_email">							
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12">
                                    <div class="form-group mt-4">
                                        <label class="form-label">Include a Note (optional)</label>
                                        <textarea class="form-control" name="extra_notes" id="co_host_extra_notes" rows="4"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 permission_selector" style="display: none;">
                                    <div class="form-group mt-4">
                                        <label class="form-label">Select Permissions*</label>
                                                                                <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1" id="permission_1" name="permissions[]" checked="">
                                                <label class="form-check-label" for="permission_1">Edit event details (title, description, date, location).</label>
                                            </div>
                                                                                <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="2" id="permission_2" name="permissions[]" checked="">
                                                <label class="form-check-label" for="permission_2">View and export registration lists.</label>
                                            </div>
                                                                                <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="3" id="permission_3" name="permissions[]" checked="">
                                                <label class="form-check-label" for="permission_3">Check in volunteers and mark attendance.</label>
                                            </div>
                                                                                <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="4" id="permission_4" name="permissions[]" checked="">
                                                <label class="form-check-label" for="permission_4">Approve or deny community-service certificate requests.</label>
                                            </div>
                                                                                <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="5" id="permission_5" name="permissions[]" checked="">
                                                <label class="form-check-label" for="permission_5">Send messages or updates to attendees.</label>
                                            </div>
                                                                                <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="6" id="permission_6" name="permissions[]" checked="">
                                                <label class="form-check-label" for="permission_6">View reports and metrics.</label>
                                            </div>
                                                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>
			</div>
		</div>
	</div>
@endsection