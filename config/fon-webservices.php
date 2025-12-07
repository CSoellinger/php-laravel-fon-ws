<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | FinanzOnline Credentials
    |--------------------------------------------------------------------------
    |
    | Your FinanzOnline web service credentials. These should be stored
    | in your .env file for security purposes. Never commit credentials
    | to version control.
    |
    | - te_id: Teilnehmer ID
    | - te_uid: Teilnehmer UID
    | - ben_id: Benutzer ID
    | - ben_pin: Benutzer PIN
    |
    */

    'credentials' => [
        'te_id' => env('FON_T_ID'),
        'te_uid' => env('FON_T_UID'),
        'ben_id' => env('FON_BEN_ID'),
        'ben_pin' => env('FON_BEN_PIN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SOAP Client Options
    |--------------------------------------------------------------------------
    |
    | Additional options to pass to the PHP SOAP client. These are passed
    | directly to the SoapClient constructor. See PHP documentation for
    | available options.
    |
    | Common options:
    | - trace: Enable request/response tracing (useful for debugging)
    | - exceptions: Throw SoapFault exceptions on SOAP errors
    | - connection_timeout: Timeout for connection in seconds
    | - cache_wsdl: WSDL cache mode (WSDL_CACHE_NONE, WSDL_CACHE_DISK, etc.)
    |
    */

    'soap_options' => [
        'trace' => env('FON_SOAP_TRACE', false),
        'exceptions' => env('FON_SOAP_EXCEPTIONS', true),
        'connection_timeout' => env('FON_SOAP_TIMEOUT', 30),
        'cache_wsdl' => env('FON_SOAP_CACHE_WSDL', WSDL_CACHE_DISK),
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Bindings
    |--------------------------------------------------------------------------
    |
    | Configure which services should be bound to the container. You can
    | disable services you don't use to reduce memory footprint.
    |
    */

    'services' => [
        'session' => true,
        'vat_id_check' => true,
        'databox_download' => true,
        'file_upload' => true,
        'bank_data_transmission' => true,
        'query_data_transmission' => true,
    ],

];
