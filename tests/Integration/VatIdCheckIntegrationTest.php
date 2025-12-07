<?php

declare(strict_types=1);

use CSoellinger\FonWebservices\Enum\VatIdCheckLevel;
use CSoellinger\FonWebservices\Model\VatIdCheckInvalid;
use CSoellinger\FonWebservices\Model\VatIdCheckValidLevelOne;
use CSoellinger\FonWebservices\Model\VatIdCheckValidLevelTwo;
use CSoellinger\FonWebservices\VatIdCheckWs;

/**
 * Integration Tests - These tests make REAL API calls to FinanzOnline
 *
 * To run these tests, add your real FON credentials to .env:
 *   FON_T_ID, FON_T_UID, FON_BEN_ID, FON_BEN_PIN
 *
 * Then run: ./vendor/bin/pest --group=integration
 */
describe('VatIdCheck Integration Tests', function (): void {
    beforeEach(function (): void {
        // Skip if using dummy/placeholder credentials
        $teId = config('fon-webservices.credentials.te_id');
        $hasRealCredentials = $teId
            && $teId !== 'TEST12345'
            && $teId !== 'your_teilnehmer_id';

        if (! $hasRealCredentials) {
            $this->markTestSkipped(
                'Real FON credentials not configured in .env file. '.
                'Set FON_T_ID, FON_T_UID, FON_BEN_ID, FON_BEN_PIN to run integration tests.'
            );
        }
    });

    it('checks a known invalid VAT ID', function (): void {
        $vatIdCheck = app(VatIdCheckWs::class);

        // ATU12345678 is a test/invalid VAT ID
        $result = $vatIdCheck->check('ATU12345678', VatIdCheckLevel::SimpleCheck);

        expect($result)->toBeInstanceOf(VatIdCheckInvalid::class)
            ->and($result->valid)->toBe(false)
            ->and($result->code)->toBeInt();
    })->group('integration', 'slow');

    it('checks a known valid VAT ID at level 1', function (): void {
        $vatIdCheck = app(VatIdCheckWs::class);

        // ATU36975500 is McDonald's Franchise GmbH (known valid VAT ID from examples)
        $result = $vatIdCheck->check('ATU36975500', VatIdCheckLevel::SimpleCheck);

        expect($result)->toBeInstanceOf(VatIdCheckValidLevelOne::class)
            ->and($result->valid)->toBe(true);
    })->group('integration', 'slow');

    it('checks a known valid VAT ID at level 2 with company details', function (): void {
        $vatIdCheck = app(VatIdCheckWs::class);

        // ATU36975500 is McDonald's Franchise GmbH
        $result = $vatIdCheck->check('ATU36975500', VatIdCheckLevel::FullCheck);

        expect($result)->toBeInstanceOf(VatIdCheckValidLevelTwo::class)
            ->and($result->valid)->toBe(true)
            ->and($result->name)->toBeString()->not->toBeEmpty()
            ->and($result->address)->toBeString()->not->toBeEmpty();
    })->group('integration', 'slow');

    it('checks your specific example - ATU7231217X should be invalid', function (): void {
        $vatIdCheck = app(VatIdCheckWs::class);

        $result = $vatIdCheck->check('ATU7231217X', VatIdCheckLevel::SimpleCheck);

        expect($result->valid)->toBe(false)
            ->and($result)->toBeInstanceOf(VatIdCheckInvalid::class);
    })->group('integration', 'slow');
})->group('integration');
