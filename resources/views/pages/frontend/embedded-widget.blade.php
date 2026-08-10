@extends('layouts.frontend')
@push('style')
<style>
#showWidgetDetail .modal-dialog {
    width: min(1040px, calc(100vw - 32px)) !important;
    max-width: 1040px !important;
}

#showWidgetDetail .modal-content {
    border: 0;
    border-radius: 10px;
    overflow: hidden;
}

#showWidgetDetail .modal-body {
    max-height: calc(100vh - 190px);
    overflow-y: auto;
    padding: 24px 0px !important;
    /* background: #f8fafc; */
}

.widget-detail-section {
    background: #fff;
    border: 1px solid #e9edf2;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 18px;
}

.widget-detail-section:last-child {
    margin-bottom: 0;
}

.widget-detail-title {
    font-size: 16px;
    font-weight: 700;
    color: #172033;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #eef1f5;
}

.widget-detail-grid {
    row-gap: 14px;
}

.widget-detail-item {
    min-height: 100%;
    padding: 12px 14px;
    background: #fbfcfe;
    border: 1px solid #eef1f5;
    border-radius: 6px;
}

.widget-detail-label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: #697586;
    margin-bottom: 6px;
    text-transform: uppercase;
}

.widget-detail-value {
    color: #1f2937;
    font-size: 14px;
    line-height: 1.5;
    word-break: break-word;
}

.widget-status-active {
    color: #198754;
    font-weight: 700;
}

.widget-status-inactive {
    color: #dc3545;
    font-weight: 700;
}

.widget-code-help {
    display: block;
    color: #697586;
    margin-bottom: 12px;
}

.widget-code-block {
    display: block;
    width: 100%;
    margin: 0;
    padding: 16px;
    background: #111827;
    color: #e5e7eb;
    border-radius: 8px;
    white-space: pre-wrap;
    word-break: break-word;
    line-height: 1.6;
}

@media (max-width: 767px) {
    #showWidgetDetail .modal-body {
        padding: 16px;
    }

    .widget-detail-section {
        padding: 16px;
    }
}
</style>
@endpush
@section('content')
    <div class="wrapper wrapper-body">
        <div class="dashboard-body">
            <div class="container-fluid"> 
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-between">
                        <div class="d-main-title">
                            <h3><i class="fa-solid fa-calendar-days me-3 mb-3"></i>Embedded Widget</h3>
                            @php
                                $breadcrumbs = [ 
                                    [
                                        'name' => 'Embedded Widget', 
                                        'url' => route('front-embedded-widget')
                                    ],
                                ];
                                $embedCode = session()->pull('clipboard_embed_code');
                            @endphp     
                            <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs"/>
                            <!-- <p class="mb-0 mt-3">You can send a message to your RSVP’s in the ‘Registration List’.</p> --> 
                        </div>
                        <a class="primary-btn w-fits" href="{{route('front-manage-widget')}}">Create Widget</a>
                    </div>
                </div>
                <x-reusables.frontend.alert />
                <div class="col-md-12">
                    <div class="conversion-setup">
                        <div class="main-card mt-5">
                            <div class="dashboard-wrap-content p-4">
                                <div class="table-card mt-4">
                                    <div class="main-table">
                                        <div class="table-responsive"> 
                                            <table class="table">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th scope="col">ID</th>
                                                        <th scope="col">Widget Title</th>
                                                        <th scope="col">Display Style</th>
                                                        <th scope="col">Location</th>
                                                        <th scope="col">Created At</th>
                                                        <th scope="col">Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($listing_data as $data)
                                                        
                                                    <tr class="single-tr">										
                                                        <td>{{$data->wid_number}}</td>								
                                                        <td>{{$data->title}}</td>	
                                                        <td>{{$data->type==1?'List View':'Map View'}}</td>	
                                                        <td>{{$data->location??'N/A'}}</td>	
                                                        <td>{{$data->created_at}}</td>
                                                        <td>
                                                            @if($data->is_active)
                                                                <span class="text-success">Active</span>
                                                            @else
                                                                <span class="text-danger">Inactive</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="dropdown position-absolute translate-middle dropup" >
                                                                <button class="option-btn" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                                                <div class="dropdown-menu dropdown-menu-right" data-popper-placement="top-end">
                                                                    <a href="{{route('front-manage-widget', ['id'=> encrypt($data->id)])}}" class="dropdown-item">
                                                                        <i class="fa-solid fa-gear me-3"></i>Edit</a>
                                                                    <a class="dropdown-item showWidgetDetailBtn" href="javascript:void(0);" url="{{route('front-view-widget-detail', ['id' => encrypt($data->id)])}}">
                                                                        <i class="fa-solid fa-eye"></i> View Code & Detail
                                                                    </a>
                                                                    <a class="dropdown-item showWidgetPreviewBtn" href="javascript:void(0);" url="{{route('front-preview-widget', ['id' => encrypt($data->id)])}}">
                                                                        <i class="fa-solid fa-eye"></i> Preview Widget
                                                                    </a>
                                                                    <a class="dropdown-item" href="{{route('front-active-inactive-widget', ['id' => encrypt($data->id)])}}">
                                                                        <i class="fa-solid fa-ban"></i> {{$data->is_active==1 ? 'Deactivate Now' : 'Activate Now'}}
                                                                    </a>
                                                                    <a class="dropdown-item delete-record" data-url="{{route('front-delete-widget', ['id' => encrypt($data->id)])}}" href="javascript:void(0);">
                                                                        <i class="fa-solid fa-trash-can"></i> Delete
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    @if($listing_data->isEmpty())
                                                    <tr>
                                                        <td colspan="6" class="text-center">No widget found.</td>
                                                    </tr>
                                                    @endif
                                                </tbody>									
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>							
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="modal fade" id="showWidgetDetail" tabindex="-1" aria-labelledby="showWidgetDetailsLabel"
    aria-modal="true" role="dialog"> 
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showWidgetDetailLabel">Embedded Widget Details</h5>
                <button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i
                        class="uil uil-multiply"></i></button>
            </div>
            <div class="modal-body" id="showWidgetDetailBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="primary-btn w-fit" id="copyWidgetCode">Copy Code</button>
                <button type="button" class="primary-btn w-fit"
                    data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<x-reusables.google :locationarray="[]"/>
@endsection
@push('script')
    <x-reusables.advanceformjs />
<script>
 let ajaxTimer = null; // ✅ declare first

$(document).ready(function () {

    $('.selectpicker1').selectpicker();

    $(document).on('input', '.bs-searchbox input', function () {
        $('#bs-select-3 .no-results').text('Type at leat 2 character..');

        clearTimeout(ajaxTimer);

        let query = $(this).val();

        if (query.length < 2) return;
        $('#bs-select-3 .no-results').text('Loading...');
        ajaxTimer = setTimeout(function () {
            $.ajax({
                url: "{{ route('front-get-organizer-options') }}",
                type: "POST",
                data: {
                    search: query,
				    '_token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    let $select = $('.selectpicker1');
                    $select.empty();
                    if(res.results.length > 0){
                        res.results.forEach(item => {
                            $select.append(
                                `<option value="${item.id}">${item.text}</option>`
                            );
                        });
                    }else{
                        $('#bs-select-3 .no-results').text('No results found');
                    }
                    $select.selectpicker('refresh');
                }
            });

        }, 300);
    });
});
</script>
@if($embedCode)
    <textarea id="embedCode" style="position:absolute; left:-9999px;">{{ $embedCode }}</textarea>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const code = document.getElementById('embedCode').value;

            navigator.clipboard.writeText(code).then(function () {
                console.log('Embed code copied');

                // optional success toast
                // alert('Embed code copied to clipboard');
            }).catch(function (err) {
                console.error('Copy failed:', err);
            });
        });
    </script>
@endif
@endpush