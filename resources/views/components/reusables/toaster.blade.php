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
 <script>
     let show_alert = JSON.parse('{!! json_encode([]) !!}');
 </script>
