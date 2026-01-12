@php
    $is_update_form = $formData ? true : false;
    $action_url = $is_update_form
        ? route('update-volunteer', ['placeholder' => $formData->encrypted_id])
        : route('create-volunteer');
    $role_name = old('role_name', $formData->role_name ?? '');
    $is_active = old('is_active', $formData->is_active ?? '');
    $pageName = 'Manage ' . $module;
    $parentPageName = $module;
    $parentRoute = route('index-volunteers');
    $module = $module ?? 'Volunteer';
    $first_name = old('first_name', $formData->first_name ?? '');
    $last_name = old('last_name', $formData->last_name ?? '');
    $email_address = old('email_address', $formData->email_address ?? '');
    $phone = old('phone', $formData->phone ?? '');
    $address_line_1 = old('address_line_1', $formData->address_line_1 ?? '');
    $address_line_2 = old('address_line_2', $formData->address_line_2 ?? '');
    $city_id = old('city_id', $formData->city_id ?? '');
    $pincode = old('pincode', $formData->pincode ?? '');
    $is_newsletter = old('is_newsletter', $formData->is_newsletter ?? 0);
    $is_organiser = old('is_organiser', $formData->is_organiser ?? 0);
    $status = old('status', $formData->status ?? 'inactive');
    $profile_picture = isset($formData->profile_picture) ? asset($formData->profile_picture->path) : null;
@endphp
@extends('layouts.backend_new', ['pageName' => 'Manage volunteer & organizer', 'parentPageName' => $parentPageName, 'parentRoute' => $parentRoute,'breadcrumb' => [
    ['name' => 'Volunteers', 'route' => route('index-volunteers')],
    ['name' => 'Manage Volunteer & Organizer', 'route' => ''],
]])
@section('css')
    <x-reusables.advanceformcss />
@endsection
@section('js')
    <x-reusables.advanceformjs />
<script src="{{asset('js/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/js/validation.js')}}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <form action="{{ $action_url }}" method="post" class="manageVolunteerForm" enctype="multipart/form-data">
                @csrf
                @if ($is_update_form)
                    @method('PUT')
                @endif
                <div class="card">
                    <div class="card-body">
                        <x-reusables.frontend.alert />
                        <div class="row">
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="first_name">First Name*</label>
                                    <input type="text" class="form-control textonlymask" id="first_name" name="first_name" value="{{ $first_name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="last_name">Last Name*</label>
                                    <input type="text" class="form-control textonlymask" id="last_name" name="last_name" value="{{ $last_name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="email_address">Email Address*</label>
                                    <input type="email" class="form-control" id="email_address" name="email_address" value="{{ $email_address }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control usphonemask" id="phone" name="phone" value="{{ $phone }}">
                                </div>
                            </div>
                            @if (!$is_update_form)
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="password">Password*</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="address_line_1">Address Line 1</label>
                                    <input type="text" class="form-control onlyTextNumberSplaceMask" id="address_line_1" name="address_line_1" value="{{ $address_line_1 }}">
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="address_line_2">Address Line 2</label>
                                    <input type="text" class="form-control onlyTextNumberSplaceMask" id="address_line_2" name="address_line_2" value="{{ $address_line_2 }}">
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="pincode">Pincode</label>
                                    <!-- <input type="text" class="form-control pincodemask" id="pincode" name="pincode" value="{{ $pincode }}"> -->
                                    <select datatype="pincode" class="form-select select2ajax" id="pincode" name="city_id">
                                        @if($formData && isset($formData->city_state_zip) && isset($formData->city_state_zip->zipcode))
                                            <option value="{{ $formData->city_state_zip->id }}" selected>
                                                {{ $formData->city_state_zip->zipcode.' ('. $formData->city_state_zip->city.', '.$formData->city_state_zip->state.')' }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="city_id">City</label>
                                    <select class="form-select select2" id="city_id" name="city_id">
                                        <option value="" disabled selected>Select City</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->city_id }}" @selected($city_id == $city->city_id)>
                                                {{ $city->city_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> -->
                            <div class="col-sm-12 col-md-6 mt-2">
                                <label for="">Profile Picture</label><small> (Recommended size: 200 × 200 px)</small>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                                @if ($profile_picture)
                                    <img src="{{ $profile_picture }}" class="img-fluid mt-2" id="profile_image_preview" alt="Profile Picture" style="max-width: 50px;">
                                @endif
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="is_organiser">Role*</label>
                                    <select name="is_organiser" @disabled($is_organiser == 1 && isset($formData->encrypted_id)) class="form-select"
                                         id="">
                                        <option value="0" @selected(!$is_organiser == 1)>Volunteer</option>
                                        <option value="1" @selected($is_organiser == 1)>Organizer</option>
                                    </select>   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card organisation-details" style="display: {{ $is_organiser == 1 ? 'block' : 'none' }}">
                    <div class="card-body">
                        <h3 class="card-title fw-bold admin-card-header">Organization Details</h3>
                        @php
                            $organisation_logo =
                                isset($formData) && $formData->organisation_logo
                                    ? asset($formData->organisation_logo->path)
                                    : null;
                            $organisation_name = old('organisation_name', $formData->organisation_name ?? '');
                            $organisation_landing_slug = old(
                                'organisation_landing_slug',
                                $formData->organisation_landing_slug ?? '',
                            );
                            $organisation_short_description = old(
                                'organisation_short_description',
                                $formData->organisation_short_description ?? '',
                            );
                            $organisation_website = old('organisation_website', $formData->organisation_website ?? '');
                            $organisation_instagram = old(
                                'organisation_instagram',
                                $formData->organisation_instagram ?? '',
                            );
                            $organisation_facebook = old(
                                'organisation_facebook',
                                $formData->organisation_facebook ?? '',
                            );
                            $organisation_linkedin = old(
                                'organisation_linkedin',
                                $formData->organisation_linkedin ?? '',
                            );
                            $organisation_twitter = old('organisation_twitter', $formData->organisation_twitter ?? '');
                            $organisation_updated_at = old(
                                'organisation_updated_at',
                                $formData->organisation_updated_at ?? '',
                            );
                        @endphp
                        <div class="row">
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="organisation_name">Organization Name</label>
                                    <input type="text" class="form-control textonlymask" id="organisation_name"
                                        name="organisation_name" value="{{ $organisation_name }}">
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="organisation_logo">Organization Logo</label> <small> (Recommended size: 200 × 200 px)</small>
                                    <input type="file" class="form-control" id="organisation_logo"
                                        name="organisation_logo" accept="image/*">
                                    @if ($organisation_logo)
                                        <img src="{{ $organisation_logo }}" class="img-fluid mt-2"
                                            alt="Organization Logo" style="max-width: 50px;">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <label for="organisation_short_description">Organization Short Description</label>
                                    <textarea class="form-control" id="organisation_short_description" name="organisation_short_description">{{ $organisation_short_description }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="organisation_website">Organization Website</label>
                                    <input type="text" class="form-control urlmask" id="organisation_website"
                                        name="organisation_website" value="{{ $organisation_website }}">
                                </div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="organisation_landing_slug">Organization Landing Slug</label>
                                    <input type="text" class="form-control" id="organisation_landing_slug"
                                        name="organisation_landing_slug" value="{{ $organisation_landing_slug }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3 mt-2">
                                <div class="form-group">
                                    <label for="organisation_instagram">Organization Instagram</label>
                                    <input type="text" class="form-control urlmask" id="organisation_instagram"
                                        name="organisation_instagram" value="{{ $organisation_instagram }}">
                                </div>
                            </div>
                            <div class="col-md-3 mt-2">
                                <div class="form-group">
                                    <label for="organisation_facebook">Organization Facebook</label>
                                    <input type="text" class="form-control urlmask" id="organisation_facebook"
                                        name="organisation_facebook" value="{{ $organisation_facebook }}">
                                </div>
                            </div>
                            <div class="col-md-3 mt-2">
                                <div class="form-group">
                                    <label for="organisation_linkedin">Organization LinkedIn</label>
                                    <input type="text" class="form-control urlmask" id="organisation_linkedin"
                                        name="organisation_linkedin" value="{{ $organisation_linkedin }}">
                                </div>
                            </div>
                            <div class="col-md-3 mt-2">
                                <div class="form-group">
                                    <label for="organisation_twitter">Organization Twitter</label>
                                    <input type="text" class="form-control urlmask" id="organisation_twitter"
                                        name="organisation_twitter" value="{{ $organisation_twitter }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <x-reusables.form-footer route="index-volunteers"
                        action="{{ $is_update_form ? 'update' : 'create' }}" module="{{ $module }}" />
                </div>
            </form>
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection
