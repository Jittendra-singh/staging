@php
    $flash = null;
    $flash = request()->hasSession() ? request()->session()->get('flash_data') : null;
    $message = '';
    $status = false;
    if ($flash) {
        $notification_class = $flash['status'] ? 'success' : 'danger';
        $message = $flash['message'];
        $status = $flash['status'];
    } elseif (isset($errors) && $errors->any()) {
        $notification_class = 'danger';
        foreach ($errors->all() as $key => $error) {
            if($key>0){
                $message .= "<br>";
            }
            $message .= "<span>$error</span>";
        }
    }
    $flashData = [];
    if ($message) {
        $flashData = [
            'status' => $status,
            'class' => $notification_class,
            'message' => $message,
        ];
    }
@endphp
@if(isset($flashData) && !empty($flashData))
    @if($flashData['status'])
    <div class="alert alert-success mt-3">
        {!! $flashData['message'] !!}
        <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @else
    <div class="alert alert-danger mt-3">
        {!! $flashData['message'] !!}
        <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
@endif