<?php

declare(strict_types=1);

use CSoellinger\FonWebservices\Authentication\FonCredential;
use CSoellinger\FonWebservices\BankDataTransmissionWs;
use CSoellinger\FonWebservices\DataboxDownloadWs;
use CSoellinger\FonWebservices\FileUploadWs;
use CSoellinger\FonWebservices\QueryDataTransmissionWs;
use CSoellinger\FonWebservices\SessionWs;
use CSoellinger\FonWebservices\VatIdCheckWs;

it('registers FonCredential as a singleton', function (): void {
    $credential1 = app(FonCredential::class);
    $credential2 = app(FonCredential::class);

    expect($credential1)->toBe($credential2);
});

it('creates FonCredential with correct configuration', function (): void {
    $credential = app(FonCredential::class);

    // Check that credentials are loaded from config (not null/empty)
    expect($credential->teId)->toBeString()->not->toBeEmpty()
        ->and($credential->teUid)->toBeString()->not->toBeEmpty()
        ->and($credential->benId)->toBeString()->not->toBeEmpty()
        ->and($credential->benPin)->toBeString()->not->toBeEmpty();

    // Verify they match the configured values
    expect($credential->teId)->toBe(config('fon-webservices.credentials.te_id'))
        ->and($credential->teUid)->toBe(config('fon-webservices.credentials.te_uid'))
        ->and($credential->benId)->toBe(config('fon-webservices.credentials.ben_id'))
        ->and($credential->benPin)->toBe(config('fon-webservices.credentials.ben_pin'));
});

it('registers SessionWs as a singleton', function (): void {
    $session1 = app(SessionWs::class);
    $session2 = app(SessionWs::class);

    expect($session1)->toBe($session2);
});

it('registers VatIdCheckWs as a singleton', function (): void {
    $service1 = app(VatIdCheckWs::class);
    $service2 = app(VatIdCheckWs::class);

    expect($service1)->toBe($service2);
});

it('registers DataboxDownloadWs as a singleton', function (): void {
    $service1 = app(DataboxDownloadWs::class);
    $service2 = app(DataboxDownloadWs::class);

    expect($service1)->toBe($service2);
});

it('registers FileUploadWs as a singleton', function (): void {
    $service1 = app(FileUploadWs::class);
    $service2 = app(FileUploadWs::class);

    expect($service1)->toBe($service2);
});

it('registers BankDataTransmissionWs as a singleton', function (): void {
    $service1 = app(BankDataTransmissionWs::class);
    $service2 = app(BankDataTransmissionWs::class);

    expect($service1)->toBe($service2);
});

it('registers QueryDataTransmissionWs as a singleton', function (): void {
    $service1 = app(QueryDataTransmissionWs::class);
    $service2 = app(QueryDataTransmissionWs::class);

    expect($service1)->toBe($service2);
});

it('publishes config file', function (): void {
    $this->artisan('vendor:publish', [
        '--provider' => 'CSoellinger\\Laravel\\FonWebservices\\FonWebservicesServiceProvider',
        '--tag' => 'fon-webservices-config',
    ])->assertSuccessful();
});
