@php
    $tableHead = ['Name', 'Phone', 'Email', 'Topic', 'Message', 'Date'];
    $f_status = '';
    if (isset($_POST['f_status']) && !empty($_POST['f_status'])) {
        $f_status = $_POST['f_status'];
    }
@endphp
@extends('layouts.backend_new', ['pageName' => 'Other Inquiry', 'parentPageName' => 'Other Inquiry'])
@section('css')
    <x-reusables.datatablecss />
    <x-reusables.advanceformcss />
@endsection
@section('js')
    <x-reusables.datatablejs />
    <x-reusables.advanceformjs />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="" method="post" id="inquiry-form">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <label class="form-label">Created Start & End Date</label>
                                <input name="f_date" type="text" value="{{ $f_date }}" class="form-control"
                                    id="inquiry-date">
                            </div>
                            <div class="col-sm-3 offset-sm-6 text-right">
                                <div class="mb-3">
                                    <label class="form-label">Request From</label>
                                    <select class="form-select" name="f_status" id="inquiry-status">
                                        <option value="">All</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- end row -->
                    <div class="table-responsive">
                        <table id="custom_datatable" sort="5" class="table table-bordered  nowrap w-100"
                            dt-responsive>
                            <x-reusables.table-header :tableHead="$tableHead" />
                            <tbody>
                                @forelse ($listingData as $data)
                                    <tr>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->phone }}</td>
                                        <td>{{ $data->email }}</td>
                                        <td>{{ $data->topic }}</td>
                                        <td style="white-space:normal;">
                                            @php
                                                $msg = $data->description;
                                                $more_msg = substr($msg, 100);
                                                $less_msg = substr($msg, 0, 100);
                                            @endphp
                                            {{ $less_msg }}
                                            @if (!empty($more_msg))
                                                <a href="javascript:void(0);" showhideid="{{ $data->id }}"
                                                    class="seemore seemore{{ $data->id }}">... see more</a>
                                                <span
                                                    style="display:none;"
                                                    class="seelessdiv{{ $data->id }}">
                                                        {{ $more_msg }} 
                                                        <a href="javascript:void(0);" showhideid="{{ $data->id }}"
                                                        class="seeless">see less</a>
                                                </span>
                                            @endif
                                        </td>
                                        <td><span class="d-none">{{ $data->createdDateTimeStr }}</span>
                                            {{ $data->created_at }}
                                        </td>
                                    </tr>
                                @empty
                                    <x-reusables.empty-data :colspan="count($tableHead)" />
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
    @if(false)
    {{-- add multiple referrer modal --}}
    <div id="withdrawal_modal" class="modal fade" tabindex="-1" aria-labelledby="withdrawalLabel" aria-hidden="true"
        data-bs-scroll="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawalLabel">Approve Withdrawal Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ url(config('app.admin_prefix') . 'approve-withdrawal-request') }}" method="POST">
                        @csrf
                        <input name="withdrawal_id" type="hidden" id="withdrawal_id">
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="payment_type" class="form-label ">Payment Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select font-size-13 select2" name="payment_type" id="payment_type"
                                    required>
                                    <option value="">Select option</option>
                                    <option value="1">Cash</option>
                                    <option value="2">Card</option>
                                    <option value="3">UPI</option>
                                    <option value="4">Net Banking</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label for="payment_info" class="form-label">Any Payment Information</label>
                                <input class="form-control" type="text" value="" name="payment_info"
                                    id="payment_info">
                            </div>
                            <div class="col-sm-12">
                                <label for="note" class="form-label">Other Notes</label>
                                <textarea class="form-control" name="note" id="note"></textarea>
                            </div>
                            <div class="col-sm-12 mt-4 text-center">
                                <button type="button" class="btn btn-light waves-effect me-3"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Approve Now</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    {{-- add multiple referrer modal end --}}
@endsection
