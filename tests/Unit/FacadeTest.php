<?php

declare(strict_types=1);

use CSoellinger\FonWebservices\BankDataTransmissionWs;
use CSoellinger\FonWebservices\DataboxDownloadWs;
use CSoellinger\FonWebservices\FileUploadWs;
use CSoellinger\FonWebservices\QueryDataTransmissionWs;
use CSoellinger\FonWebservices\SessionWs;
use CSoellinger\FonWebservices\VatIdCheckWs;
use CSoellinger\Laravel\FonWebservices\Facades\FonBankDataTransmission;
use CSoellinger\Laravel\FonWebservices\Facades\FonDataboxDownload;
use CSoellinger\Laravel\FonWebservices\Facades\FonFileUpload;
use CSoellinger\Laravel\FonWebservices\Facades\FonQueryDataTransmission;
use CSoellinger\Laravel\FonWebservices\Facades\FonSession;
use CSoellinger\Laravel\FonWebservices\Facades\FonVatIdCheck;

it('FonSession facade resolves to SessionWs', function (): void {
    expect(FonSession::getFacadeRoot())->toBeInstanceOf(SessionWs::class);
});

it('FonVatIdCheck facade resolves to VatIdCheckWs', function (): void {
    expect(FonVatIdCheck::getFacadeRoot())->toBeInstanceOf(VatIdCheckWs::class);
});

it('FonDataboxDownload facade resolves to DataboxDownloadWs', function (): void {
    expect(FonDataboxDownload::getFacadeRoot())->toBeInstanceOf(DataboxDownloadWs::class);
});

it('FonFileUpload facade resolves to FileUploadWs', function (): void {
    expect(FonFileUpload::getFacadeRoot())->toBeInstanceOf(FileUploadWs::class);
});

it('FonBankDataTransmission facade resolves to BankDataTransmissionWs', function (): void {
    expect(FonBankDataTransmission::getFacadeRoot())->toBeInstanceOf(BankDataTransmissionWs::class);
});

it('FonQueryDataTransmission facade resolves to QueryDataTransmissionWs', function (): void {
    expect(FonQueryDataTransmission::getFacadeRoot())->toBeInstanceOf(QueryDataTransmissionWs::class);
});
