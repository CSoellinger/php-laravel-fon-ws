<?php

declare(strict_types=1);

use CSoellinger\FonWebservices\BankDataTransmissionWs;
use CSoellinger\FonWebservices\DataboxDownloadWs;
use CSoellinger\FonWebservices\Enum\VatIdCheckLevel;
use CSoellinger\FonWebservices\FileUploadWs;
use CSoellinger\FonWebservices\Model\VatIdCheckValidLevelOne;
use CSoellinger\FonWebservices\QueryDataTransmissionWs;
use CSoellinger\FonWebservices\SessionWs;
use CSoellinger\FonWebservices\VatIdCheckWs;
use CSoellinger\Laravel\FonWebservices\Facades\FonBankDataTransmission;
use CSoellinger\Laravel\FonWebservices\Facades\FonDataboxDownload;
use CSoellinger\Laravel\FonWebservices\Facades\FonFileUpload;
use CSoellinger\Laravel\FonWebservices\Facades\FonQueryDataTransmission;
use CSoellinger\Laravel\FonWebservices\Facades\FonSession;
use CSoellinger\Laravel\FonWebservices\Facades\FonVatIdCheck;

/**
 * These tests verify that the package can be used in multiple ways:
 * - Dependency Injection (for services, controllers, jobs)
 * - Facades (for quick access)
 * - Direct instantiation via app()
 *
 * This proves it's a GENERIC Laravel package, not just CLI commands.
 */
describe('Service Usage Patterns', function (): void {
    it('can inject VatIdCheckWs via dependency injection', function (): void {
        $result = new VatIdCheckValidLevelOne;
        $result->valid = true;

        $mock = Mockery::mock(VatIdCheckWs::class);
        $mock->shouldReceive('check')->once()->andReturn($result);
        $this->app->instance(VatIdCheckWs::class, $mock);

        $service = new class(app(VatIdCheckWs::class))
        {
            public function __construct(
                private VatIdCheckWs $vatIdCheck
            ) {}

            public function checkVat(string $vatId): bool
            {
                $result = $this->vatIdCheck->check($vatId, VatIdCheckLevel::SimpleCheck);

                return $result->valid;
            }
        };

        $isValid = $service->checkVat('ATU12345678');

        expect($isValid)->toBe(true);
    });

    it('can use FonVatIdCheck facade', function (): void {
        $result = new VatIdCheckValidLevelOne;
        $result->valid = true;

        $mock = Mockery::mock(VatIdCheckWs::class);
        $mock->shouldReceive('check')->once()->andReturn($result);
        $this->app->instance(VatIdCheckWs::class, $mock);

        $checkResult = FonVatIdCheck::check('ATU12345678', VatIdCheckLevel::SimpleCheck);

        expect($checkResult->valid)->toBe(true);
    });

    it('can resolve VatIdCheckWs from container', function (): void {
        $vatIdCheck = app(VatIdCheckWs::class);

        expect($vatIdCheck)->toBeInstanceOf(VatIdCheckWs::class);
    });

    it('resolves all web services as singletons', function (): void {
        $session1 = app(SessionWs::class);
        $session2 = app(SessionWs::class);

        expect($session1)->toBe($session2); // Same instance (singleton)

        $vatCheck1 = app(VatIdCheckWs::class);
        $vatCheck2 = app(VatIdCheckWs::class);

        expect($vatCheck1)->toBe($vatCheck2); // Same instance (singleton)
    });

    it('provides all service facades', function (): void {
        expect(FonSession::getFacadeRoot())->toBeInstanceOf(SessionWs::class)
            ->and(FonVatIdCheck::getFacadeRoot())->toBeInstanceOf(VatIdCheckWs::class)
            ->and(FonDataboxDownload::getFacadeRoot())->toBeInstanceOf(DataboxDownloadWs::class)
            ->and(FonFileUpload::getFacadeRoot())->toBeInstanceOf(FileUploadWs::class)
            ->and(FonBankDataTransmission::getFacadeRoot())->toBeInstanceOf(BankDataTransmissionWs::class)
            ->and(FonQueryDataTransmission::getFacadeRoot())->toBeInstanceOf(QueryDataTransmissionWs::class);
    });

    it('can be used in a simulated controller action', function (): void {
        $result = new \CSoellinger\FonWebservices\Model\VatIdCheckValidLevelTwo;
        $result->valid = true;
        $result->name = 'Test Company GmbH';
        $result->address = 'Vienna, Austria';

        $mock = Mockery::mock(VatIdCheckWs::class);
        $mock->shouldReceive('check')->once()->andReturn($result);
        $this->app->instance(VatIdCheckWs::class, $mock);

        $controller = new class(app(VatIdCheckWs::class))
        {
            public function __construct(
                private VatIdCheckWs $vatIdCheck
            ) {}

            public function validateVat(string $vatId): array
            {
                $result = $this->vatIdCheck->check($vatId, VatIdCheckLevel::FullCheck);

                if ($result instanceof \CSoellinger\FonWebservices\Model\VatIdCheckValidLevelTwo) {
                    return [
                        'valid' => true,
                        'name' => $result->name,
                        'address' => $result->address,
                    ];
                }

                return ['valid' => $result->valid];
            }
        };

        $response = $controller->validateVat('ATU12345678');

        expect($response)->toBe([
            'valid' => true,
            'name' => 'Test Company GmbH',
            'address' => 'Vienna, Austria',
        ]);
    });
});
