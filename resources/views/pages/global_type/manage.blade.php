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
    $routeparam = ['slug' => $module];
    $default_banner_img = $module == 'categories' && isset($formData)
    ? $formData->formattedDefaultBannerImg()
    : asset('assets_front/images/event-imgs/big-2.jpg');
@endphp
@extends('layouts.backend_new', ['pageName' => "$pageName", 'parentPageName' => $parentPageName, 'parentRoute' => $parentRoute,'breadcrumb' => [
    ['name' => ucfirst($module), 'route' => route('index-global-type', ['slug' => $module])],
    ['name' => 'Manage ' . ucfirst($module), 'route' => ''],
]])
@section('css')
    <x-reusables.advanceformcss />
    <link rel="stylesheet" href="{{ asset('assets_front/vendor/cropperjs/cropper.min.css') }}">
    <style>
        .img-preview-container {
            width: 270px;
            height: 185px;
            overflow: hidden;
            border: 2px solid #ddd;
            margin: 10px auto;
        }
        #cropper-modal .modal-body {
            max-height: 60vh;
            overflow: hidden;
            padding: 0;
        }
        #crop-image-container {
            max-height: 60vh;
            width: 100%;
            overflow: hidden;
        }
        #image-to-crop {
            display: block;
            max-width: 100%;
            max-height: 60vh;
        }
        .admin-image-upload-btn input[type="file"] {
            display: none;
        }
    </style>
@endsection
@section('js')
    <x-reusables.advanceformjs />
    <script src="{{ asset('assets_front/vendor/cropperjs/cropper.min.js') }}"></script>
    <script src="{{ asset('assets_front/vendor/cropperjs/jquery-cropper.min.js') }}"></script>
    <script>
    $(document).ready(function () {
        let cropperInstance = null;

        $('#image-input').on('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (event) {
                $('#image-to-crop').attr('src', event.target.result);
                $('#cropper-modal').modal('show');
            };
            reader.readAsDataURL(file);
        });

        $('#cropper-modal').on('shown.bs.modal', function () {
            if (cropperInstance) {
                $('#image-to-crop').cropper('destroy');
                cropperInstance = null;
            }

            $('#image-to-crop').cropper({
                aspectRatio: 1000 / 660,
                viewMode: 1,
                autoCropArea: 0.8,
                movable: true,
                zoomable: true,
                scalable: true,
                preview: '.img-preview-container',
            });

            cropperInstance = true;
        });

        $('#cropper-modal').on('hidden.bs.modal', function () {
            if (cropperInstance) {
                $('#image-to-crop').cropper('destroy');
                cropperInstance = null;
            }

            if ($('#cropped_image').val() != '') {
                $('#current-preview').attr('src', $('#cropped_image').val());
            }

            $('#image-input').val('');
        });

        $('#crop-btn').on('click', function () {
            const canvas = $('#image-to-crop').cropper('getCroppedCanvas', {
                width: 1000,
                height: 660,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            const base64Data = canvas.toDataURL('image/jpeg', 0.85);
            $('#cropped_image').val(base64Data);
            $('#current-preview').attr('src', base64Data);
            $('#cropper-modal').modal('hide');
        });
    });
    </script>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <x-reusables.frontend.alert />
                    <form action="{{ $action_url }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-sm-4">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" value="{{ $name }}" name="name" id="name" required>
                            </div>
                            @if($module == 'categories')
                                <div class="row justify-content-center mt-3">
                                    <div class="col-sm-6 text-center">
                                        <label class="form-label">Default Banner Image</label>
                                        <small class="d-block">(Recommended image size: 1000 × 660 px)</small>

                                        <input type="hidden" name="cropped_image" id="cropped_image">

                                        <div class="img-preview-container">
                                            <img src="{{ $default_banner_img }}"
                                                id="current-preview"
                                                alt="Default Banner Preview"
                                                style="width:100%; height:100%; object-fit:cover;">
                                        </div>

                                        <div class="admin-image-upload-btn">
                                            <input type="file" id="image-input" accept="image/*">
                                            <label for="image-input" class="btn btn-outline-primary mb-0">
                                                Upload Image
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif
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

<div class="modal fade" id="cropper-modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crop Category Banner Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="crop-image-container">
                    <img id="image-to-crop" src="" alt="Image to crop">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="crop-btn">Crop & Use This</button>
            </div>
        </div>
    </div>
</div>
@endsection
