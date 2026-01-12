 <div class="position-fixed top-0 end-0 p-3" style="z-index: 11111">
     <div id="error_toast" class="toast bg-danger" role="alert" aria-live="assertive" aria-atomic="true">
         <div class="toast-header">
             <strong class="me-auto">Hello, world! This is a toast message.</strong>
             <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
         </div>
     </div>
 </div>
 <div class="position-fixed top-0 end-0 p-3" style="z-index: 11111">
     <div id="success_toast" class="toast bg-success" role="alert" aria-live="assertive" aria-atomic="true">
         <div class="toast-header">
             <strong class="me-auto">Hello, world! This is a toast message.</strong>
             <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
         </div>
     </div>
 </div>
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
         foreach ($errors->all() as $error) {
             $message .= "<span>$error</span><br>";
         }
     }
     $flashData = [];
     if ($message) {
         $flashData = [
             'class' => $notification_class,
             'message' => $message,
         ];
     }

 @endphp
 <script>
     let show_alert = JSON.parse('{!! isset($flashData) && $flashData ? json_encode($flashData) : json_encode([]) !!}');
 </script>
