<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    /*
    |--------------------------------------------------------------------------
    | Print Service Configuration
    |--------------------------------------------------------------------------
    */
    'print' => [
        'font_family' => env('PRINT_FONT_FAMILY', 'Roboto'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Semaphore SMS API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Semaphore SMS API integration.
    | Add these to your .env file:
    | SEMAPHORE_API_KEY=your_api_key_here
    | SEMAPHORE_SENDER_NAME=Click2Serve
    |
    */
    'semaphore' => [
        'api_key' => env('SEMAPHORE_API_KEY'),
        'sender_name' => env('SEMAPHORE_SENDER_NAME', 'Click2Serve'),
        'api_url' => env('SEMAPHORE_API_URL', 'https://api.semaphore.co/api/v4/messages'),
        'enabled' => env('SEMAPHORE_ENABLED', false),
        'test_number' => env('SEMAPHORE_TEST_NUMBER', '639219580125'),
        'use_test_number' => env('SEMAPHORE_USE_TEST_NUMBER', false),
        // SSL verification: set to false to disable SSL certificate verification
        // Useful for local development on Windows/XAMPP where CA bundle may not be available
        // Default: null (auto-disables in local/testing environments)
        'verify_ssl' => env('SEMAPHORE_VERIFY_SSL', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Node.js SMS service that handles Semaphore integration
    |
    */
    'sms_service' => [
        'url' => env('SMS_SERVICE_URL', 'http://localhost:3001'),
        'timeout' => env('SMS_SERVICE_TIMEOUT', 30),
        'enabled' => env('SMS_SERVICE_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Python SMS Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Python SMS service that handles Semaphore integration
    |
    */
    'python_sms' => [
        'python_path' => env('PYTHON_PATH', 'py'),
        'script_path' => env('PYTHON_SMS_SCRIPT_PATH', 'sms-service/send_sms.py'),
        'enabled' => env('PYTHON_SMS_ENABLED', true),
        'test_number' => env('PYTHON_SMS_TEST_NUMBER', '639219580125'),
        'use_test_number' => env('PYTHON_SMS_USE_TEST_NUMBER', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Chatbot Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Node.js chatbot service that handles Dialogflow integration
    |
    */
    'chatbot' => [
        'url' => env('CHATBOT_SERVICE_URL', 'http://localhost:5000'),
        'timeout' => env('CHATBOT_SERVICE_TIMEOUT', 30),
        'enabled' => env('CHATBOT_SERVICE_ENABLED', true),
    ],

];