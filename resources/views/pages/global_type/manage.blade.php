@php
    $is_update_form = $formData ? true : false;
    $action_url = !$is_update_form
        ? route('create-global-type', ['slug' => $module])
        : route('update-global-type', ['slug' => $module, 'placeholder' => $formData->encrypted_id]);
    $name = old('name', $formData->name ?? '');
    $slug = old('slug', $formData->slug ?? '');
    $status = old('status', $formData->status ?? '');
    $type = old('type', $formData->type ?? '');
    $pageName = 'Manage ' . ucfirst($module);
    $parentPageName = ucfirst($module) . 's';
    $parentRoute = route('index-global-type', ['slug' => $module] );
    $routeparam = ['slug' => $module]
@endphp
@extends('layouts.backend_new', ['pageName' => "$pageName", 'parentPageName' => $parentPageName, 'parentRoute' => $parentRoute,'breadcrumb' => [
    ['name' => ucfirst($module), 'route' => route('index-global-type', ['slug' => $module])],
    ['name' => 'Manage ' . ucfirst($module), 'route' => ''],
]])
@section('css')
    <x-reusables.advanceformcss />
@endsection
@section('js')
    <x-reusables.advanceformjs />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <x-reusables.frontend.alert />
                    <form action="{{ $action_url }}" method="post">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-sm-4">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" value="{{ $name }}" name="name" id="name" required>
                            </div>
                            @if($module == 'tags')
                                <!-- <div class="col-sm-4 ">
                                    <label for="tagtype" class="form-label">Tag Type <span class="text-danger">*</span></label>
                                    <select class="form-select" name="type" id="tagtype" required>
                                        <option value="event" @selected($type == 'event')>Event</option>
                                        <option value="volunteer" @selected($type == 'volunteer')>Volunteer / Organizer</option>
                                    </select>
                                </div> -->
                            @endif
                        </div>
                        <div class="row mt-2">
                            <x-reusables.form-footer route="index-global-type" :routeparam="$routeparam" action="{{ $is_update_form ? 'update' : 'create' }}"
                                :module="$module" />
                        </div>
                    </form>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection
