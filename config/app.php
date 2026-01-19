<?php
use Illuminate\Support\Facades\Facade;
define('ADMIN',1);
// for frontend 
define('SEND_MAIL',true);
define('SUPPORT_EMAIL','outreach@volunteerCleanup.org');
define('PARTNER_LOGIN_SMS','Hi, Contact No. {mobile}, your OTP to login is {otp} ');
define('APPNAME','Volunteer Cleanup');
// email subjects
define('VOLUNTEER_REG_BY_BACKEND_SUB','Welcome, Eco Warrior ! Confirm Your Email to Dive In.');
define('VOLUNTEER_REG_BY_FRONTEND_SUB','Welcome, Eco Warrior ! Confirm Your Email to Dive In.');
define('FRONT_RESET_PASS_SUB','Reset Your Password');
define('COHOST_INVITE_SUB','You\'re Invited to Co-Host an Event on '.APPNAME);
define('COMM_SERVICE_FORM_SUB','Community Service Form');
define('EVENT_SUMMARY_MAIL_SUB','You Did It! 🌍 Here\'s What We Achieved Together');
define('CONTACT_ORG_MAIL_SUB','New Inquiry through VolunteerCleanup.Org website');
define('EVENT_PUBLISH_MAIL_SUB','New Event Published on VolunteerCleanup.org – ');
define('COMM_SERVICE_CONFIRMED_MAIL_SUB','New Inquiry through VolunteerCleanup.Org website');
define('COMM_SERVICE_REQUEST_MAIL_SUB','Community Service Form Request');
return [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */
    'name' => env('APP_NAME', 'Laravel'),
    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */
    'env' => env('APP_ENV', 'production'),
    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */
    'debug' => (bool) env('APP_DEBUG', false),
    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */
    'url' => env('APP_URL', 'https://google.com'),
    'asset_url' => env('ASSET_URL'),
    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */
    'timezone' => 'America/New_York',
    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */
    'locale' => 'en',
    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */
    'fallback_locale' => 'en',
    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */
    'faker_locale' => 'en_US',
    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */
    'maintenance' => [
        'driver' => 'file',
        // 'store'  => 'redis',
    ],
    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */
    'providers' => [
        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        /*
         * Package Service Providers...
         */
        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        /**Maatwebsite for excel reader */
        Maatwebsite\Excel\ExcelServiceProvider::class,
        Barryvdh\DomPDF\ServiceProvider::class,
    ],
    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */
    'aliases' => Facade::defaultAliases()->merge([
        // 'ExampleClass' => App\Example\ExampleClass::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'PDF' => Barryvdh\DomPDF\Facades\Pdf::class,
    ])->toArray(),
    /**Basic constants */
    'admin_prefix'              =>        'control-panel/',
    'front_prefix'              =>        '',
    'success'                   =>        'Great! Your Operation was Successful.',
    'failed'                    =>        'Opps! Seems like some Error Occured.',
    'unauthorized'              =>        'Opps! You\'re not Authorized to Perform this Action.',
    'servedata'                 =>        'Great! Your data is Ready.',
    'loginFailed'               =>        'Opps! We are unable to Authenticate you.',
    'dateFormat'                =>        'd M, Y',
    'dateTimeFormat'            =>        'd M, Y h:i A',
    'eventDateTimeFormat'       =>        'D, M d, Y h:i A',
    'timeFormat'                =>        'h:i A',
    'menu_permissions'          =>         [1 => 'View', 2 => 'Create', 3 => 'Update', 4 => 'Delete' , 5 => 'UpdateProfile'],
    'app_name'                  =>         env('APP_NAME', 'Laravel'),
    'accessibility'             =>         ['3' => "Both" , '1' => "Portal Only" , '2' =>  "App Only" ] ,
    'admin_role'                =>         '1',
    //'timezones'                 =>         ['eastern', 'central', 'mountain', 'pacific', 'peru', 'greenwich'],
    //'timezonescode'             =>         ['eastern'=>'EDT', 'central'=>'CDT', 'mountain'=>'MDT', 'pacific'=>'PDT', 'peru'=>'PET', 'greenwich'=>'GMT'],
    'timezones'                 =>         ['eastern', 'central', 'mountain', 'pacific'],
    'timezonescode'             =>         ['eastern'=>'EDT', 'central'=>'CDT', 'mountain'=>'MDT', 'pacific'=>'PDT'],
    'reoccurring'               =>         ['daily', 'weekly', 'monthly', 'yearly'],
    'wp_sync_url'               =>         "https://volunteercleanup.org/?fluentcrm=1&route=contact&hash=ccece292-78c5-4f56-ac14-b4ee4149838b",
    'frontsite'                 =>         "https://volunteercleanup.org/",
    'laravelurl'                 =>        "https://volunteercleanup.org/event/",
    'frontsiteold'              =>         "https://volunteercleanup.org/",
    'currency'                  =>         "$",
    'communityformbadge'        => [
        'pending'   => 'text-danger', 
        'requested' => 'text-primary', 
        'approved'  => 'text-success',
        'cancelled' => 'text-danger'
    ],
    /**cohost permissions */
    'cohost_permissions'        =>  [
            '1'  => 'Edit event details (title, description, date, location).',
            '2'  => 'View and export registration lists.',
            '3'  => 'Check in volunteers and mark attendance.',
            '4'  => 'Approve or deny community-service certificate requests.',
            '5'  => 'Send messages or updates to attendees.',
            '6'  => 'View reports and metrics.',
    ]
];
