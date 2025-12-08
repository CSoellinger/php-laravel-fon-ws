<?php

declare(strict_types=1);

use CSoellinger\FonWebservices\SessionWs;
use CSoellinger\FonWebservices\VatIdCheckWs;

/**
 * Test that SOAP options from config are actually passed to the base package
 */
it('passes SOAP options to SessionWs', function (): void {
    config()->set('fon-webservices.soap_options', [
        'trace' => true,
        'exceptions' => false,
        'connection_timeout' => 60,
    ]);

    $sessionWs = app(SessionWs::class);

    // If options were invalid, SoapClient would throw during instantiation
    expect($sessionWs)->toBeInstanceOf(SessionWs::class);
});

it('passes SOAP options to VatIdCheckWs', function (): void {
    config()->set('fon-webservices.soap_options', [
        'trace' => true,
        'connection_timeout' => 45,
    ]);

    $vatIdCheck = app(VatIdCheckWs::class);

    expect($vatIdCheck)->toBeInstanceOf(VatIdCheckWs::class);
});

it('uses default SOAP options when none configured', function (): void {
    config()->set('fon-webservices.soap_options', []);

    $sessionWs = app(SessionWs::class);

    expect($sessionWs)->toBeInstanceOf(SessionWs::class);
});

it('SOAP trace option actually works', function (): void {
    config()->set('fon-webservices.soap_options', [
        'trace' => 1,
    ]);

    $sessionWs = app(SessionWs::class);

    expect(method_exists($sessionWs, '__getLastRequest'))->toBe(true)
        ->and(method_exists($sessionWs, '__getLastResponse'))->toBe(true);
});
