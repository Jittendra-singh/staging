@php
    $pageName = 'Orders';
    $createUrl = route('create-order');
    $tableHead = ['Order ID', 'Event', 'Organizer', 'Purchased By', 'Total Tickets', 'Posted Date', 'Action'];
@endphp
@extends('layouts.backend_new', ['pageName' => $pageName, 'parentPageName' => $pageName])
@section('css')
    <x-reusables.datatablecss />
    <x-reusables.advanceformcss/>
@endsection
@section('js')
    <x-reusables.datatablejs />
    <x-reusables.advanceformjs />
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
                                <label for="event" class="form-label">Search & Select Purchased By</label>
                                <select class="form-control select2ajax" datatype="all_volunteers" id="purchased_by_id" name="purchased_by_id">
                                    @if(request('purchased_by_id') && $selected_all_volunteer)
                                        <option value="{{ request('purchased_by_id') }}" selected>
                                            {{ $selected_all_volunteer->first_name.' '.$selected_all_volunteer->last_name.' ('.$selected_all_volunteer->email_address.')' }}
                                        </option>
                                    @endif
                                </select>
                            </div>
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
                            <!-- <div class="form-group">
                                <label for="volunteer" class="form-label">Purchased By</label>
                                <input type="text" class="form-control" id="volunteer" name="volunteer"
                                    value="{{ request('volunteer') }}" placeholder="Search by Name">
                            </div>
                            <div class="form-group">
                                <label for="event" class="form-label">Event Name</label>
                                <input type="text" class="form-control" id="event" name="event"
                                    value="{{ request('event') }}" placeholder="Search by Name">
                            </div> -->
                            <div class="form-group">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="{{ request('start_date') }}">
                            </div>
                            <div class="form-group">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="{{ request('end_date') }}">
                            </div>
                            <div class="form-group">
                                <label for="event" class="form-label">Sort By</label>
                                <select class="form-control select2" id="sort_by" name="sort_by">
                                    <option value="1" {{ request('sort_by',1) == 1 ? 'selected' : '' }}>Date Desc</option>
                                    <option value="2" {{ request('sort_by') == 2 ? 'selected' : '' }}>Date Asc</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('index-orders') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            {{-- <x-reusables.fab-button :title="$pageName" :module="$module" :url="$createUrl" /> --}}
            <div class="table-responsive">
                <table id="custom_datatable" sort="1" class="table table-bordered nowrap w-100 volunteer-records"
                    dt-responsive>
                    <x-reusables.table-header :tableHead="$tableHead" />
                    <tbody>
                        @forelse ($listingData as $data)
                            <tr>
                                <td>{{ @$data->order_number }}</td>
                                <td>{{ @$data->event->event_name }}</td>
                                <td>{!! @$data->event->creator->name.' <br/>('.@$data->event->creator->email_address.')' !!}</td>
                                <td>{!! @$data->creator->name.' <br/>('.@$data->creator->email_address.')' !!}</td>
                                <td>{{ @$data->tickets->count() }}</td>
                                <td>{{ @$data->createdDateTime }}</td>
                                <td>
                                    <x-reusables.action-buttons :id="$data->encrypted_id" :module="$module" :name="$data->name"
                                        :data="$data" />
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
@endsection
