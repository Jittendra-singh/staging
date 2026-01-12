@extends('layouts.backend')
@php 
$upcoming_class = 0;
$completed_class = 0;
$calendar_data = array();
foreach ($classes as $key => $class) {
    if($class->creator->role_id==TRAINER){
        $color = '#e04f1a';
        if($class->class_status==1){
            $upcoming_class++;
            $color = '#4c78dd';
        }
        if($class->class_status==2){
            $completed_class++;
            $color = '#82b54b';
        }
        $arr = array(
            'title'=>TR_PREFIX.'-'.$class->class_code,
            'date'=>date('Y-m-d',strtotime($class->class_timings)),
            'color'=>$color,
            'id'=>encrypt($class->id)
        );
        $calendar_data[] = $arr;
    }
    if($class->creator->role_id==INSTRUCTOR){
        $color = '#e04f1a';
        if($class->class_status==1){
            $upcoming_class++;
            $color = '#4c78dd';
        }
        if($class->class_status==2){
            $completed_class++;
            $color = '#82b54b';
        }
        $arr = array(
            'title'=>IN_PREFIX.'-'.$class->class_code,
            'date'=>date('Y-m-d',strtotime($class->class_timings)),
            'color'=>$color,
            'id'=>encrypt($class->id)
        );
        $calendar_data[] = $arr;
    }
}
@endphp 
@section('content')
    <!-- <div class="content">

        <div
            class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center py-2 text-center text-md-start">
            <div class="flex-grow-1 mb-1 mb-md-0">
                <h1 class="h3 fw-bold mb-2">
                    Dashboard
                </h1>
                <h2 class="h6 fw-medium fw-medium text-muted mb-0">
                    Welcome <a class="fw-semibold"
                        href="be_pages_generic_profile.html">{{ auth('sanctum')->user()->name }}</a>, everything looks great.
                </h2>
            </div>

        </div>

    </div> -->

    {{-- Dashboard content Heading --}}

    <div class="content">
        @if (false)
            <h4 class="text-center mt-7">Dashboard Coming Soon....</h4>
        @else
            <!-- Overview -->
            <div class="row items-push">
                <div class="col-sm-6 col-xxl-3">
                    <!-- Pending Orders -->
                    <div class="block block-rounded d-flex flex-column h-100 mb-0">
                        <div
                            class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                            <dl class="mb-0">
                                <dt class="fs-3 fw-bold">{{count($classes)}}</dt>
                                <dd class="fs-sm fw-medium fs-sm fw-medium text-muted mb-0">Total Classes</dd>
                            </dl>
                            <div class="item item-rounded-lg bg-body-light">
                                <i class="far fa-gem fs-3 text-primary"></i>
                            </div>
                        </div>
                        <div class="bg-body-light rounded-bottom">
                            <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between"
                                href="{{ url(config('app.admin_prefix') . 'created-classes') }}">
                                <span>View all Classes</span>
                                <i class="fa fa-arrow-alt-circle-right ms-1 opacity-25 fs-base"></i>
                            </a>
                        </div>
                    </div>
                    <!-- END Pending Orders -->
                </div>
                <div class="col-sm-6 col-xxl-3">
                    <!-- New Customers -->
                    <div class="block block-rounded d-flex flex-column h-100 mb-0">
                        <div
                            class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                            <dl class="mb-0">
                                <dt class="fs-3 fw-bold">{{$upcoming_class}}</dt>
                                <dd class="fs-sm fw-medium fs-sm fw-medium text-muted mb-0">Upcoming Classes</dd>
                            </dl>
                            <div class="item item-rounded-lg bg-body-light">
                                <i class="fa fa-person-military-rifle fs-3 text-primary"></i>
                            </div>
                        </div>
                        <div class="bg-body-light rounded-bottom">
                            <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between"
                                href="{{ url(config('app.admin_prefix') . 'joined-classes') }}">
                                <span>View all Upcoming Classes</span>
                                <i class="fa fa-arrow-alt-circle-right ms-1 opacity-25 fs-base"></i>
                            </a>
                        </div>
                    </div>
                    <!-- END New Customers -->
                </div>
                <div class="col-sm-6 col-xxl-3">
                    <!-- Conversion Rate -->
                    <div class="block block-rounded d-flex flex-column h-100 mb-0">
                        <div
                            class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                            <dl class="mb-0">
                                <dt class="fs-3 fw-bold">{{$completed_class}}</dt>
                                <dd class="fs-sm fw-medium fs-sm fw-medium text-muted mb-0">Completed Classes</dd>
                            </dl>
                            <div class="item item-rounded-lg bg-body-light">
                                <i class="fa fa-calendar-check fs-3 text-primary"></i>
                            </div>
                        </div>
                        <div class="bg-body-light rounded-bottom">
                            <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between"
                                href="{{ url(config('app.admin_prefix') . 'joined-classes') }}">
                                <span>View all Completed Classes</span>
                                <i class="fa fa-arrow-alt-circle-right ms-1 opacity-25 fs-base"></i>
                            </a>
                        </div>
                    </div>
                    <!-- END Conversion Rate-->
                </div>
                <div class="col-sm-6 col-xxl-3">
                    <!-- Messages -->
                    <div class="block block-rounded d-flex flex-column h-100 mb-0">
                        <div
                            class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center">
                            <dl class="mb-0">
                                <dt class="fs-3 fw-bold">${{$total_earning}}</dt>
                                <dd class="fs-sm fw-medium fs-sm fw-medium text-muted mb-0">Total Earnings</dd>
                            </dl>
                            <div class="item item-rounded-lg bg-body-light">
                                <i class="fa fa-circle-dollar-to-slot fs-3 text-primary"></i>
                            </div>
                        </div>
                        <div class="bg-body-light rounded-bottom">
                            <a class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between"
                                href="{{ url(config('app.admin_prefix') . 'created-classes') }}">
                                <span>View all Created Classes</span>
                                <i class="fa fa-arrow-alt-circle-right ms-1 opacity-25 fs-base"></i>
                            </a>
                        </div>
                    </div>
                    <!-- END Messages -->
                </div>
            </div>
            <!-- END Overview -->

            <div class="row">
                <div class="col-xl-12 col-xxl-12 d-flex flex-column">
                    <!-- Calendar -->
                    <div class="block block-rounded">
                        <div class="block-content">
                        <div class="row items-push">
                            <div class="col-md-12 col-lg-12 col-xl-12">
                            <!-- Calendar Container -->
                            <div data={{json_encode($calendar_data)}} id="js-calendar"></div>
                            </div>
                            <!-- <div class="col-md-4 col-lg-5 col-xl-3">
                                <form class="js-form-add-event push">
                                    <div class="input-group">
                                    <input type="text" class="js-add-event form-control" placeholder="Add Event..">
                                    <span class="input-group-text">
                                        <i class="fa fa-fw fa-plus-circle"></i>
                                    </span>
                                    </div>
                                </form>
                                <ul id="js-events" class="list list-events">
                                    <li>
                                    <div class="js-event p-2 fs-sm fw-medium rounded bg-info-light text-info">Codename X</div>
                                    </li>
                                    <li>
                                    <div class="js-event p-2 fs-sm fw-medium rounded bg-success-light text-success">Weekend Adventure</div>
                                    </li>
                                    <li>
                                    <div class="js-event p-2 fs-sm fw-medium rounded bg-info-light text-info">Project Mars</div>
                                    </li>
                                    <li>
                                    <div class="js-event p-2 fs-sm fw-medium rounded bg-warning-light text-warning">Meeting</div>
                                    </li>
                                    <li>
                                    <div class="js-event p-2 fs-sm fw-medium rounded bg-success-light text-success">Walk the dog</div>
                                    </li>
                                    <li>
                                    <div class="js-event p-2 fs-sm fw-medium rounded bg-info-light text-info">AI schedule</div>
                                    </li>
                                    <li>
                                    <div class="js-event p-2 fs-sm fw-medium rounded bg-success-light text-success">Cinema</div>
                                    </li>
                                    <li>
                                    <div class="js-event p-2 fs-sm fw-medium rounded bg-danger-light text-danger">Project X</div>
                                    </li>
                                    <li>
                                    <div class="js-event p-2 fs-sm fw-medium rounded bg-warning-light text-warning">Skype Meeting</div>
                                    </li>
                                </ul>
                                <div class="text-center">
                                    <p class="fs-sm text-muted">
                                    <i class="fa fa-arrows-alt"></i> Drag and drop events on the calendar
                                    </p>
                                </div>
                            </div> -->
                        </div>
                        </div>
                    </div>
                    <!-- END Calendar -->
                </div>
            </div>

        @endif
        <!-- END Recent Orders -->
    </div>
@endsection
