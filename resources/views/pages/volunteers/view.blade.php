@php
    $pageName = 'Volunteers & Organizers';
    $createUrl = route('create-volunteer');
    $tableHead = ['Name', 'Email', 'Zip (City, State)', 'Phone', 'Role' ,'Created Date', 'Status', 'Action'];
@endphp
@extends('layouts.backend_new', ['pageName' => $pageName, 'parentPageName' => $pageName])
@section('css')
    <x-reusables.datatablecss />
    <x-reusables.advanceformcss/>
@endsection
@section('js')
    <x-reusables.datatablejs />
    <x-reusables.advanceformjs />
    <script src="{{asset('js/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('assets/js/validation.js')}}"></script>
@endsection
@section('buttons')
    <x-reusables.fab-button :title="'Volunteer & Organizer'" :module="$module" :url="$createUrl" />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <x-reusables.frontend.alert />
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET">
                        <div class="d-flex justify-content-start gap-3 align-items-end">
                            <div class="form-group">
                                <label for="event" class="form-label">Search & Select Event</label>
                                <select class="form-control select2ajax" datatype="event" id="event_select" name="event_id">
                                    @if(request('event_id') && $selected_event)
                                        <option value="{{ request('event_id') }}" selected>
                                            {{ $selected_event->event_name }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="name" class="form-label">Name/Email</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ request('name') }}" placeholder="Name/Email">
                            </div>
                            <div class="form-group">
                                <label for="organiser" class="form-label">Role</label>
                                <select class="form-select " id="organiser" name="role">
                                    <option value="" disabled selected>All</option>
                                    <option value="volunteer" @selected(@request()->role == "volunteer")>Volunteer</option>
                                    <option value="organiser" @selected(@request()->role == "organiser")>Organizer</option>
                                    <option value="cohost"    @selected(@request()->role == "cohost")>Cohost</option>
                                </select> 
                            </div>
                            <div class="form-group">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                            </div>
                            <div class="form-group">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                            </div>
                            <div class="form-group">
                                <label for="event" class="form-label">Sort By</label>
                                <select class="form-control select2" id="sort_by" name="sort_by">
                                    <option value="1" {{ request('sort_by',1) == 1 ? 'selected' : '' }}>Date Desc</option>
                                    <option value="2" {{ request('sort_by') == 2 ? 'selected' : '' }}>Date Asc</option>
                                </select>
                            </div>
                            {{-- <div class="d-flex justify-content-center mt-4 gap-3"> --}}
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="{{ route('index-volunteers') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <!-- end row -->
            <div class="table-responsive">
                <table id="custom_datatable" sort="1" class="table table-bordered nowrap w-100 volunteer-records"
                    dt-responsive>
                    <x-reusables.table-header :tableHead="$tableHead" />
                    <tbody>
                        @forelse ($listingData as $data)
                            @php
                                $jsonData = $data->organisation ?: (object) [];
                                $jsonData->volunteer_id = $data->encrypted_id;
                                $jsonData->uploaded_logo =
                                    $jsonData && isset($jsonData->organisation_logo)
                                        ? asset(@$jsonData->organisation_logo->path)
                                        : null;
                                /*lets append report data as well*/
                                $data->totals = collect([
                                    'total_organised_events' => $data->events->count() ? :'N/a',
                                    'total_cohosting_events' => $data->cohostingEvents->count() ? :'N/a',
                                    'total_volunteered_events' => $data->attendingEvents->count() ? :'N/a',
                                ]);
                                $reportJsonData = $data->totals;
                            @endphp
                            <tr>
                                <td>
                                    {{ $data->name }}
                                    <div class="d-none tr-detail">
                                        @json($jsonData)
                                    </div>
                                    <div class="d-none report-details">
                                        @json($reportJsonData)
                                    </div>
                                </td>
                                <td>{{ $data->email_address }}</td>
                                <td>
                                @if(isset($data->city_state_zip) && isset($data->city_state_zip->id))
                                {{ @$data->city_state_zip->zipcode.' ('.@$data->city_state_zip->city.', '.@$data->city_state_zip->state.')' }}
                                @endif
                                </td>
                                <td>{{ $data->phone }}</td>
                                <td>
                                    @if($data->is_organiser == 1)
                                        <span class="text-primary">Organizer</span>
                                    @else
                                        <span class="text-success">Volunteer</span>
                                    @endif
                                </td>
                                <td>{{ $data->created_at }}</td>
                                <td>
                                    <x-reusables.list-switch :onlabel="'Active'" :offlabel="'Inactive'" :data="$data"
                                        url="{{ route('toggle-volunteer-status', ['placeholder' => $data->encrypted_id]) }}" />
                                </td>
                                <td>
                                    <x-reusables.action-buttons :id="$data->encrypted_id" :module="$module" :name="$data->name" :data="$data" />
                                </td>
                            </tr>
                        @empty
                            <x-reusables.empty-data :colspan="count($tableHead)" />
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-2" data-aos="fade-up" data-aos-duration="800">
                    {{ $listingData->links() }}
                </div>
            </div>
        </div> <!-- end col -->
    </div>
    <!-- end row -->
    {{-- Password reset modal --}}
    <div id="volunteer-organisation-modal" class="modal fade modal-lg detailed-modal-popup" tabindex="-1"
        aria-labelledby="volunteer-organisation-modalLabel" aria-hidden="true" data-bs-scroll="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="volunteer-organisation-modalLabel">Organization Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('create-volunteer-organisation') }}" enctype="multipart/form-data"
                        method="POST">
                        @csrf
                        <input type="hidden" id="volunteer_id" name="volunteer_id">
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="organisation_name" class="form-label">Organization Name</label>
                                <input class="form-control" type="text" name="organisation_name" id="organisation_name">
                            </div>
                            <div class="col-sm-12 mt-4">
                                <label for="organisation_logo" class="form-label">Organization Logo <span
                                        class="text-danger">Max - 2MB</span></label>
                                <input type="file" name="organisation_logo" id="organisation_logo_input_box"
                                    class="form-control">
                                <img src="" width="30" id = "uploaded_logo" class="d-none" height="30"
                                    alt="">
                            </div>
                            <div class="col-sm-12 mt-4">
                                <label for="short_description" class="form-label">Short Description</label>
                                <textarea class="form-control" name="short_description" id="short_description"></textarea>
                            </div>
                            <div class="col-sm-6 mt-4">
                                <label for="website" class="form-label">Website</label>
                                <input class="form-control" type="text" name="website" id="website">
                            </div>
                            <div class="col-sm-6 mt-4">
                                <label for="instagram" class="form-label">Instagram</label>
                                <input class="form-control" type="text" name="instagram" id="instagram">
                            </div>
                            <div class="col-sm-6 mt-4">
                                <label for="facebook" class="form-label">Facebook</label>
                                <input class="form-control" type="text" name="facebook" id="facebook">
                            </div>
                            <div class="col-sm-6 mt-4">
                                <label for="linkedin" class="form-label">LinkedIn</label>
                                <input class="form-control" type="text" name="linkedin" id="linkedin">
                            </div>
                            <div class="col-sm-6 mt-4">
                                <label for="twitter" class="form-label">Twitter</label>
                                <input class="form-control" type="text" name="twitter" id="twitter">
                            </div>
                            <div class="col-sm-12 mt-4 text-center">
                                <button type="button" class="btn btn-light waves-effect me-3"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    {{-- Volunteer Details --}}
    <div id="volunteer-report-modal" class="modal fade modal-lg detailed-modal-popup" tabindex="-1"
        aria-labelledby="volunteer-report-modalLabel" aria-hidden="true" data-bs-scroll="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="volunteer-report-modalLabel">Volunteer Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row g-4 text-center justify-content-center">
                        <!-- Total Events -->
                        <div class="col-md-6 col-xl-3">
                            <div class="card shadow-sm h-100 border-0 rounded-4 bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="text-white">Total Organised Events</h6>
                                    <h3 class="fw-bold text-white total_organised_events" id="total_organised_events">0</h3>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Events -->
                        <div class="col-md-6 col-xl-3">
                            <div class="card shadow-sm h-100 border-0 rounded-4 bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="text-white">Total Co-Hosting Events</h6>
                                    <h3 class="fw-bold text-white total_cohosting_events" id="total_cohosting_events">0</h3>
                                </div>
                            </div>
                        </div>

                        <!-- Total Volunteers -->
                        <div class="col-md-6 col-xl-3">
                            <div class="card shadow-sm h-100 border-0 rounded-4 bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="text-white">Total Volunteered Events</h6>
                                    <h3 class="fw-bold text-white total_volunteered_events" id="total_volunteered_events">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- <form action="">
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="total_organised_events" class="form-label">Total Organised Events</label>
                                <input class="form-control total_organised_events" type="text" readonly id="total_organised_events">
                            </div>
                            
                            <div class="col-sm-12">
                                <label for="total_cohosting_events" class="form-label">Total Cohosting Events</label>
                                <input class="form-control total_cohosting_events" type="text" readonly id="total_cohosting_events">
                            </div>
                            
                            <div class="col-sm-12">
                                <label for="total_volunteered_events" class="form-label">Total Volunteered Events</label>
                                <input class="form-control total_volunteered_events" type="text" readonly id="total_volunteered_events">
                            </div>
                        </div>
                    </form> -->
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <x-reusables.reset-password />
@endsection
