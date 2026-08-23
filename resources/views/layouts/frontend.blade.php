@php
$outPages = isset($outPages) ? true : false;
$is_session = session()->get('checkout_event');
@endphp
<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, shrink-to-fit=9">
    <meta name="title" content="{{ $seo_details->title ?? '' }}">
    <meta name="description" content="{{ $seo_details->description ?? '' }}">
    <meta name="keywords" content="{{ $seo_details->keywords ?? '' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        {{ @$seo_details->title }} | {{ env('APP_NAME') }}
    </title>
    <!-- Favicon Icon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
    <!-- Stylesheets -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:ital,wght@0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    <!-- <link href="{{ asset('assets_front/vendor/unicons-2.0.1/css/unicons.css') }}" rel='stylesheet'> -->
    <link href="{{ asset('assets_front/css/style.css?v='.time()) }}" rel="stylesheet">
    <link href="{{ asset('assets_front/css/event-detail.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_front/css/dashboard-style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_front/css/vertical-responsive-menu.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_front/css/analytics.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_front/css/responsive.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_front/css/night-mode.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_front/css/jquery-steps.css') }}" rel="stylesheet">
    <!-- Vendor Stylesheets -->
    <link href="{{ asset('assets_front/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_front/vendor/OwlCarousel/assets/owl.carousel.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_front/vendor/OwlCarousel/assets/owl.theme.default.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_front/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_front/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <!-- <link href="{{ asset('assets_front/vendor/chartist/dist/chartist.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets_front/vendor/chartist-plugin-tooltip/dist/chartist-plugin-tooltip.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('assets_front/css/datepicker.min.css')}}" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('js/plugins/sweetalert2/sweetalert2.min.css') }}">
    <style>
        .ck-content ul {
        list-style-type: disc;
        margin-left: 20px;
        }
        .ck-content ol {
        list-style-type: decimal;
        margin-left: 20px;
        }
        .ck-content em,
        .ck-content i,
        .ck-editor__editable em,
        .ck-editor__editable i {
        font-style: italic;
        font-family: inherit;
        font-weight: inherit;
        }
    </style>
    @stack('style')
    <script>
        const base = "{!! url(config('app.admin_prefix')) !!}";
        const frontbase = "{!! url('/') !!}";
    </script>
</head>

<body class="d-flex flex-column h-100">
    <!-- Add Organisation Model Start-->
    <div class="modal fade" id="addorganisationModal" tabindex="-1" aria-labelledby="addorganisationLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addorganisationLabel">Organization details</h5>
                    <button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i
                            class="uil uil-multiply"></i></button>
                </div>
                <div class="modal-body">
                    <div class="model-content main-form">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group text-center mt-4">
                                    <label class="form-label">Avatar*</label>
                                    <span class="org_design_button btn-file">
                                        <span><i class="fa-solid fa-camera"></i></span>
                                        <input type="file" id="org_avatar" accept="image/*"
                                            name="Organization_avatar">
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group mt-4">
                                    <label class="form-label">Name*</label>
                                    <input class="form-control h_40" type="text" placeholder="" value="">
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group mt-4">
                                    <label class="form-label">Profile Link*</label>
                                    <input class="form-control h_40" type="text" placeholder=""
                                        value="" disabled>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group mt-4">
                                    <label class="form-label">About*</label>
                                    <textarea class="form-textarea" placeholder="">About</textarea>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group mt-4">
                                    <label class="form-label">Email*</label>
                                    <input class="form-control h_40" type="text" placeholder="" value="">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group mt-4">
                                    <label class="form-label">Phone*</label>
                                    <input class="form-control h_40" type="text" placeholder="" value="">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group mt-4">
                                    <label class="form-label">Website*</label>
                                    <input class="form-control h_40" type="text" placeholder="" value="">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group mt-4">
                                    <label class="form-label">Facebook*</label>
                                    <input class="form-control h_40" type="text" placeholder="" value="">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group mt-4">
                                    <label class="form-label">Instagram*</label>
                                    <input class="form-control h_40" type="text" placeholder="" value="">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group mt-4">
                                    <label class="form-label">Twitter*</label>
                                    <input class="form-control h_40" type="text" placeholder="" value="">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group mt-4">
                                    <label class="form-label">LinkedIn*</label>
                                    <input class="form-control h_40" type="text" placeholder="" value="">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group mt-4">
                                    <label class="form-label">Youtube*</label>
                                    <input class="form-control h_40" type="text" placeholder="" value="">
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <h4 class="address-title">Address</h4>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group mt-4">
                                    <label class="form-label">Address 1*</label>
                                    <input class="form-control h_40" type="text" placeholder="" value="">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group mt-4">
                                    <label class="form-label">Address 2*</label>
                                    <input class="form-control h_40" type="text" placeholder="" value="">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group main-form mt-4">
                                    <label class="form-label">Country*</label>
                                    <select class="selectpicker" data-size="5" title="Nothing selected"
                                        data-live-search="true">
                                        <option value="Algeria">Algeria</option>
                                        <option value="Argentina">Argentina</option>
                                        <option value="Australia">Australia</option>
                                        <option value="Austria">Austria (Österreich)</option>
                                        <option value="Belgium">Belgium (België)</option>
                                        <option value="Bolivia">Bolivia</option>
                                        <option value="Brazil">Brazil</option>
                                        <option value="Canada">Canada</option>
                                        <option value="Chile">Chile</option>
                                        <option value="Colombia">Colombia</option>
                                        <option value="Costa Rica">Costa Rica</option>
                                        <option value="Cyprus">Cyprus</option>
                                        <option value="Czech Republic">Czech Republic</option>
                                        <option value="Denmark">Denmark</option>
                                        <option value="Dominican Republic">Dominican Republic</option>
                                        <option value="Estonia">Estonia</option>
                                        <option value="Finland">Finland</option>
                                        <option value="France">France</option>
                                        <option value="Germany">Germany</option>
                                        <option value="Greece">Greece</option>
                                        <option value="Hong Kong">Hong Kong</option>
                                        <option value="Iceland">Iceland</option>
                                        <option value="India">India</option>
                                        <option value="Indonesia">Indonesia</option>
                                        <option value="Ireland">Ireland</option>
                                        <option value="Israel">Israel</option>
                                        <option value="Italy">Italy</option>
                                        <option value="Japan">Japan</option>
                                        <option value="Latvia">Latvia</option>
                                        <option value="Lithuania">Lithuania</option>
                                        <option value="Luxembourg">Luxembourg</option>
                                        <option value="Malaysia">Malaysia</option>
                                        <option value="Mexico">Mexico</option>
                                        <option value="Nepal">Nepal</option>
                                        <option value="Netherlands">Netherlands</option>
                                        <option value="New Zealand">New Zealand</option>
                                        <option value="Norway">Norway</option>
                                        <option value="Paraguay">Paraguay</option>
                                        <option value="Peru">Peru</option>
                                        <option value="Philippines">Philippines</option>
                                        <option value="Poland">Poland</option>
                                        <option value="Portugal">Portugal</option>
                                        <option value="Singapore">Singapore</option>
                                        <option value="Slovakia">Slovakia</option>
                                        <option value="Slovenia">Slovenia</option>
                                        <option value="South Africa">South Africa</option>
                                        <option value="South Korea">South Korea</option>
                                        <option value="Spain">Spain</option>
                                        <option value="Sweden">Sweden</option>
                                        <option value="Switzerland">Switzerland</option>
                                        <option value="Tanzania">Tanzania</option>
                                        <option value="Thailand">Thailand</option>
                                        <option value="Turkey">Turkey</option>
                                        <option value="United Kingdom">United Kingdom</option>
                                        <option value="United States">United States</option>
                                        <option value="Vietnam">Vietnam</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group mt-4">
                                    <label class="form-label">State*</label>
                                    <input class="form-control h_40" type="text" placeholder="" value="">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group mt-4">
                                    <label class="form-label">City/Suburb*</label>
                                    <input class="form-control h_40" type="text" placeholder="" value="">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group mt-4">
                                    <label class="form-label">Zip/Post Code*</label>
                                    <input class="form-control h_40" type="text" placeholder="" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="primary-btn w-fit" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="primary-btn w-fit">Add</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Organisation Model End-->
    <!-- Header Start-->
    <header class="header">
        <div class="header-inner">
            <nav class="navbar navbar-expand-lg bg-barren barren-head fixed-top pt-0 pb-0 ps-lg-0 pe-2"
                data-aos="fade-down" data-aos-easing="linear" data-aos-duration="600">
                <div class="container-fluid ps-0">
                    @if (!$outPages)
                    <button type="button" id="toggleMenu" class="toggle_menu">
						<i class="fa-solid fa-bars-staggered"></i>
					</button>
                    @endif 
                    <!-- Brand -->
                    <a class="navbar-brand order-lg-0 ml-lg-0 ml-2 me-auto" href="{{ config('app.frontsite') }}">
                        <div class="res-main-logo">
                            <img src="{{ url('/') }}/assets_front/images/logo.webp" alt="">
                        </div>
                        <div class="main-logo" id="logo">
                            <img src="{{ url('/') }}/assets_front/images/logo.webp" alt="">
                            <img class="logo-inverse" src="{{ url('/') }}/assets_front/images/logo.webp" alt="">
                        </div>
                    </a>

                    @if (auth()->guard('front')->check())
                    <a href="{{ route('front-dashboard') }}" class="btn btn-success text-white mobile-dashboard-btn me-2" style="display: none;">
                        Dasbhoard
                    </a>
                    @else
                    <a href="{{ route('front-login') }}" class="btn btn-primary text-white mobile-login-btn me-2" style="display: none;">
                        Login
                    </a>
                    @endif 
                    <!-- Mobile Toggle -->
                    <button class="navbar-toggler me-2" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                        aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                        <i class="fa-solid fa-bars"></i>
                    </button>

                    <!-- Right Side (cart, donate, login/profile) -->
                    <div class="right-header d-none d-lg-flex order-2">
                        <ul class="d-flex align-items-center mb-0">
                            @if($is_session)
                            <li class="ms-0 me-3">
                                <a href="{{ route('front-checkout') }}" class="sign-up-btn p-3 text-white">
                                    <i class="fa fa-shopping-cart"></i>
                                </a>
                            </li>
                            @endif
                            <li class="ms-0 me-3">
                                <a href="{{ config('app.frontsite') }}donate" class="sign-up-btn text-white">
                                    Donate
                                </a>
                            </li>
                            @if (auth()->guard('front')->check())
                            @php
                            $userProfilePhoto = auth()->guard('front')->user()->formattedImage('profile_picture');
                            $userProfileAlt = auth()->guard('front')->user()->name;
                            @endphp
                            <li class="dropdown account-dropdown ms-0">
                                <a href="#" class="account-link" role="button" id="accountClick"
                                    data-bs-auto-close="outside" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="{{ $userProfilePhoto }}" alt="{{ $userProfileAlt }}">
                                    <i class="fas fa-caret-down arrow-icon"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-account dropdown-menu-end"
                                    aria-labelledby="accountClick">
                                    <li>
                                        <div class="dropdown-account-header">
                                            <div class="account-holder-avatar">
                                                <img src="{{ $userProfilePhoto }}" alt="{{ $userProfileAlt }}">
                                            </div>
                                            <h5>{{ auth()->guard('front')->user()->name }}</h5>
                                            <p>{{ auth()->guard('front')->user()->email_address }}</p>
                                        </div>
                                    </li>
                                    <li class="profile-link">
                                        <a href="{{ route('front-dashboard') }}" class="link-item">Dashboard</a>
                                        <a href="{{ route('front-cleanup-attending') }}" class="link-item">Cleanups I'm Attending</a>
                                        <a href="{{ route('front-cleanup-hosting','organizer') }}" class="link-item">Cleanups I'm Hosting</a>
                                        <a href="{{ route('front-email-alerts') }}" class="link-item">Weekly Email Alerts</a>
                                        <a href="{{ route('front-profile') }}" class="link-item">My Profile</a>
                                        <a href="{{ route('front-logout') }}" class="link-item">Logout</a>
                                    </li>
                                </ul>
                            </li>
                            @else
                            <li class="ms-0">
                                <a href="{{ route('front-login') }}" class="primary-btn">
                                    Login
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>

                    <!-- Offcanvas Menu -->
                    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar"
                        aria-labelledby="offcanvasNavbarLabel">
                        <div class="offcanvas-header">
                            <div class="offcanvas-logo" id="offcanvasNavbarLabel">
                                <img src="{{ url('/') }}/assets_front/images/logo.webp" alt="">
                            </div>
                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <ul class="navbar-nav justify-content-center flex-grow-1">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        Cleanups <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">
                                            <title>Chevron right</title>
                                            <path d="M9 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('view-events') }}">Find A Cleanup</a></li>
                                        <li><a class="dropdown-item" href="{{ route('front-post-a-event') }}">Organize A Cleanup</a></li>
                                        <li><a class="dropdown-item" href="{{ route('front-create-alert') }}">Get Notified</a></li>
                                        <li><a class="dropdown-item" href="{{ config('app.frontsite') }}solo-cleanup/">Do A Solo Cleanup</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        Programs <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">
                                            <title>Chevron right</title>
                                            <path d="M9 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ config('app.frontsite') }}corporate-days-of-service/">Corporate Days Of Service</a></li>
                                        <li><a class="dropdown-item" href="{{ config('app.frontsite') }}trashathon/">Trashathon Fundraiser</a></li>
                                        <li><a class="dropdown-item" href="{{ config('app.frontsite') }}rent-recycling-bins/">Rent Recycling Bins</a></li>
                                        <!-- <li><a class="dropdown-item" href="{{ route('view-international-events') }}">International Coastal Cleanup Day</a></li> -->
                                        <li><a class="dropdown-item" href="{{ config('app.frontsite') }}international-coastal-cleanup">International Coastal Cleanup Day</a></li>
                                        <li><a class="dropdown-item" href="{{ config('app.frontsite') }}leave-no-trace/">Leave No Trace Miami</a></li>
                                        <li><a class="dropdown-item" href="{{ config('app.frontsite') }}cigarette-litter-reduction/">Cigarette Litter Solutions</a></li>
                                        <li><a class="dropdown-item" href="{{ config('app.frontsite') }}mitzvah-projects/">Mitzvah Projects</a></li>
                                        <li><a class="dropdown-item" href="{{ config('app.frontsite') }}memorial-cleanups/">Memorial Cleanups</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        Resources <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">
                                            <title>Chevron right</title>
                                            <path d="M9 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ config('app.frontsite') }}learn-about-marine-debris/">Learn About Marine Debris</a></li>
                                        <li><a class="dropdown-item" href="{{ config('app.frontsite') }}faqs/">Frequently Asked Questions</a></li>
                                        <li><a class="dropdown-item" href="{{ config('app.frontsite') }}cleanup-organizer-resources/">Resources for Cleanup Organizers</a></li>
                                        <li><a class="dropdown-item" href="{{ config('app.frontsite') }}cleanup-supplies-grant/">Cleanup Supplies Grant</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        About Us <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">
                                            <title>Chevron right</title>
                                            <path d="M9 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ config('app.frontsite') }}about">Our Story</a></li>
                                        <li><a class="dropdown-item" href="{{ config('app.frontsite') }}news/">News</a></li>
                                        <li><a class="dropdown-item" href="{{ config('app.frontsite') }}sponsor-icc/">Our Sponsors</a></li>
                                        <li><a class="dropdown-item" href="{{ config('app.frontsite') }}contact-us/">Contact Us</a></li>
                                    </ul>
                                </li>
                                <!-- <li class="nav-item"><a class="nav-link" href="{{ route('view-events') }}">Find A Cleanup</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('front-post-a-event') }}">Organize A Cleanup</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Get Notified</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ config('app.frontsite') }}solo-cleanup/">Solo Cleanup</a></li>

                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        More <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">
                                            <title>Chevron right</title>
                                            <path d="M9 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-submenu">
                                            <a class="dropdown-item dropdown-toggle" href="#">Programs</a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ config('app.frontsite') }}corporate-days-of-service/">Corporate Days Of Service</a></li>
                                                <li><a class="dropdown-item" href="{{ config('app.frontsite') }}trashathon/">Trashathon Fundraiser</a></li>
                                                <li><a class="dropdown-item" href="{{ config('app.frontsite') }}rent-recycling-bins/">Rent Recycling Bins</a></li>
                                                <li><a class="dropdown-item" href="{{ config('app.frontsite') }}miami-coastal-cleanup-day/">International Coastal Cleanup Day</a></li>
                                                <li><a class="dropdown-item" href="{{ config('app.frontsite') }}leave-no-trace/">Leave No Trace Miami</a></li>
                                                <li><a class="dropdown-item" href="{{ config('app.frontsite') }}cigarette-litter-reduction/">Cigarette Litter Solutions</a></li>
                                                <li><a class="dropdown-item" href="{{ config('app.frontsite') }}mitzvah-projects/">Mitzvah Projects</a></li>
                                                <li><a class="dropdown-item" href="{{ config('app.frontsite') }}memorial-cleanups/">Memorial Cleanups</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown-submenu">
                                            <a class="dropdown-item dropdown-toggle" href="#">Resources</a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ config('app.frontsite') }}learn-about-marine-debris/">Marine Debris</a></li>
                                                <li><a class="dropdown-item" href="{{ config('app.frontsite') }}faqs/">FAQs</a></li>
                                                <li><a class="dropdown-item" href="{{ config('app.frontsite') }}cleanup-organizer-resources/">Organizer Resources</a></li>
                                                <li><a class="dropdown-item" href="{{ config('app.frontsite') }}cleanup-supplies-grant/">Supply Grants</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li> -->
                            </ul>

                            <!-- Social links -->
                            <div class="offcanvas-footer mt-4">
                                <div class="offcanvas-social">
                                    <h5>Follow Us</h5>
                                    <ul class="social-links d-flex">
                                        <li><a href="#" class="social-link"><i class="fab fa-facebook-square"></i></a></li>
                                        <li><a href="#" class="social-link"><i class="fab fa-instagram"></i></a></li>
                                        <li><a href="#" class="social-link"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a></li>
                                        <li><a href="#" class="social-link"><i class="fab fa-youtube"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </nav>

            <div class="overlay"></div>
        </div>
    </header>
    <!-- Header End-->
    <!-- Left Sidebar Start -->
    @if (!$outPages)
    @php
    $adminUser = auth()->guard('front')->user();
    @endphp
    <nav class="vertical_nav">
        <div class="left_section menu_left" id="js-menu">
            <div class="left_section">
                <ul>
                    <li class="menu--item">
                        <a href="{{ route('front-dashboard') }}"
                            class="menu--link {{ url()->current() == route('front-dashboard') ? 'active' : '' }}"
                            title="Dashboard" data-bs-toggle="tooltip" data-bs-placement="right">
                            <i class="fa-solid fa-gauge menu--icon"></i>
                            <span class="menu--label">Dashboard</span>
                        </a>
                    </li>
                    <li class="menu--item menu--item__has_sub_menu {{ url()->current() == route('front-cleanup-attending') || request()->routeIs('front-cleanup-hosting') || url()->current() == route('front-post-a-event') || url()->current() == route('front-registration-list') ? 'menu--subitens__opened' : '' }}">
                        <a class="menu--link">
                            <i class="fa-solid fa-calendar-days menu--icon"></i>
                            <span class="menu--label">Events</span>
                        </a>
                        <ul class="sub_menu mt-0">
                            <li>
                                <a href="{{ route('front-post-a-event') }}"
                                    class="sub_menu--link {{ url()->current() == route('front-post-a-event') ? 'active' : '' }}"
                                    title="Post a New Cleanup" data-bs-toggle="tooltip" data-bs-placement="right">
                                    Post a New Cleanup
                                </a>
                            </li>
                            @if($adminUser->is_organiser == '1')
                            <li>
                                <a href="{{ route('front-cleanup-hosting', ['type' => 'organizer']) }}"
                                    class="sub_menu--link {{ request()->routeIs('front-cleanup-hosting',['type' => request()->segment(2) ?? 'organizer']) ? 'active' : '' }}"
                                    title="Cleanups I’m Hosting" data-bs-toggle="tooltip" data-bs-placement="right">
                                    Cleanups I’m Hosting
                                </a>
                            </li>
                            @endif
                            <li>
                                <a href="{{ route('front-cleanup-attending') }}"
                                    class="sub_menu--link {{ url()->current() == route('front-cleanup-attending') ? 'active' : '' }}"
                                    title="Attending" data-bs-toggle="tooltip" data-bs-placement="right">
                                    Cleanups I'm Attending
                                </a>
                            </li>
                            @if($adminUser->is_organiser == '1')
                            <li>
                                <a href="{{ route('front-registration-list') }}"
                                    class="sub_menu--link {{ url()->current() == route('front-registration-list') ? 'active' : '' }}"
                                    title="Registration Lists" data-bs-toggle="tooltip" data-bs-placement="right">
                                    Registration Lists
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    <li class="menu--item menu--item__has_sub_menu {{ url()->current() == route('contact-lists') || url()->current() == route('front-email-alerts') ? 'menu--subitens__opened' : '' }}">
                        <a class="menu--link">
                            <i class="fa-solid fa-envelope menu--icon"></i>
                            <span class="menu--label">Communication</span>
                        </a>
                        <ul class="sub_menu mt-0">
                            @if($adminUser->is_organiser == '1')
                            <li>
                                <a href="{{ route('contact-lists') }}"
                                    class="sub_menu--link {{ url()->current() == route('contact-lists') ? 'active' : '' }}"
                                    title="Inbox" data-bs-toggle="tooltip" data-bs-placement="right">
                                    Inbox
                                </a>
                            </li>
                            @endif
                            <li>
                                <a href="{{ route('front-email-alerts') }}"
                                    class="sub_menu--link {{ url()->current() == route('front-email-alerts') ? 'active' : '' }}"
                                    title="Weekly Email Alerts" data-bs-toggle="tooltip" data-bs-placement="right">
                                    Weekly Email Alerts
                                </a>
                            </li>
                        </ul>
                    </li>
                    @php
                        $isCertificateDetailMyRequest = request()->routeIs('front-certificate-request-detail')
                            && isset($ticket)
                            && $adminUser->id == $ticket->volunteer_id;

                        $isCertificateDetailVolunteerRequest = request()->routeIs('front-certificate-request-detail')
                            && isset($ticket)
                            && $adminUser->id != $ticket->volunteer_id;
                    @endphp
                    <li class="menu--item menu--item__has_sub_menu {{ request()->routeIs('front-certificate-my-requests') || request()->routeIs('front-certificate-volunteer-requests') || $isCertificateDetailMyRequest || $isCertificateDetailVolunteerRequest ? 'menu--subitens__opened' : '' }}">
                    <!-- <li class="menu--item menu--item__has_sub_menu {{ url()->current() == route('front-certificate-my-requests') ? 'menu--subitens__opened' : '' }} {{ url()->current() == route('front-certificate-volunteer-requests') ? 'menu--subitens__opened' : '' }}"> -->
                        <a class="menu--link">
                            <i class="menu--icon fa fa-vcard"></i>
                            <span class="menu--label">Certificates </span>
                        </a>
                        @if($adminUser->attendingEventsWithoutActive()->count()>0 || $adminUser->is_organiser == '1')
                        <ul class="sub_menu mt-0">
                            @if($adminUser->attendingEventsWithoutActive()->count()>0)
                            <li>
                                <a href="{{ route('front-certificate-my-requests') }}" class="sub_menu--link {{ request()->routeIs('front-certificate-my-requests') || $isCertificateDetailMyRequest ? 'active' : '' }}" title="Track your requests and download your certificates once approved" data-bs-toggle="tooltip" data-bs-placement="right">My Requests</a>

                                <!-- <a href="{{ route('front-certificate-my-requests') }}" class="sub_menu--link {{ url()->current() == route('front-certificate-my-requests') ? 'active' : '' }}" title="Track your requests and download your certificates once approved" data-bs-toggle="tooltip" data-bs-placement="right">My Requests</a> -->
                            </li>
                            @endif
                            @if($adminUser->is_organiser == '1')
                            <li>

                                <a href="{{ route('front-certificate-volunteer-requests') }}" class="sub_menu--link {{ request()->routeIs('front-certificate-volunteer-requests') || $isCertificateDetailVolunteerRequest ? 'active' : '' }}" title="Check volunteer requests and approve or reject them" data-bs-toggle="tooltip" data-bs-placement="right">Volunteer Requests</a>

                                <!-- <a href="{{ route('front-certificate-volunteer-requests') }}" class="sub_menu--link {{ url()->current() == route('front-certificate-volunteer-requests') ? 'active' : '' }}" title="Check volunteer requests and approve or reject them" data-bs-toggle="tooltip" data-bs-placement="right">Volunteer Requests</a> -->
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    @if($adminUser->is_organiser == '1')
                    <!-- <li class="menu--item">
                        <a href="{{ route('front-certificate-volunteer-requests') }}"
                            class="menu--link {{ url()->current() == route('front-certificate-volunteer-requests') ? 'active' : '' }}"
                            title="Certificate Management" data-bs-toggle="tooltip" data-bs-placement="right">
                            <i class="fa-solid fa-certificate menu--icon"></i>
                            <span class="menu--label">Certificate Management</span>
                        </a>
                    </li> -->


                    <!-- <li class="menu--item">
                        <a href="{{ route('front-sales-report') }}"
                            class="menu--link {{ url()->current() == route('front-sales-report') ? 'active' : '' }}"
                            title="Sales Report" data-bs-toggle="tooltip" data-bs-placement="right">
                            <i class="fa-solid fa-bar-chart menu--icon"></i>
                            <span class="menu--label">Sales Report</span>
                        </a>
                    </li> -->

                    <li class="menu--item menu--item__has_sub_menu {{ request()->routeIs('front-sales-report') || request()->routeIs('front-orders') || request()->routeIs('front-order-details') || request()->routeIs('front-stripe-connect') ? 'menu--subitens__opened' : '' }}">
                        <a class="menu--link">
                            <i class="menu--icon fa fa-dollar"></i>
                            <span class="menu--label">Financials</span>
                        </a>
                        <ul class="sub_menu mt-0">
                            <li>
                                <a href="{{ route('front-stripe-connect') }}" class="sub_menu--link {{ url()->current() == route('front-stripe-connect') ? 'active' : '' }}" title="Payment Setup" data-bs-toggle="tooltip" data-bs-placement="right">Stripe Connect</a>
                            </li>
                            <li>
                                <a href="{{ route('front-sales-report') }}" class="sub_menu--link {{ url()->current() == route('front-sales-report') ? 'active' : '' }}" title="Attendees & Bookings" data-bs-toggle="tooltip" data-bs-placement="right">Paid Tickets & Donations</a>
                            </li>
                            <li>
                                <a href="{{ route('front-orders') }}" class="sub_menu--link {{ request()->routeIs('front-orders') || request()->routeIs('front-order-details') ? 'active' : '' }}" title="Revenue and Donation Summary" data-bs-toggle="tooltip" data-bs-placement="right">Orders</a>

                            </li>
                        </ul>
                    </li>
                    @else 
                    <li class="menu--item">
                        <a href="{{ route('front-orders') }}"
                        class="menu--link {{ request()->routeIs('front-orders') || request()->routeIs('front-order-details') ? 'active' : '' }}"
                        title="Orders" data-bs-toggle="tooltip" data-bs-placement="right">
                            <i class="fa-solid fa-shopping-cart menu--icon"></i>
                            <span class="menu--label">Orders</span>
                        </a>
                    </li>
                    @endif

                    <li class="menu--item menu--item__has_sub_menu {{ url()->current() == route('front-embedded-widget') || url()->current() == route('front-embedded-widget-report') ? 'menu--subitens__opened' : '' }}">
                        <a class="menu--link">
                            <i class="fa-solid fa-globe menu--icon"></i>
                            <span class="menu--label">Website & Widgets</span>
                        </a>
                        <ul class="sub_menu mt-0">
                            <li>
                                <a href="{{ route('front-embedded-widget') }}" class="sub_menu--link {{ url()->current() == route('front-embedded-widget') ? 'active' : '' }}" title="Embedded Widget" data-bs-toggle="tooltip" data-bs-placement="right">Embedded Widget</a>
                            </li>
                            <li>
                                <a href="{{ route('front-embedded-widget-report') }}" class="sub_menu--link {{ url()->current() == route('front-embedded-widget-report') ? 'active' : '' }}" title="Widget Report" data-bs-toggle="tooltip" data-bs-placement="right">Widget Report</a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu--item menu--item__has_sub_menu {{ url()->current() == route('front-profile') ? 'menu--subitens__opened' : '' }}">
                        <a class="menu--link">
                            <i class="fa-solid fa-user menu--icon"></i>
                            <span class="menu--label">Account</span>
                        </a>
                        <ul class="sub_menu mt-0">
                            <li>
                                <a href="{{ route('front-profile') }}" class="sub_menu--link {{ url()->current() == route('front-profile') ? 'active' : '' }}" title="Manage Profile" data-bs-toggle="tooltip" data-bs-placement="right">Manage Profile</a>
                            </li>
                        </ul>
                    </li>
                    <!-- <li class="menu--item">
                        <a href="{{ route('front-certificate-my-requests') }}"
                            class="menu--link {{ url()->current() == route('front-certificate-my-requests') ? 'active' : '' }}"
                            title="Certificate Requests" data-bs-toggle="tooltip" data-bs-placement="right">
                            <i class="fa-solid fa-vcard menu--icon"></i>
                            <span class="menu--label">Certificate Requests</span>
                        </a>
                    </li> -->
                    <li class="menu--item">
                        <a href="{{ route('front-release-notes') }}" class="menu--link {{ url()->current() == route('front-release-notes') ? 'active' : '' }}" title="Release Notes"
                            data-bs-toggle="tooltip" data-bs-placement="right">
                            <i class="fa-solid fa-bullhorn menu--icon"></i>
                            <span class="menu--label">Release Notes
                                @if(!empty($hasNewReleaseNotes))
                                    <span class="badge bg-danger ms-2">New</span>
                                @endif
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endif
    <!-- Left Sidebar End -->
    <!-- Body Start -->
    @yield('content')
    <!-- Body End -->
    {{-- Footer --}}
    @if ($outPages)
    <footer class="footer bg-white mt-auto py-5">
        <div class="container">
            <div class="row row-gap-4 align-items-center justify-content-center mb-4 border-bottom pb-4" style="border-color:#cccccc4d !important;">
                <div class="col-sm-5">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <ul class="list-unstyled mb-0 d-flex gap-2">
                            <li><a href="{{ config('app.frontsite') }}about/" class="text-decoration-none">About Us | </a></li>
                            <li><a href="{{ config('app.frontsite') }}contact-us/" class="text-decoration-none"> Contact Us | </a></li>
                            <li><a href="{{ config('app.frontsite') }}terms/" class="text-decoration-none"> Terms of Use | </a></li>
                            <li><a href="#" class="text-decoration-none"> Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="footer-social-links d-flex justify-content-center gap-4">
                        <a href="https://www.instagram.com/volunteercleanup/" target="_blank">
                            <svg class="e-font-icon-svg e-fab-instagram" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg">
                                <path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"></path>
                            </svg>
                        </a>
                        <a href="https://www.facebook.com/volunteercleanup/" target="_blank">
                            <svg class="e-font-icon-svg e-fab-facebook-f" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg">
                                <path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"></path>
                            </svg>
                        </a>
                        <a href="https://www.youtube.com/@VolunteerCleanupOrg" target="_blank">
                            <svg class="e-font-icon-svg e-fab-youtube" viewBox="0 0 576 512" xmlns="http://www.w3.org/2000/svg">
                                <path d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="col-sm-4">
                    <p class="text-center">Copyright © 2025 VolunteerCleanup.org</p>
                </div>
            </div>
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-12 text-white">
                    <p class="mb-2 text-center">
                        <strong>VolunteerCleanup.org</strong> mobilizes volunteers to clean neighborhoods, shorelines, and waterways,
                        raising awareness of marine debris and reducing single-use plastics.
                    </p>
                    <p class="mb-0 text-center">
                        A project of Miami Dade Coastal Cleanup, Inc. — a 501(c)(3) nonprofit (EIN 42-1766017)
                    </p>
                </div>
            </div>
        </div>
        </div>
    </footer>

    @endif

    <x-reusables.toaster />
    <script src="{{ asset('assets_front/js/vertical-responsive-menu.min.js') }}"></script>
    <script src="{{ asset('assets_front/js/jquery.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/pages/bootstrap-toasts.init.js')}}"></script> --}}
    <script src="{{ asset('assets_front/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets_front/vendor/OwlCarousel/owl.carousel.js') }}"></script>
    <script src="{{ asset('assets_front/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <!-- <script src="{{ asset('assets_front/vendor/chartist/dist/chartist.min.js') }}"></script>
    <script src="{{ asset('assets_front/vendor/chartist-plugin-tooltip/dist/chartist-plugin-tooltip.min.js') }}"></script>
    <script src="{{ asset('assets_front/js/analytics.js') }}"></script> -->
    <script src="{{ asset('assets_front/js/custom.js?v='.time()) }}" defer></script>
    <script src="{{ asset('assets_front/js/night-mode.js') }}"></script>
    <script src="{{ asset('assets_front/js/jquery-steps.min.js') }}"></script>
    <script src="{{ asset('assets_front/js/datepicker.min.js') }}"></script>
    <script src="{{ asset('js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true
        });
    </script>
    @stack('script')
</body>

</html>