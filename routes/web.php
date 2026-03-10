<?php
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| 
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
| 
*/
/**View Composer */
View::composer('*', function ($view) {
    $settings = \App\Models\Setting::first();
    $view->with('settings', $settings);
});
Route::get('home', function () {return redirect()->route('view-events');})->name('home');
Route::get('/', function () {return redirect()->route('view-events');});
/**
 * -------------------------------------------------------------------
 * Front Website Routes
 * -------------------------------------------------------------------
 */
Route::view('view-mail' , 'pages.emails.community-service-form')->name('view-mail');
Route::controller(\App\Http\Controllers\StripeController::class)->group(function(){
    Route::post('connect-stripe', 'connectStripe')->name('connect-stripe');
    Route::get('stripe-connect-refresh', 'stripeConnectRefresh')->name('stripe-connect-refresh');
    Route::get('stripe-connect-done/{id}/{acc_id}', 'stripeConnectDone')->name('stripe-connect-done');
    Route::get('stripe-payment-success', 'stripePaymentSuccess')->name('stripe-payment-success');
    Route::post('stripe-checkout-webhook', 'stripeCheckoutWebhook')->name('stripe-checkout-webhook')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
});
Route::controller(\App\Http\Controllers\FrontOrderController::class)->group(function(){
    Route::post('land-checkout', 'landToCheckout')->name('land-to-checkout');
    Route::get('checkout', 'checkout')->name('front-checkout');
    Route::post('auto-populate-attandee', 'autoPopulateAttandee')->name('auto-populate-attandee');
    Route::post('front-create-order', 'frontCreateOrder')->name('front-create-order');
    Route::post('check-before-create-order', 'checkBeforeCreateOrder')->name('check-before-create-order');

    //ical 
    Route::get('add-event-to-calendar/{id}', 'downloadICS')
     ->name('add-event-to-calendar');

    // alert 
    Route::get('create-alert' , 'createAlertPage')->name('front-create-alert');
    Route::post('create-weekly-newsletter-alert' , 'submitCreateWeeklyNewsletterAlert')->name('submit-create-weekly-newsletter-alert');
    Route::post('create-alert' , 'submitCreateAlert')->name('submit-create-alert');
    Route::post('create-newsletter' , 'submitCreateNewsletter')->name('submit-create-newsletter');

    /**waiver form*/
    Route::get('view-waiver-form/{ticket}' , 'viewWaiverForm')->name('view-waiver-form');
    Route::post('submit-waiver-form/{ticket}' , 'submitWaiverForm')->name('submit-waiver-form');
    Route::post('submit-rsvp-form/{ticket}' , 'submitRSVPForm')->name('submit-rsvp-form');
    Route::get('cancel-rsvp/{ticket}/{type}' , 'cancelRSVP')->name('cancel-rsvp-get');
    Route::post('cancel-rsvp/{ticket}' , 'cancelRSVP')->name('cancel-rsvp');
    Route::post('cancel-refund-paid-ticket' , 'cancelRefundPaidTicket')->name('cancel-refund-paid-ticket');
    Route::post('cancel-refund-paid-ticket-attendee/{ticket}/{type}' , 'cancelRefundPaidTicketAttendee')->name('cancel-refund-paid-ticket-attendee');
    Route::get('rsvp-cancelled-page/{event}', 'rsvpCancelledPage')->name('rsvp-cancelled-page');
    
    Route::get('cancel-registration-front/{ticket}', 'cancelRegistrationFront')->name('cancel-registration-front');
    Route::get('confirm-cancel-registration-front/{ticket}/{type}', 'confirmCancelRegistrationFront')->name('confirm-cancel-registration-front');

    Route::get('download-event-ical/{placeholder}' , 'downloadEventIcal')->name('download-event-ical');
    Route::post('check-event-slug', 'checkEventSlug')->name('front-check-event-slug');

    Route::get('update-ticket-dates', 'updateTicketDates')->name('update-ticket-dates');
});
Route::controller(\App\Http\Controllers\FrontController::class)->group(function(){

    Route::get('show-widget/{id}', 'showWidget')->name('front-show-widget');
    Route::post('add-widget-log/{widget_id}/{type}/{ref_id?}', 'addWidgetLog')->name('front-add-widget-log');

    Route::get('events-lists' , 'viewEvents')->name('view-events');
    Route::get('international-events' , 'viewInternationalEvents')->name('view-international-events');
    //Route::get('event/{slug}' , 'viewEventPage')->name('view-event-details');
    Route::post('verify-ticket-passcode/{ticket}' , 'verifyTicketPasscode')->name('verify-ticket-passcode');
    Route::get('organizer-detail/{slug?}' , 'organizerDetail')->name('organizer-detail');
    Route::post('save-organizer-lead' , 'saveOrganizerLead')->name('save-organizer-lead');
    Route::get('download-registration-calendar/{slug}', 'generateIcs')->name('front-registration-calendar');
    Route::get('booking-confirmed/{order}', 'bookingConfirmed')->name('front-booking-confirmed');
    Route::get('rsvp-confirmation/{event}', 'rsvpWaiverConfirmed')->name('front-rsvp-confirmation');
    Route::get('unsubscribe-alert/{user}/{alert?}', 'unsubscribeAlert')->name('unsubscribe-alert');
    Route::get('confirmed-unsubscribe-alert/{user}/{alert?}', 'confirmedUnsubscribeAlert')->name('confirmed-unsubscribe-alert');
    Route::get('show-community-service-pdf/{ticket}', 'showCommunityServicePdf')->name('show-community-service-pdf');
    Route::get('front-save-ticket-notify/{ticket}', 'saveTicketNotify')->name('save-ticket-notify');

    // cron
    Route::get('cron-weekly-alerts' , 'cronWeeklyAlerts')->name('cron-weekly-alerts');
    Route::get('cron-ticket-notifications' , 'cronTicketNotifications')->name('cron-ticket-notifications');
    /*--------------------------------------------------------------------------------------- *
    *Volunteer/Organisation Portal 
    * --------------------------------------------------------------------------------------- */
    Route::middleware(['auth:front'])->group(function () {
        Route::get('dashboard', 'dashboard')->name('front-dashboard');
        Route::get('cleanup-attending', 'cleanupAttending')->name('front-cleanup-attending');
        Route::post('get-organizer-options' , 'getOrganizerOptions' )->name('front-get-organizer-options');

        Route::get('embedded-widget', 'embeddedWidget')->name('front-embedded-widget');
        Route::get('manage-widget/{id?}', 'manageWidget')->name('front-manage-widget');
        Route::post('save-widget/{id?}', 'saveWidget')->name('front-save-widget');
        Route::get('embedded-widget-report', 'embeddedWidgetReport')->name('front-embedded-widget-report');
        Route::post('embedded-widget-report', 'embeddedWidgetReport')->name('front-embedded-widget-report');
        //Route::post('create-widget' , 'createWidget')->name('front-create-widget');
        Route::post('view-widget-detail/{id}', 'viewWidgetDetail')->name('front-view-widget-detail');
        Route::post('preview-widget/{id}', 'previewWidget')->name('front-preview-widget');
        Route::get('active-inactive-widget/{id}' , 'activeInactiveWidget')->name('front-active-inactive-widget');
        Route::delete('delete-widget/{id}' , 'deleteWidget')->name('front-delete-widget');
        
        
        Route::get('community-form/{placeholder}', 'communityForm')->name('community-form');
        Route::get('event-feedback/{placeholder}', 'eventFeedback')->name('event-feedback');
        Route::get('waiver.form/{ticket}' , 'waiverForm')->name('waiver.form');
        /**EMAIL ALERTS */
        Route::get('email-alerts', 'emailAlerts')->name('front-email-alerts');
        Route::get('trigger-email-alert/{id}', 'triggerEmailAlert')->name('front-trigger-email-alerts');
        Route::post('save-email-alerts', 'saveEmailAlerts')->name('front-save-email-alerts');
        Route::get('disable-email-trigger/{id}', 'disableEmailAlerts')->name('front-disable-email-alerts');
        Route::delete('delete-alert/{id}', 'deleteAlerts')->name('front-delete-alerts');
        /**CLEANUP LISTING */
        Route::post('add-co-host' , 'addCoHost')->name('front-add-co-host');
        Route::post('update-co-host/{id}' , 'updateCoHost')->name('front-update-co-host');
        Route::post('remove-cohost/{id}' , 'removeCoHost')->name('front-remove-cohost');
        //Route::post('change-cohost-permission/{id}' , 'changeCoHostPermission')->name('front-change-cohost-permission');
        Route::post('re-invite-cohost/{id}' , 'reinviteCoHost')->name('front-re-invite-cohost');

        Route::get('cleanup-hosting/{type?}', 'cleanupHosting')->name('front-cleanup-hosting');
        Route::delete('cleanup-hosting/{id}', 'cleanupHostingDelete')->name('front-cleanup-hosting-delete');
        Route::post('send-rsvp-emails/{slug}' , 'sendRsvpEmails')->name('front-send-rsvp-emails');
        Route::get('post-an-event/{id?}', 'postEvent')->name('front-post-a-event');
        Route::post('manage-ticket-type-html/{id?}' , 'manageTicketTypeHtml')->name('manage-ticket-type-html');
        Route::post('save-ticket-type' , 'saveTicketType')->name('save-ticket-type');
        Route::post('update-ticket-status/{id}' , 'updateTicketStatus')->name('update-ticket-status');
        Route::get('duplicate-an-event/{slug}', 'duplicateAnEvent')->name('front-duplicate-an-event');
        Route::post('save-cleanup-basic-details', 'saveCleanUpBasicDetails')->name('front-save-cleanup-basic-details');
        Route::post('save-cleanup-media-details', 'saveCleanUpMediaDetails')->name('front-save-cleanup-media-details');
        Route::post('save-cleanup-ticket-details', 'saveCleanUpTicketDetails')->name('front-save-cleanup-ticket-details');
        Route::post('save-cleanup-seo-details', 'saveCleanUpSeoDetails')->name('front-save-cleanup-seo-details');
        Route::post('save-cleanup-after-action', 'saveCleanUpAfterAction')->name('front-save-cleanup-after-action');
        Route::post('send-thankyou-request-email-front', 'sendThankyouRequestEmailFront')->name('send-thankyou-request-email-front');
        Route::post('/get-focus-keywords-frontend', 'getFocusKeywordsFront')->name('get-focus-keywords-frontend');
        Route::post('/get-event-meta-data', 'getEventMetaData')->name('get-event-meta-data');
        Route::post('/update-event-publish-status', 'updateEventPublishStatus')->name('update-event-publish-status');
        
        
        /**EVENT REGISTRATION */
        Route::get('stripe-connect', 'stripeConnect')->name('front-stripe-connect');
        Route::post('disconnect-stripe-connect', 'disconnectStripeConnect')->name('front-disconnect-stripe-connect');
        Route::get('sales-report', 'salesReport')->name('front-sales-report');
        Route::get('registration-list', 'registrationList')->name('front-registration-list');
        Route::post('post-registration-notes', 'postRegistrationNotes')->name('front-post-registration-notes');
        Route::get('export-registration', 'exportRegistration')->name('front-export-registration');
        Route::post('ticket-mark-checkin/{id}', 'ticketMarkCheckin')->name('ticket-mark-checkin');
        Route::post('request-community-form/{id}', 'requestCommunityForm')->name('request-community-form');
        
        
        /**PROFILE HANDLER */
        Route::get('profile', 'profile')->name('front-profile');
        Route::post('save-profile', 'saveProfile')->name('save.front-profile');
        Route::post('save-organisation-details', 'saveOrganisationDetails')->name('save.front.organisation.details');

        
        Route::get('inbox', 'contactLists')->name('contact-lists');
        Route::get('orders', 'orders')->name('front-orders');
        Route::post('orders', 'orders')->name('front-orders-post');
        Route::get('order-details/{placeholder}', 'orderDetails')->name('front-order-details');

    });
});
Route::controller(\App\Http\Controllers\FrontAuthController::class)->group(function(){
    Route::middleware(['guest:front'])->group(function () {
        Route::get('login' , 'login')->name('front-login');
        Route::post('login' , 'handleLogin')->name('front-login-post');
        Route::get('register' , 'register')->name('front-register');
        Route::post('register' , 'registerVolunteer')->name('front-register-post');
        Route::get('front-verify-email/{id}/{timestamp}' , 'verifyEmail')->name('front-verify-email');
        Route::get('forgot-password' , 'forgotPassword')->name('front-forgot-password');
        Route::post('reset-password-email' , 'resetPasswordEmail')->name('reset.password.email');
        Route::get('send-activation-link/{id}' , 'sendActivationLink')->name('send-activation-link');
        Route::get('activate-account/{id}/{timestamp}' , 'activateAccount')->name('activate-account');
        
        Route::get('/reset-password/{token}','resetPassword')->name('resetPassword');
        Route::post('/reset-password','updatePassword')->name('updatePassword');

        Route::get('/import-data','importdata')->name('import-data');
    });
    Route::middleware(['auth:front'])->group(function () {
        Route::get('front-logout' , 'logout')->name('front-logout');
    });

});

/**
 * -------------------------------------------------------------------
 * Admin Panel Routes
 * -------------------------------------------------------------------
 */
Route::prefix(config('app.admin_prefix'))->group(function () {
    Route::get('/welcome', function () {return redirect()->route('dashboard');})->name('welcome');
    Route::middleware(['auth:web', 'shareview'])->group(function () {
        Route::get('/', function () {
            return redirect()->route('dashboard');
        });
        Route::view('site-settings' , 'pages.site-settings');
        Route::controller(\App\Http\Controllers\RoleController::class)->group(function () {
            Route::get('/roles', 'index')->name('index-role');
            Route::get('/manage-role/{role_placeholder?}', 'showForm')->name('show-role');
            Route::post('/manage-role', 'manage')->name('create-role');
            Route::get('/toggle-role-status/{role_placeholder}', 'toggleStatus')->name('toggle-role-status');
            Route::put('/manage-role/{role_placeholder}', 'manage')->name('update-role');
            Route::delete('/role/{role_placeholder}', 'delete')->name('delete-role');
        });
        Route::controller(\App\Http\Controllers\CustomerController::class)->group(function () {
            Route::get('/customers', 'index')->name('index-customer');
            Route::get('/manage-customer/{id?}', 'showForm')->name('show-customer');
            Route::post('/manage-customer', 'manage')->name('create-customer');
            Route::get('/toggle-customer-status/{id}', 'toggleStatus')->name('toggle-customer-status');
            Route::put('/manage-customer/{id}', 'manage')->name('update-customer');
            Route::delete('/customer/{id}', 'delete')->name('delete-customer');
            Route::post('/verify-customer-phone', 'verify_customer_phone')->name('verify_customer_phone');
            Route::post('/send-otp-customer-phone/{id}', 'send_otp_customer_phone')->name('send_otp_customer_phone');
        });
        Route::controller(\App\Http\Controllers\DashboardController::class)->group(function () {
            Route::get('/dashboard', 'index')->name('dashboard');
        });
        Route::controller(\App\Http\Controllers\ContactController::class)->group(function () {
            Route::get('/other-inquiry', 'index')->name('other-inquiry');
            Route::post('/other-inquiry', 'index')->name('other-inquiry');
        });
        Route::controller(\App\Http\Controllers\UserController::class)->group(function () {
            Route::get('/users', 'index')->name('index-user');
            Route::get('/manage-user/{user_placeholder?}', 'showForm')->name('show-user');
            Route::post('/manage-user', 'manage')->name('create-user');
            Route::put('/manage-user/{user_placeholder}', 'manage')->name('update-user');
            Route::delete('/user/{user_placeholder}', 'delete')->name('delete-user');
            Route::post('/update-user-password/{placeholder}', 'updatePassword')->name('manage-user-password');
            Route::post('/update-profile', 'updateProfile')->name('update-profile');
            Route::get('/update-profile', 'viewUpdatePassword')->name('view-update-profile');
            Route::post('/change-user-status/{id}', 'changeUserStatus')->name('manage-user-status');
            Route::get('/toggle-user-status/{user_placeholder}', 'toggleStatus')->name('toggle-user-status');
            Route::get('/settings', 'settings')->name('settings');
            Route::put('/update-settings', 'update_settings')->name('update_settings');
        });
        Route::controller(\App\Http\Controllers\VolunteerController::class)->group(function () {
            Route::get('/volunteers', 'index')->name('index-volunteers');
            Route::get('/manage-volunteers/{placeholder?}', 'showForm')->name('show-volunteer');
            Route::post('/manage-volunteers', 'manage')->name('create-volunteer');
            Route::put('/manage-volunteers/{placeholder}', 'manage')->name('update-volunteer');
            Route::post('/manage-volunteer-organisation', 'manageVolunteerOrganisation')->name('create-volunteer-organisation');
            Route::get('/toggle-volunteer-status/{placeholder}', 'toggleStatus')->name('toggle-volunteer-status');
            Route::delete('/volunteers/{placeholder}', 'delete')->name('delete-volunteer');
           Route::post('/update-volunteer-password/{placeholder}', 'updatePassword')->name('manage-volunteer-password');
            Route::post('/get-organizer-options' , 'getOrganizerOptions' )->name('get-organizer-options');
            Route::post('/get-volunteer-options' , 'getVolunteerOptions' )->name('get-volunteer-options');
            Route::post('/get-all-volunteer-options' , 'getAllVolunteerOptions' )->name('get-all-volunteer-options');
            
            Route::get('embedded-widget', 'embeddedWidget')->name('embedded-widget');
            Route::get('embedded-widget-report', 'embeddedWidgetReport')->name('embedded-widget-report');
            Route::post('embedded-widget-report', 'embeddedWidgetReport')->name('embedded-widget-report');
            Route::post('view-widget-detail/{id}', 'viewWidgetDetail')->name('view-widget-detail');
            Route::get('change-status-widget/{id}' , 'changeStatusWidget')->name('change-status-widget');
            Route::delete('/widget/{placeholder}', 'deleteWidget')->name('delete-widget');
        });
        Route::controller(\App\Http\Controllers\EventController::class)->group(function () {
            Route::get('/events', 'index')->name('index-events');
            Route::get('/manage-events/{placeholder?}', 'showForm')->name('show-event');
            Route::post('/get-focus-keywords', 'getFocusKeywords')->name('get-focus-keywords');
            Route::post('/get-event-meta-data-backend', 'getEventMetaDataBackend')->name('get-event-meta-data-backend');
            Route::post('/manage-events', 'manage')->name('create-event');
            Route::put('/manage-events/{placeholder}', 'manage')->name('update-event');
            Route::post('/update-event-permission/{placeholder}' , 'updateEventPermission')->name('update-event-permissions');
            Route::get('/clone-event/{placeholder}', 'cloneEvent')->name('clone-event');
            Route::get('/send-community-form/{ticket}', 'sendCommunityForm')->name('send-community-form');
            Route::get('/send-waiver-invitation/{ticket}', 'reSendWaiverInvitation')->name('send-waiver-invitation');
            Route::get('/send-thankyou-request-email/{placeholder}' , 'sendThankyouRequestEmail' )->name('send-thankyou-request-email');
            Route::get('/mark-checkin/{ticket}', 'markCheckin')->name('mark-checkin');
            Route::get('/toggle-event-status/{placeholder}', 'toggleStatus')->name('toggle-event-status');
            Route::get('/toggle-event-publish/{placeholder}', 'togglePublish')->name('toggle-event-publish');
            Route::delete('/events/{placeholder}', 'delete')->name('delete-event');
            //CO-HOST ROUTES
            Route::get('/manage-cohosts/{placeholder}' , 'manageCoHosts' )->name('manage-event-hosts');
            Route::post('/invite-cohosts/{placeholder}' , 'inviteCoHosts' )->name('events.cohosts.invite');
            //Route::get('/resend-invite-cohosts/{eventPlaceholder}/{volunteerPlaceholder}' , 'reInviteCoHosts' )->name('resend.cohost.invitation');
            Route::get('/resend-invite-cohosts/{placeholder}' , 'reInviteCoHosts' )->name('resend.cohost.invitation');
            Route::post('/delete-cohost/{placeholder}' , 'deleteCohost' )->name('delete.cohost');
            //post event ROUTES
            Route::get('/post-event-action/{placeholder}' , 'postEventAction' )->name('post-event-action');
            Route::post('/save-after-event-action/{placeholder}' , 'saveAfterEventAction' )->name('save-after-event-action');
            Route::post('/get-event-options' , 'getEventOptions' )->name('get-event-options');
        });
        Route::controller(\App\Http\Controllers\OrderController::class)->group(function () {
            Route::get('/orders', 'index')->name('index-orders');
            Route::get('/view-order-details/{placeholder}', 'viewOrderDetails')->name('view-order-details');
            Route::post('/manage-orders', 'manage')->name('create-order');
            Route::put('/manage-orders/{placeholder}', 'manage')->name('update-order');
            Route::get('/toggle-order-status/{placeholder}', 'toggleStatus')->name('toggle-order-status');
            Route::delete('/orders/{placeholder}', 'delete')->name('delete-order');
            Route::get('/sales-report', 'salesReport')->name('index-sales-report');
        });
        Route::controller(\App\Http\Controllers\GlobalTypeController::class)->group(function () {
            Route::prefix('global-types')->group(function () {
                Route::get('/{slug}', 'index')->name('index-global-type');
                Route::get('manage/{slug}/{placeholder?}', 'showForm')->name('show-global-type');
                Route::post('manage/{slug}', 'manage')->name('create-global-type');
                Route::post('manage/{slug}/{placeholder}', 'manage')->name('update-global-type');
                Route::delete('/{slug}/{placeholder}', 'delete')->name('delete-global-type');
                Route::get('/toggle-status/{slug}/{placeholder}', 'toggleStatus')->name('toggle-type-status');
            });
        });
        Route::controller(\App\Http\Controllers\MenuController::class)->group(function () {
            Route::get('/permissions', 'permissionPage')->name('show-permission');
            Route::post('/permissions', 'setPermissions')->name('set-permission');
        });
        Route::get('logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
        Route::view('guest', 'pages.guest-page');
    });
    Route::middleware('guest')->group(function () {
        Route::controller(\App\Http\Controllers\AuthController::class)->group(function () {
            Route::get('/login', 'loginView')->name('loginView');
            Route::post('/login', 'login')->name('login');
            Route::get('/forgot-password', 'forgotPasswordView')->name('forgotPassword');
            Route::post('/forgot-password', 'forgotPasswordEmail');
            Route::get('/reset-password/{id}', 'resetPasswordView');
            Route::post('/reset-password/{id}', 'resetPassword');
        });
    });
    /**common controller for website and backend */
    Route::controller(\App\Http\Controllers\CommonSystemController::class)->group(function () {
        Route::post('/get-pincode-options' , 'getPincodeOptions' )->name('get-pincode-options');
        Route::get('/import-city-state-zip' , 'importCityStateZip' )->name('import-city-state-zip');
    });
});
/**common controller for website and backend */
Route::controller(\App\Http\Controllers\CommonSystemController::class)->group(function () {
    Route::get('respond-cohost-invitation/{response}/{event}/{volunteer}' , 'RespondCohostInvitationLink')->name('response.cohost.invitaion')->where('response', 'accept|cancel');
    Route::post('get-ajax-slug/{type}', 'getAjaxSlug')->name('get-ajax-slug');
    // cron 
    Route::get('cron-event-rsvp-reminder', 'cronEventRSVPReminder')->name('cron-event-rsvp-reminder');
});
Route::controller(\App\Http\Controllers\FrontController::class)->group(function(){
    Route::get('{slug}' , 'viewEventPage')->name('view-event-details');
});