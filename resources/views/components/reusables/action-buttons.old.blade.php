<div class="dropdown">
    <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle" type="button"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bx bx-dots-horizontal-rounded"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        @can('view' , $module)
            @if($module == 'orders')
                <li>
                    <a class="dropdown-item " href="{{route('view-order-details', ['placeholder' => $id])}}" data-name="{{ $name }}" >View Details</a>
                </li>
            @endif
        @endcan
        @can('update', $module)
            <li><a class="dropdown-item" href="{{ isset($editurl) ? $editurl : url(config('app.admin_prefix') . "manage-$module/$id") }}">Edit</a></li>
            @if ($module == 'user' || $module == 'volunteers')
                @php
                    $targetUrl = route( $module == 'user' ? 'manage-user-password' : 'manage-volunteer-password' , ['placeholder' => $id])
                @endphp
                <li>
                    <a class="dropdown-item update-user-credentials" href="javascript:void(0);" data-id="{{ $id }}"
                        data-name="{{ $name }}" data-bs-toggle="modal" data-action = "{{$targetUrl}}"
                        data-bs-target="#forgot-password-modal">Update Password</a>
                </li>
            @endif
            @if ($module == 'volunteers' && $data->is_organiser == 1)
                <li>
                    <a class="dropdown-item update-organisation-details" href="javascript:void(0);"
                        data-id="{{ $id }}" data-name="{{ $name }}" data-bs-toggle="modal"
                        data-bs-target="#volunteer-organisation-modal">Org. Details
                    </a>
                </li>  
            @endif
            @if($module == 'events')
                <li>
                    <a class="dropdown-item" href="{{route('manage-event-hosts', ['placeholder' => $id])}}"
                        data-id="{{ $id }}" data-name="{{ $name }}" >Manage Co-Hosts
                    </a>
                </li>
            @endif
        @endcan
        @can('delete', $module)
            <li><a data-id="{{ $id }}" data-module="{{ $deleteurl ??  $module }}" data-name="{{ $name }}"
                    class="dropdown-item delete-record js-swal-confirm" href="javascript:void(0);">Delete</a></li>
        @endcan
    </ul>
</div>
