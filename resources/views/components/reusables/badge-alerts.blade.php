@php
    
    $flash = request()->session()->get('flash_data');

    $message = '';
    $status = false;
    
    if ($flash) {

        $notification_class = $flash['status'] ? 'success' : 'error';
        $message = $flash['message'];
        $status = $flash['status'];

    } elseif ($errors->any()) {

        $notification_class = 'danger';

        foreach ($errors->all() as $error) {
            $message .= $error;
        }
    }
    
@endphp

@if ($message && false)

    <div class="alert alert-{{$notification_class}} alert-dismissible" role="alert">
        <div class="mb-0">
            {{$message}}
        </div>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    
@endif
