{{-- <x-reusables.reusables.fab-button :title="$pageName" module="volunteer" :url="route('create-volunteer')" /> --}}
@can('create', $module)
    <div class="row">
        <div class="col-sm">
            <div class="mb-4">
                <a href="{{ $url }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i>
                    Add {{ ucfirst($title ?? '') }}</a>
            </div>
        </div>
    </div>
@endcan
