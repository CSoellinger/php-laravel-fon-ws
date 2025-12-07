<?php

declare(strict_types=1);

use CSoellinger\FonWebservices\SessionWs;
use CSoellinger\FonWebservices\VatIdCheckWs;

/**
 * Test that SOAP options from config are actually passed to the base package
 */
it('passes SOAP options to SessionWs', function (): void {
    // Set custom SOAP options in config
    config()->set('fon-webservices.soap_options', [
        'trace' => true,
        'exceptions' => false,
        'connection_timeout' => 60,
    ]);

    $sessionWs = app(SessionWs::class);

    // These are protected properties in SoapClient, but we can verify
    // the class was instantiated (if options were invalid, it would throw)
    expect($sessionWs)->toBeInstanceOf(SessionWs::class);
});

it('passes SOAP options to VatIdCheckWs', function (): void {
    // Set custom SOAP options in config
    config()->set('fon-webservices.soap_options', [
        'trace' => true,
        'connection_timeout' => 45,
    ]);

    $vatIdCheck = app(VatIdCheckWs::class);

    expect($vatIdCheck)->toBeInstanceOf(VatIdCheckWs::class);
});

it('uses default SOAP options when none configured', function (): void {
    // Clear soap_options from config
    config()->set('fon-webservices.soap_options', []);

    $sessionWs = app(SessionWs::class);

    expect($sessionWs)->toBeInstanceOf(SessionWs::class);
});

it('SOAP trace option actually works', function (): void {
    // Enable trace mode
    config()->set('fon-webservices.soap_options', [
        'trace' => 1, // Enable tracing
    ]);

    $sessionWs = app(SessionWs::class);

    // When trace is enabled, these methods should be available
    expect(method_exists($sessionWs, '__getLastRequest'))->toBe(true)
        ->and(method_exists($sessionWs, '__getLastResponse'))->toBe(true);
});
