<div class="d-flex gap-2 justify-content-start action-button">
    @can('view' , $module)
        @if($module == 'orders')
                <a href="{{route('view-order-details', ['placeholder' => $id])}}" data-name="{{ $name }}" >
                    <i class="fs-5 mdi mdi-arrow-expand" title="View Details"></i>
                </a>
        @endif
    @endcan
    @can('update', $module)
        <a href="{{ isset($editurl) ? $editurl : url(config('app.admin_prefix') . "manage-$module/$id") }}">
            <i class="fs-5 mdi mdi-pencil"  title="Edit Record"></i>
        </a>
        @if ($module == 'user' || $module == 'volunteers')
            @php
                $targetUrl = route( $module == 'user' ? 'manage-user-password' : 'manage-volunteer-password' , ['placeholder' => $id])
            @endphp
            <a class="update-user-credentials" href="javascript:void(0);" data-id="{{ $id }}"
                data-name="{{ $name }}" data-bs-toggle="modal" data-action = "{{$targetUrl}}"
                data-bs-target="#forgot-password-modal">
                <i class="fs-5 mdi mdi-lock" title="Reset Password"></i>
            </a>
        @endif
    @endcan
    @can('delete', $module)
        <a data-id="{{ $id }}" data-module="{{ $deleteurl ??  $module }}" data-name="{{ $name }}"
            class="delete-record js-swal-confirm" href="javascript:void(0);">
            <i class="fs-5 mdi mdi-delete" title="Delete Record"></i>
        </a>
    @endcan
    @if($module == 'volunteers')
        @can('update' , $module)
            <div class="dropdown">
                <button class="btn btn-link font-size-16 text-primary py-0 px-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" >
                    <i class="bx bx-dots-horizontal-rounded"></i> 
                </button>
                <ul class="dropdown-menu drop-menu-end" >
                    <li class="dropdown-item">
                        <a href="javascript:void(0);" data-id="{{ $id }}" data-name="{{ $name }}" data-bs-toggle="modal" data-action = "{{$targetUrl}}" data-bs-target="#volunteer-report-modal" data-details="report-details">
                            <i class="fs-5 mdi mdi-chart-areaspline" title="View Report"></i> 
                            <span class="text-muted">View Report</span>
                        </a>
                    </li> 
                    <li class="dropdown-item">
                        <a target="_blank" href="{{route('index-orders', ['purchased_by_id' => $data->id])}}">
                            <i class="fs-5 mdi mdi-order-bool-ascending"></i> <span class="text-muted">View Orders</span>    
                        </a>
                    </li>
                    @if($data->is_organiser)
                    <li class="dropdown-item">
                        <a target="_blank" href="{{route('index-events', ['organiser_id' => $data->id])}}">
                            <i class="fs-5 mdi mdi-map-marker-plus-outline"></i> <span class="text-muted">View Organized Events ({{ $data->totals['total_organised_events'] }})</span>
                        </a>
                    </li>
                    <li class="dropdown-item">
                        <a target="_blank" href="{{route('index-events', ['cohost_id' => $data->id])}}">
                            <i class="fs-5 mdi mdi-map-marker-right-outline"></i> <span class="text-muted">View Co-Hosting Events ({{ $data->totals['total_cohosting_events'] }})</span>
                        </a>
                    </li>
                    @endif
                    <li class="dropdown-item">
                        <a target="_blank" href="{{route('index-events', ['volunteer_id' => $data->id])}}">
                            <i class="fs-5 mdi mdi-map-marker-radius-outline"></i> <span class="text-muted">View Volunteered Events ({{ $data->totals['total_volunteered_events'] }})</span>
                        </a>
                    </li>
                </ul>
            </div>
        @endcan
    @endif

    @if($module == 'events')
        @can('update' , $module)
            <div class="dropdown dropstart">
                <button class="btn btn-link font-size-16 text-primary py-0 px-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" >
                    <i class="bx bx-dots-horizontal-rounded"></i> 
                </button>
                <ul class="dropdown-menu drop-menu-end" >
                    @if(!empty($data->slug))
                    <li class="dropdown-item">
                        <a target="_BLANK"  title="Visit in Frontend" href="{{ route('view-event-details',['slug'=>$data->slug]) }}" oldhref="{{config('app.frontsite').'event/'.$data->slug}}">
                            <i class="fs-5 mdi mdi-share"></i> <span class="text-muted">View</span>    
                        </a>
                    </li>
                    @endif
                    <li class="dropdown-item">
                        <a href="{{route('clone-event', ['placeholder' => $id])}}" title="Clone Event">
                            <i class="fs-5 mdi mdi-content-duplicate"></i> <span class="text-muted">Clone Event</span>
                        </a>
                    </li>
                    <li class="dropdown-item">
                        <a target="_blank" href="{{route('index-orders', ['event_id' => $data->id])}}" title="View Orders">
                            <i class="fs-5 mdi mdi-order-bool-ascending"></i> <span class="text-muted">View Orders</span>
                        </a>
                    </li>
                    <!-- <li class="dropdown-item">
                        <a href="{{route('manage-event-hosts', ['placeholder' => $id])}}"
                            data-id="{{ $id }}" data-name="{{ $name }}" >
                            <i class="fs-5 mdi mdi-account-multiple-plus" title="Manage Co-host"></i> 
                            <span class="text-muted">Manage Co-host</span>
                        </a>
                    </li>  -->
                    <li class="dropdown-item">
                        <a href="{{route('manage-event-hosts', ['placeholder' => $id])}}" title="View Attendees">
                            <i class="fs-5 mdi mdi-account-multiple-plus"></i> <span class="text-muted">Manage Co-Host</span>
                        </a>
                    </li>
                    <li class="dropdown-item">
                        <a href="{{route('post-event-action', ['placeholder' => $id])}}" title="View Attendees">
                            <i class="fs-5 mdi mdi-ticket-confirmation-outline"></i> <span class="text-muted">Volunteers & Other Actions</span>
                        </a>
                    </li>
                </ul>
            </div>
        @endcan
    @endif