@php
    $pageName = 'Events';
    $createUrl = route('create-event');
    $tableHead = ['Name', 'Organizer', 'Venue', 'Event Start Date', 'Event End Date','Co-Host','Attendees','Total Tickets', 'Created Date', 'Visibility', 'Action'];
    //print_r($listingData->toArray());die();
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
@section('buttons')
    <x-reusables.fab-button :title="'Event'" :module="$module" :url="$createUrl" />
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
                                <label for="event_name" class="form-label">Event Name</label>
                                <input type="text" class="form-control" id="event_name" name="event_name" value="{{ request('event_name') }}" placeholder="Name">
                            </div>
                            <div class="form-group">
                                <label for="event" class="form-label">Search & Select Organizer</label>
                                <select class="form-control select2ajax" datatype="organiser" id="organiser_select" name="organiser_id">
                                    @if(request('organiser_id') && $selected_organiser)
                                        <option value="{{ request('organiser_id') }}" selected>
                                            {{ $selected_organiser->first_name.' '.$selected_organiser->last_name.' ('.$selected_organiser->email_address.')' }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="event" class="form-label">Search & Select Volunteer</label>
                                <select class="form-control select2ajax" datatype="volunteer" id="volunteer_select" name="volunteer_id">
                                    @if(request('volunteer_id') && $selected_volunteer)
                                        <option value="{{ request('volunteer_id') }}" selected>
                                            {{ $selected_volunteer->first_name.' '.$selected_volunteer->last_name.' ('.$selected_volunteer->email_address.')' }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="event" class="form-label">Search & Select Co-Host</label>
                                <select class="form-control select2ajax" datatype="organiser" id="cohost_select" name="cohost_id">
                                    @if(request('cohost_id') && $selected_cohost)
                                        <option value="{{ request('cohost_id') }}" selected>
                                            {{ $selected_cohost->first_name.' '.$selected_cohost->last_name.' ('.$selected_cohost->email_address.')' }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                            <!-- <div class="form-group">
                                <label for="organiser" class="form-label">Organizer</label>
                                <select class="form-select " id="organiser" name="organiser">
                                    <option value="" disabled selected>Pick Option</option>
                                    @foreach ($volunteers as $organiser)
                                        <option value="{{ $organiser->id }}"
                                            {{ request('organiser') == $organiser->id ? 'selected' : '' }}>
                                            {{ $organiser->name ."/". $organiser->email_address}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="cohost" class="form-label">Co-Host</label>
                                <select class="form-select " id="cohost" name="cohost">
                                    <option value="" disabled selected>Pick Option</option>
                                    @foreach ($volunteers as $cohost)
                                        <option value="{{ $cohost->id }}"
                                            {{ request('cohost') == $cohost->id ? 'selected' : '' }}>
                                            {{ $cohost->name ."/". $cohost->email_address}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="attendee" class="form-label">Attendee</label>
                                <select class="form-select " id="attendee" name="attendee">
                                    <option value="" disabled selected>Pick Option</option>
                                    @foreach ($volunteers as $attendee)
                                        <option value="{{ $attendee->id }}"
                                            {{ request('attendee') == $attendee->id ? 'selected' : '' }}>
                                            {{ $attendee->name ."/". $attendee->email_address }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> -->
                        </div>
                        <div class="d-flex justify-content-start gap-3 align-items-end mt-2">
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
                                <label for="event" class="form-label">Start Date Sort By</label>
                                <select class="form-control select2" id="sort_by" name="sort_by">
                                    <option value="1" {{ request('sort_by',1) == 1 ? 'selected' : '' }}>Date Desc</option>
                                    <option value="2" {{ request('sort_by') == 2 ? 'selected' : '' }}>Date Asc</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('index-events') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <!-- end row -->
            <div class="table-responsive">
                <table id="event_datatable" sort="1" class="table table-bordered nowrap w-100 volunteer-records" dt-responsive>
                    <x-reusables.table-header :tableHead="$tableHead" />
                    <tbody>
                        @forelse ($listingData as $data)
                        @php 
                        $totalTicketCount = '-';
                        if($data->eventTickets->count()>0){
                            $totalTicketCount = 0;
                            foreach($data->eventTickets as $eventTicket){
                                $totalTicketCount += $eventTicket->available_quantity;
                            }
                        }
                        $totalBookedTicket = $data->tickets->where('rsvp_confirmation',1)->count();
                        @endphp 
                            <tr>
                                <td>{{ $data->event_name }}</td>
                                <td>{!! $data->creator->name.' <br/>('.$data->creator->email_address.')' !!}</td>
                                <td>{{ $data->venue }}</td>
                                <td>{{ $data->start_datetime->format(config('app.dateTimeFormat')) }}</td>
                                <td>{{ $data->end_datetime ? $data->end_datetime->format(config('app.dateTimeFormat')):'' }}</td>
                                <td>{{ $data->cohosts_count }}</td>
                                <td>{{ $totalBookedTicket }}</td>
                                <td>{{ $totalTicketCount }}</td>
                                <td>{{ $data->created_at }}</td>
                                <td> 
                                    {{-- <form method="post" class="event-status-form" action="{{ route('toggle-event-status', ['placeholder' => $data->encrypted_id]) }}">
                                        @csrf
                                        <select name="type" id="" class="form-select" onchange="this.form.submit()" >
                                            <option @selected($data->status == 'draft')     value="draft"> Drafted </option>
                                            <option @selected($data->status == 'published') value="published"> Published </option>
                                            <option @selected($data->status == 'archived')  value="archived"> Archived </option>
                                        </select>
                                    </form> --}}
                                    <x-reusables.list-switch :data="$data" field="is_active" url="{{route('toggle-event-status' , ['placeholder' => $data->encrypted_id])}}"/>
                                </td> 
                                <td>
                                    <x-reusables.action-buttons :id="$data->encrypted_id" :module="$module"
                                        :name="$data->name" :data="$data" />
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
@endsection
