<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Identification Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default identification driver when using this
    | feature. You can swap this driver on the fly if required.
    |
    | Supported drivers: "prelude", "vonage"
    |
    */
    'identification' => [
        'default' => env('PHONE_IDENTIFICATION_SERVICE', 'prelude'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Verification Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default verification driver when using this
    | feature. You can swap this driver on the fly if required.
    |
    | Supported drivers: "prelude", "twilio", "vonage"
    |
    */
    'verification' => [
        'default' => env('PHONE_VERIFICATION_SERVICE', 'prelude'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Phone Services
    |--------------------------------------------------------------------------
    |
    | Here you can configure the required credentials and additional metadata
    | for each supported external phone service.
    |
    | Supported drivers: "prelude", "twilio", "vonage"
    |
    */
    'services' => [

        'prelude' => [
            'key' => env('PRELUDE_KEY'),
            'customer_uuid' => env('PRELUDE_CUSTOMER_UUID'),
        ],

        'twilio' => [
            'account_id' => env('TWILIO_ACCOUNT_ID'),
            'auth_token' => env('TWILIO_AUTH_TOKEN'),
            'service_sid' => env('TWILIO_SERVICE_SID'),
        ],

        'vonage' => [
            // See https://github.com/laravel/vonage-notification-channel for Vonage configuration
        ],

    ],

];
