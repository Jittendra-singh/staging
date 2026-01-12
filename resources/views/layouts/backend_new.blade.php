@php
    $routePrefix = request()->path();
    $parentRoute = isset($parentRoute) && $parentRoute ? config('app.admin_prefix').$parentRoute : ''; 
    $userFirstCharacter = Auth::user() ? substr(Auth::user()->name, 0, 1) : '#';
    $role_id = Auth::user() ? Auth::user()->role_id : 1;
    $userName = Auth::guard('web')->user() ? Auth::guard('web')->user()->name : 'ADMIN';
@endphp
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>{{ config('app.app_name') }} | {{ $pageName ?? '' }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="" name="description" />
        <meta content="Devwings" name="author" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{asset('assets/images/favicon.png')}}">
        <!-- preloader css -->
        <link rel="stylesheet" href="{{asset('assets/css/preloader.min.css')}}" type="text/css" />
        <!-- Bootstrap Css -->
        <link href="{{asset('assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{asset('assets/css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />
        @yield('css')
        <link href="{{asset('assets/css/custom.css?v='.time())}}" id="app-style" rel="stylesheet" type="text/css" />
        <style>
        img.lazy {
            background-image: url('@php echo url('/'); @endphp/assets/images/loader1.gif');
            background-repeat: no-repeat;
            background-position: 50% 50%;
        }
        </style>
        <script>
            const base = "{!! url(config('app.admin_prefix')) !!}";
            const weburl = "{!! url('/') !!}";
        </script>
    </head>
    <body>
        <!-- Begin page -->
        <div id="layout-wrapper">
            <header id="page-topbar">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box">
                            <a href="javascript:void(0);" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{asset($settings->logo)}}" alt="" height="20">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{asset($settings->logo)}}" alt="" height="60">
                                </span>
                            </a>
                            <a href="javascript:void(0);" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{asset($settings->logo)}}" alt="" height="20">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{asset($settings->logo)}}" alt="" height="60">
                                </span>
                            </a>
                        </div>
                        <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>
                    </div>
                    <div class="d-flex">
                        {{-- <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item noti-icon position-relative" id="page-header-notifications-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i data-feather="bell" class="icon-lg"></i>
                                <span class="badge bg-danger rounded-pill">5</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                aria-labelledby="page-header-notifications-dropdown">
                                <div class="p-3">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="m-0"> Notifications </h6>
                                        </div>
                                        <div class="col-auto">
                                            <a href="#!" class="small text-reset text-decoration-underline"> Unread (3)</a>
                                        </div>
                                    </div>
                                </div>
                                <div data-simplebar style="max-height: 230px;">
                                    <a href="#!" class="text-reset notification-item">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <img src="{{asset('assets/images/users/avatar-3.jpg')}}" class="rounded-circle avatar-sm" alt="user-pic">
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">James Lemire</h6>
                                                <div class="font-size-13 text-muted">
                                                    <p class="mb-1">It will seem like simplified English.</p>
                                                    <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span>1 hour ago</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#!" class="text-reset notification-item">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-sm me-3">
                                                <span class="avatar-title bg-primary rounded-circle font-size-16">
                                                    <i class="bx bx-cart"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">Your order is placed</h6>
                                                <div class="font-size-13 text-muted">
                                                    <p class="mb-1">If several languages coalesce the grammar</p>
                                                    <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span>3 min ago</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#!" class="text-reset notification-item">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 avatar-sm me-3">
                                                <span class="avatar-title bg-success rounded-circle font-size-16">
                                                    <i class="bx bx-badge-check"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">Your item is shipped</h6>
                                                <div class="font-size-13 text-muted">
                                                    <p class="mb-1">If several languages coalesce the grammar</p>
                                                    <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span>3 min ago</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#!" class="text-reset notification-item">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <img src="{{asset('assets/images/users/avatar-6.jpg')}}" class="rounded-circle avatar-sm" alt="user-pic">
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">Salena Layfield</h6>
                                                <div class="font-size-13 text-muted">
                                                    <p class="mb-1">As a skeptical Cambridge friend of mine occidental.</p>
                                                    <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span>1 hour ago</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-2 border-top d-grid">
                                    <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
                                        <i class="mdi mdi-arrow-right-circle me-1"></i> <span>View More..</span> 
                                    </a>
                                </div>
                            </div>
                        </div> --}}
                        {{-- <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item right-bar-toggle me-2">
                                <i data-feather="settings" class="icon-lg"></i>
                            </button>
                        </div> --}}
                        <div class="dropdown d-inline-block">
                            {{-- id="page-header-user-dropdown" --}}
                            <button type="button" class="btn header-item bg-light-subtle border-start border-end " id="topnav-menu-content" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium">{{$userName}}</span>
                                <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                {{-- <a class="dropdown-item" href="apps-contacts-profile.html"><i class="mdi mdi mdi-face-man font-size-16 align-middle me-1"></i> Profile</a>
                                <a class="dropdown-item" href="auth-lock-screen.html"><i class="mdi mdi-lock font-size-16 align-middle me-1"></i> Lock Screen</a> 
                                <div class="dropdown-divider"></div> --}}
                                <a class="dropdown-item" href="{{route('logout')}}"><i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </header><!-- ========== Left Sidebar Start ========== -->
            <div class="vertical-menu">
                <div data-simplebar class="h-100">
                    <!--- Sidemenu -->
                    <div id="sidebar-menu">
                        <!-- Left Menu Start -->
                        <ul class="metismenu list-unstyled" id="side-menu">
                            <li class="menu-title" data-key="t-menu">Menu</li>
                            @foreach ($parentMenus as $parentMenu)
                            @php
                                if($parentMenu->is_active==0){
                                    continue;
                                }
                                $otherClass = count($parentMenu->childMenus)  ? 'has-arrow' : '';
                                $menu_arr = $parentMenu->toArray();
                                $parentMenuOpen = '';
                                if(isset($menu_arr['child_menus']) && !empty(isset($menu_arr['child_menus']))){
                                    foreach($menu_arr['child_menus'] as $cm){
                                        if (str_contains($routePrefix, $cm['menu_href'])) { 
                                            $parentMenuOpen = 'mm-active';
                                        }
                                    }
                                }
                            @endphp
                            <li class="{{$parentMenuOpen}} {{ in_array(config('app.admin_prefix') . $parentMenu['menu_href'], [$routePrefix, $parentRoute]) ? 'mm-active' : '' }}">
                                <a href="{{ $parentMenu['menu_href'] ? url(config('app.admin_prefix') . $parentMenu['menu_href']) : 'javascript:void(0);' }}"  class="{{$otherClass}}">
                                    <i class="mdi {{$parentMenu['menu_icon']}}"></i>
                                    <span>{{ $parentMenu['menu_placeholder'] }}</span>
                                </a>
                                @if (count($parentMenu->childMenus))
                                <ul class="sub-menu" aria-expanded="false">
                                    @foreach ($parentMenu->childMenus as $childMenu)
                                        @if($childMenu->is_active==0)
                                            @continue;
                                        @endif 
                                        <li><a href="{{ url(config('app.admin_prefix') . $childMenu['menu_href']) }}">{{$childMenu['menu_placeholder']}}</a></li>
                                    @endforeach
                                </ul>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <!-- Sidebar -->
                </div>
            </div>
            <!-- Left Sidebar End -->
            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">
                <div class="page-content">
                    <div class="container-fluid">
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12  {{!empty($breadcrumb)?'':'d-flex justify-content-between'}}">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">{{$pageName}}</h4>
                                    @if(!empty($breadcrumb))
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            @foreach($breadcrumb as $item)
                                                <li class="breadcrumb-item {{ $item['route'] != '' ? '' : 'active' }}">
                                                    @if($item['route'] != '')
                                                        <a href="{{ $item['route'] }}">{{ $item['name'] }}</a>
                                                    @else
                                                        {{ $item['name'] }}
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ol>
                                    </div>
                                    @endif
                                </div>
                                @yield('buttons')
                            </div>
                        </div>
                        <!-- end page title -->
                        @yield('content')
                    </div> <!-- container-fluid -->
                </div>
                <!-- End Page-content -->
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <script>document.write(new Date().getFullYear())</script> © {{ config('app.app_name') }}.
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
            <!-- end main content-->
        </div>
        <!-- END layout-wrapper -->
        <!-- Right Sidebar -->
        <div class="right-bar">
            <div data-simplebar class="h-100">
                <div class="rightbar-title d-flex align-items-center p-3">
                    <h5 class="m-0 me-2">Theme Customizer</h5>
                    <a href="javascript:void(0);" class="right-bar-toggle ms-auto">
                        <i class="mdi mdi-close noti-icon"></i>
                    </a>
                </div>
                <!-- Settings -->
                <hr class="m-0" />
                <div class="p-4">
                    <h6 class="mb-3">Layout</h6>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout"
                            id="layout-vertical" value="vertical">
                        <label class="form-check-label" for="layout-vertical">Vertical</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout"
                            id="layout-horizontal" value="horizontal">
                        <label class="form-check-label" for="layout-horizontal">Horizontal</label>
                    </div>
                    <h6 class="mt-4 mb-3 pt-2">Layout Mode</h6>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout-mode"
                            id="layout-mode-light" value="light">
                        <label class="form-check-label" for="layout-mode-light">Light</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout-mode"
                            id="layout-mode-dark" value="dark">
                        <label class="form-check-label" for="layout-mode-dark">Dark</label>
                    </div>
                    <h6 class="mt-4 mb-3 pt-2">Layout Width</h6>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout-width"
                            id="layout-width-fuild" value="fuild" onchange="document.body.setAttribute('data-layout-size', 'fluid')">
                        <label class="form-check-label" for="layout-width-fuild">Fluid</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout-width"
                            id="layout-width-boxed" value="boxed" onchange="document.body.setAttribute('data-layout-size', 'boxed')">
                        <label class="form-check-label" for="layout-width-boxed">Boxed</label>
                    </div>
                    <h6 class="mt-4 mb-3 pt-2">Layout Position</h6>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout-position"
                            id="layout-position-fixed" value="fixed" onchange="document.body.setAttribute('data-layout-scrollable', 'false')">
                        <label class="form-check-label" for="layout-position-fixed">Fixed</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout-position"
                            id="layout-position-scrollable" value="scrollable" onchange="document.body.setAttribute('data-layout-scrollable', 'true')">
                        <label class="form-check-label" for="layout-position-scrollable">Scrollable</label>
                    </div>
                    <h6 class="mt-4 mb-3 pt-2">Topbar Color</h6>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="topbar-color"
                            id="topbar-color-light" value="light" onchange="document.body.setAttribute('data-topbar', 'light')">
                        <label class="form-check-label" for="topbar-color-light">Light</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="topbar-color"
                            id="topbar-color-dark" value="dark" onchange="document.body.setAttribute('data-topbar', 'dark')">
                        <label class="form-check-label" for="topbar-color-dark">Dark</label>
                    </div>
                    <h6 class="mt-4 mb-3 pt-2 sidebar-setting">Sidebar Size</h6>
                    <div class="form-check sidebar-setting">
                        <input class="form-check-input" type="radio" name="sidebar-size"
                            id="sidebar-size-default" value="default" onchange="document.body.setAttribute('data-sidebar-size', 'lg')">
                        <label class="form-check-label" for="sidebar-size-default">Default</label>
                    </div>
                    <div class="form-check sidebar-setting">
                        <input class="form-check-input" type="radio" name="sidebar-size"
                            id="sidebar-size-compact" value="compact" onchange="document.body.setAttribute('data-sidebar-size', 'md')">
                        <label class="form-check-label" for="sidebar-size-compact">Compact</label>
                    </div>
                    <div class="form-check sidebar-setting">
                        <input class="form-check-input" type="radio" name="sidebar-size"
                            id="sidebar-size-small" value="small" onchange="document.body.setAttribute('data-sidebar-size', 'sm')">
                        <label class="form-check-label" for="sidebar-size-small">Small (Icon View)</label>
                    </div>
                    <h6 class="mt-4 mb-3 pt-2 sidebar-setting">Sidebar Color</h6>
                    <div class="form-check sidebar-setting">
                        <input class="form-check-input" type="radio" name="sidebar-color"
                            id="sidebar-color-light" value="light" onchange="document.body.setAttribute('data-sidebar', 'light')">
                        <label class="form-check-label" for="sidebar-color-light">Light</label>
                    </div>
                    <div class="form-check sidebar-setting">
                        <input class="form-check-input" type="radio" name="sidebar-color"
                            id="sidebar-color-dark" value="dark" onchange="document.body.setAttribute('data-sidebar', 'dark')">
                        <label class="form-check-label" for="sidebar-color-dark">Dark</label>
                    </div>
                    <div class="form-check sidebar-setting">
                        <input class="form-check-input" type="radio" name="sidebar-color"
                            id="sidebar-color-brand" value="brand" onchange="document.body.setAttribute('data-sidebar', 'brand')">
                        <label class="form-check-label" for="sidebar-color-brand">Brand</label>
                    </div>
                    <h6 class="mt-4 mb-3 pt-2">Direction</h6>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout-direction"
                            id="layout-direction-ltr" value="ltr">
                        <label class="form-check-label" for="layout-direction-ltr">LTR</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="layout-direction"
                            id="layout-direction-rtl" value="rtl">
                        <label class="form-check-label" for="layout-direction-rtl">RTL</label>
                    </div>
                </div>
            </div> <!-- end slimscroll-menu-->
        </div>
        <!-- /Right-bar -->
        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>
       
        <!-- JAVASCRIPT -->
        <x-reusables.toaster />
        <script src="{{asset('assets/libs/jquery/jquery.min.js')}}"></script>
        <script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('assets/libs/metismenu/metisMenu.min.js')}}"></script>
        <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
        <script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>
        <script src="{{asset('assets/libs/feather-icons/feather.min.js')}}"></script>
        <!-- pace js -->
        <script src="{{asset('assets/libs/pace-js/pace.min.js')}}"></script>
        <!-- Bootstrap Toasts Js -->
        <script src="{{asset('assets/js/pages/bootstrap-toasts.init.js')}}"></script>
        @yield('js')
        <script src="{{asset('assets/js/app.js')}}"></script>
        <script src="{{ asset('assets/js/custom.js?ver='.time()) }}"></script>
    </body>
</html>
