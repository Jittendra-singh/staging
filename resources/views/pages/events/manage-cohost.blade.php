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
@endsection
@section('js')
    <x-reusables.advanceformjs />
@endsection
@section('content')
    <div class="row">
        <!-- Invite Volunteer Section -->
        <div class="col-md-12">
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
        </div>
        <!-- Cohosts Listing Section -->
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Co-Hosts List</h5>
                    <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#editCohostPermissions">Manage Permissions</button>
                </div>
                <div class="card-body">
                    @if($cohosts->count())
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Volunteer</th>
                                    <th>Status</th>
                                    <th>Invited At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cohosts as $cohost)
                                @php
                                    $class = $cohost->pivot->request_status == 'pending' ? 'info' : ($cohost->pivot->request_status == 'approved' ? 'success' : 'danger');
                                @endphp
                                    <tr>
                                        <td>{{ $cohost->name }} ({{ $cohost->email_address }})</td>
                                        <td><span class="badge bg-{{$class}}-subtle text-{{$class}}">{{ ucfirst($cohost->pivot->request_status) }}</span></td>
                                        <td>{{ \Carbon\Carbon::parse($cohost->pivot->created_at)->format(config('app.dateFormat')) }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                @if($cohost->pivot->request_status != 'approved')
                                                    <span>
                                                        <a title="Resend Link" href="{{route('resend.cohost.invitation',['eventPlaceholder'=> $event->encrypted_id, 'volunteerPlaceholder'=>$cohost->encrypted_id])}}">
                                                            <i class="fs-5 mdi mdi-share-all-outline"></i>
                                                        </a>
                                                    </span>
                                                @endif
                                                <span>
                                                        <a title="Delete Event" href="{{route('delete.cohost', ['placeholder'=> $cohost->pivot->id])}}">
                                                            <i class="fs-5 mdi mdi-delete"></i>
                                                        </a>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No Co-Hosts invited yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- edit cohost permissions --}}
    <div class="modal fade" id="editCohostPermissions" tabindex="-1" aria-labelledby="editCohostPermissionsLabel" aria-hidden="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
                <form action="{{route('update-event-permissions', ['placeholder' => $event->encrypted_id])}}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCohostPermissionsLabel">Manage Co-Host Permissions</h5>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="model-content main-form">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        @foreach ($cohosts as $cohost)
                                            <th>{{ $cohost->name }} </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (config('app.cohost_permissions') as $permission_key => $permission)
                                        <tr>
                                            <th>{{$permission}}</th>
                                            @foreach($cohosts as $cohost)
                                                @php
                                                    $checked = \App\Models\EventCoHost::where('event_id', $event->id)->where('volunteer_id', $cohost->id)
                                                                ->whereHas('permissions', function($q) use($permission_key){
                                                                    $q->where('permission_name', $permission_key);
                                                                })->exists() ? 'checked' : '';
                                                @endphp
                                                <td>
                                                    <div class="square-switch">
                                                        <input switch="none" {{$checked}} type="checkbox" name="manage_permission[]"
                                                                value="{{$cohost->id}}-{{$permission_key}}" 
                                                                id="switch{{$permission_key}}{{$cohost->id}}" 
                                                        />
                                                        <label for="switch{{$permission_key}}{{$cohost->id}}" data-on-label="Edit" data-off-label="View"></label>
                                                    </div>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
			</div>
		</div>
	</div>
@endsection