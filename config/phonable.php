<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Identification Service
    |--------------------------------------------------------------------------
    |
    | This option controls the default identification service when using this
    | feature. You can swap this service on the fly if required.
    |
    | Supported services: "prelude", "vonage"
    |
    */
    'identification_service' => env('PHONE_IDENTIFICATION_SERVICE', 'prelude'),

    /*
    |--------------------------------------------------------------------------
    | Default Verification Service
    |--------------------------------------------------------------------------
    |
    | This option controls the default verification service when using this
    | feature. You can swap this service on the fly if required.
    |
    | Supported services: "prelude", "twilio", "vonage"
    |
    */
    'verification_service' => env('PHONE_VERIFICATION_SERVICE', 'prelude'),

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
            'driver' => 'prelude',
            'key' => env('PRELUDE_KEY'),
            'customer_uuid' => env('PRELUDE_CUSTOMER_UUID'),
        ],

        'twilio' => [
            'driver' => 'twilio',
            'account_id' => env('TWILIO_ACCOUNT_ID'),
            'auth_token' => env('TWILIO_AUTH_TOKEN'),
            'service_sid' => env('TWILIO_SERVICE_SID'),
        ],

        'vonage' => [
            'driver' => 'vonage',
            // See https://github.com/laravel/vonage-notification-channel for Vonage configuration
        ],

    ],

];
