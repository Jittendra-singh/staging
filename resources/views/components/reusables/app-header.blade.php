<div class="bg-body-light">
    <!-- <div class="content content-full py-2"> -->
    <div class="row content-full py-2 mx-4">
        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center py-2">
            <div class="flex-grow-1">
                <h1 class="h3 fw-bold mb-2">
                    {{ $pageName  }}
                </h1>
            </div>
            @if (isset($createButton) && $createButton )
                @can( 'create'  , $module)
                    <nav class="flex-shrink-0 mt-3 mt-sm-0 ms-sm-3" aria-label="breadcrumb">
                        <a class="btn btn-primary" href="{{ url(config('app.admin_prefix') . "manage-$module") }}">
                            <i class="fa fa-plus"></i> Add {{ ucfirst($modulePlaceholder ?? $module) }}
                        </a>
                    </nav>
                @endcan
            @endif
        </div>
    </div>
</div>
