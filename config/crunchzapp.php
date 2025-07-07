<?php

/**
 * CrunchzApp SDK Configuration
 *
 * This configuration file contains all the settings for the CrunchzApp SDK.
 * You can customize these values according to your application's requirements.
 *
 * @package CrunchzApp
 * @version 1.0.0
 */

return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    */

    /**
     * Request timeout in seconds
     *
     * @var int
     */
    'timeout' => (int) env('CRUNCHZAPP_TIMEOUT', 30),

    /**
     * API Base URL
     *
     * @var string
     */
    'api_url' => env('CRUNCHZAPP_API_URL', 'https://api.crunchz.app/api'),

    /*
    |--------------------------------------------------------------------------
    | Authentication Token
    |
    | You can use the token for each channel or using global token to randomize
    | the channel in your account to be used.
    |--------------------------------------------------------------------------
    */

    /**
     * CrunchzApp API Token
     *
     * @var string|null
     */
    'token' => env('CRUNCHZAPP_TOKEN'),
    /*
    |--------------------------------------------------------------------------
    | One Time Password Configuration
    |--------------------------------------------------------------------------
    |
    | Configure OTP settings for both code-based and link-based verification.
    | These settings control how OTPs are generated, validated, and handled.
    */

    'otp' => [
        /*
        |--------------------------------------------------------------------------
        | OTP Code Configuration
        |--------------------------------------------------------------------------
        |
        | Settings for code-based OTP verification.
        */

        'code' => [
            /**
             * Length of the OTP code
             *
             * @var int
             */
            'length' => (int) env('CRUNCHZAPP_OTP_CODE_LENGTH', 4),

            /**
             * Include letters in the OTP code
             *
             * @var bool
             */
            'useLetter' => (bool) env('CRUNCHZAPP_OTP_USE_LETTER', true),

            /**
             * Include numbers in the OTP code
             *
             * @var bool
             */
            'useNumber' => (bool) env('CRUNCHZAPP_OTP_USE_NUMBER', true),

            /**
             * Use all capital letters in the OTP code
             *
             * @var bool
             */
            'allCapital' => (bool) env('CRUNCHZAPP_OTP_ALL_CAPITAL', true),

            /**
             * Application name for OTP identification
             *
             * @var string
             */
            'name' => env('CRUNCHZAPP_OTP_NAME', env('APP_NAME', 'CrunchzApp')),

            /**
             * OTP expiration time in seconds (default: 30 minutes)
             *
             * @var int
             */
            'expires' => (int) env('CRUNCHZAPP_OTP_CODE_EXPIRES', 1800),
        ],
        /*
        |--------------------------------------------------------------------------
        | OTP Link Configuration
        |--------------------------------------------------------------------------
        |
        | Settings for link-based OTP verification.
        */

        'link' => [
            /**
             * OTP link expiration time in seconds (default: 30 minutes)
             *
             * @var int
             */
            'expires' => (int) env('CRUNCHZAPP_OTP_LINK_EXPIRES', 1800),

            /**
             * User prompt message for OTP request
             *
             * @var string
             */
            'prompt' => env(
                'CRUNCHZAPP_OTP_PROMPT',
                'Give me a code to login at ' . env('APP_NAME', 'CrunchzApp')
            ),

            /**
             * Application name for OTP identification
             *
             * @var string
             */
            'name' => env('CRUNCHZAPP_OTP_LINK_NAME', env('APP_NAME', 'CrunchzApp')),

            /**
             * Response messages configuration
             */
            'respond' => [
                /**
                 * Success response message
                 *
                 * @var string
                 */
                'success' => env(
                    'CRUNCHZAPP_OTP_SUCCESS_MESSAGE',
                    'Here is your code, make sure it still safe and secret!'
                ),

                /**
                 * Failed response message
                 *
                 * @var string
                 */
                'failed' => env(
                    'CRUNCHZAPP_OTP_FAILED_MESSAGE',
                    'Your number or code was not valid'
                ),

                /**
                 * Expired response message
                 *
                 * @var string
                 */
                'expired' => env(
                    'CRUNCHZAPP_OTP_EXPIRED_MESSAGE',
                    'This link was expired. Please request a new link'
                ),
            ],

            /**
             * Callback URLs configuration
             */
            'callback' => [
                /**
                 * Success callback URL
                 *
                 * @var string
                 */
                'success' => env(
                    'CRUNCHZAPP_OTP_SUCCESS_CALLBACK',
                    'https://www.domain.com/validate/success'
                ),

                /**
                 * Failed callback URL
                 *
                 * @var string
                 */
                'failed' => env(
                    'CRUNCHZAPP_OTP_FAILED_CALLBACK',
                    'https://www.domain.com/validate/failed'
                ),
            ],
        ],
    ]
];
