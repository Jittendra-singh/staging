 <!-- <div class="position-fixed top-0 end-0 p-3" style="z-index: 11111">
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
 </div> -->
 <!-- Error / Warning Toast -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 11111; margin-top: 50px">
    <div id="error_toast" class="toast align-items-center" role="alert" aria-live="assertive" aria-atomic="true"
         style="
            background: #fff;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #f59e0b;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.10);
            min-width: 320px;
            max-width: 380px;
            padding: 14px 16px;
         ">
        <div class="d-flex align-items-start gap-3">
            <!-- Amber warning icon circle -->
            <div style="
                width: 32px; height: 32px; flex-shrink: 0;
                background: #fef3c7;
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
            ">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 1.5L14.928 13.5H1.072L8 1.5Z" stroke="#f59e0b" stroke-width="1.5" stroke-linejoin="round" fill="none"/>
                    <line x1="8" y1="6" x2="8" y2="9.5" stroke="#f59e0b" stroke-width="1.5" stroke-linecap="round"/>
                    <circle cx="8" cy="11.5" r="0.75" fill="#f59e0b"/>
                </svg>
            </div>

            <!-- Text content -->
            <div style="flex: 1; min-width: 0;">
                <!-- <p style="font-size: 0.88rem; font-weight: 700; color: #111827; margin: 0 0 2px;">Schedule Conflict</p> -->
                <strong class="toast-body-text" style="font-size: 0.82rem; color: #6b7280; margin: 0; line-height: 1.5;">
                    The user is already registered for an overlapping event.
                </strong>
            </div>

            <!-- Close button -->
            <button type="button" class="btn-close btn-close-sm ms-auto flex-shrink-0"
                    data-bs-dismiss="toast" aria-label="Close"
                    style="font-size: 0.65rem; margin-top: 2px; opacity: 0.5;"></button>
        </div>
    </div>
</div>

<!-- Success Toast -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 11111; margin-top: 50px">
    <div id="success_toast" class="toast align-items-center" role="alert" aria-live="assertive" aria-atomic="true"
         style="
            background: #fff;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #10b981;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.10);
            min-width: 320px;
            max-width: 380px;
            padding: 14px 16px;
         ">
        <div class="d-flex align-items-start gap-3">
            <!-- Green success icon circle -->
            <div style="
                width: 32px; height: 32px; flex-shrink: 0;
                background: #d1fae5;
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
            ">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="8" cy="8" r="6.5" stroke="#10b981" stroke-width="1.5" fill="none"/>
                    <path d="M5 8.5l2 2 4-4" stroke="#10b981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <!-- Text content -->
            <div style="flex: 1; min-width: 0;">
                <!-- <p style="font-size: 0.88rem; font-weight: 700; color: #111827; margin: 0 0 2px;">Success</p> -->
                <strong class="toast-body-text" style="font-size: 0.82rem; color: #6b7280; margin: 0; line-height: 1.5;">
                    Action completed successfully.
                </strong>
            </div>

            <!-- Close button -->
            <button type="button" class="btn-close btn-close-sm ms-auto flex-shrink-0" data-bs-dismiss="toast" aria-label="Close" style="font-size: 0.65rem; margin-top: 2px; opacity: 0.5;"></button>
        </div>
    </div>
</div>
 <script>
     let show_alert = JSON.parse('{!! json_encode([]) !!}');
 </script>
