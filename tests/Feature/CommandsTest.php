<?php

declare(strict_types=1);

use CSoellinger\FonWebservices\DataboxDownloadWs;
use CSoellinger\FonWebservices\Enum\VatIdCheckLevel;
use CSoellinger\FonWebservices\Model\DataboxDownloadListItem;
use CSoellinger\FonWebservices\Model\VatIdCheckInvalid;
use CSoellinger\FonWebservices\Model\VatIdCheckValidLevelOne;
use CSoellinger\FonWebservices\Model\VatIdCheckValidLevelTwo;
use CSoellinger\FonWebservices\VatIdCheckWs;

describe('fon:check-vat command', function (): void {
    it('validates level option and rejects invalid values', function (): void {
        $this->artisan('fon:check-vat', [
            'vat-id' => 'ATU12345678',
            '--level' => 3,
        ])
            ->expectsOutputToContain('Level must be 1 or 2')
            ->assertFailed();
    });

    it('checks valid VAT ID with level 1 and displays result', function (): void {
        $result = new VatIdCheckValidLevelOne;
        $result->valid = true;

        $mock = Mockery::mock(VatIdCheckWs::class);
        $mock->shouldReceive('check')
            ->once()
            ->with('ATU12345678', VatIdCheckLevel::SimpleCheck)
            ->andReturn($result);

        $this->app->instance(VatIdCheckWs::class, $mock);

        $this->artisan('fon:check-vat', [
            'vat-id' => 'ATU12345678',
            '--level' => 1,
        ])
            ->expectsOutputToContain('Checking VAT ID: ATU12345678')
            ->expectsOutputToContain('✅ VAT ID is VALID')
            ->expectsOutputToContain('ATU12345678')
            ->assertSuccessful();
    });

    it('checks valid VAT ID with level 2 and displays name and address', function (): void {
        $result = new VatIdCheckValidLevelTwo;
        $result->valid = true;
        $result->name = 'Test Company GmbH';
        $result->address = 'Teststraße 123, 1010 Wien';

        $mock = Mockery::mock(VatIdCheckWs::class);
        $mock->shouldReceive('check')
            ->once()
            ->with('ATU12345678', VatIdCheckLevel::FullCheck)
            ->andReturn($result);

        $this->app->instance(VatIdCheckWs::class, $mock);

        $this->artisan('fon:check-vat', [
            'vat-id' => 'ATU12345678',
            '--level' => 2,
        ])
            ->expectsOutputToContain('✅ VAT ID is VALID')
            ->expectsOutputToContain('Test Company GmbH')
            ->expectsOutputToContain('Teststraße 123, 1010 Wien')
            ->assertSuccessful();
    });

    it('displays invalid VAT ID result with error details', function (): void {
        $result = new VatIdCheckInvalid;
        $result->code = 1;
        $result->valid = false;
        $result->msg = 'VAT ID not found';

        $mock = Mockery::mock(VatIdCheckWs::class);
        $mock->shouldReceive('check')
            ->once()
            ->with('ATU99999999', VatIdCheckLevel::SimpleCheck)
            ->andReturn($result);

        $this->app->instance(VatIdCheckWs::class, $mock);

        $this->artisan('fon:check-vat', [
            'vat-id' => 'ATU99999999',
        ])
            ->expectsOutputToContain('❌ VAT ID is INVALID')
            ->expectsOutputToContain('ATU99999999')
            ->expectsOutputToContain('VAT ID not found')
            ->assertSuccessful();
    });

    it('outputs valid level 1 result as JSON', function (): void {
        $result = new VatIdCheckValidLevelOne;
        $result->valid = true;

        $mock = Mockery::mock(VatIdCheckWs::class);
        $mock->shouldReceive('check')
            ->once()
            ->andReturn($result);

        $this->app->instance(VatIdCheckWs::class, $mock);

        $exitCode = \Illuminate\Support\Facades\Artisan::call('fon:check-vat', [
            'vat-id' => 'ATU12345678',
            '--json' => true,
        ]);

        $output = trim(\Illuminate\Support\Facades\Artisan::output());
        $decoded = json_decode($output, true);

        expect($exitCode)->toBe(0)
            ->and($decoded)->toBeArray()
            ->and($decoded['vat_id'])->toBe('ATU12345678')
            ->and($decoded['valid'])->toBe(true);
    });

    it('outputs valid level 2 result as JSON', function (): void {
        $result = new VatIdCheckValidLevelTwo;
        $result->valid = true;
        $result->name = 'Test Company GmbH';
        $result->address = 'Teststraße 123, 1010 Wien';

        $mock = Mockery::mock(VatIdCheckWs::class);
        $mock->shouldReceive('check')
            ->once()
            ->andReturn($result);

        $this->app->instance(VatIdCheckWs::class, $mock);

        $exitCode = \Illuminate\Support\Facades\Artisan::call('fon:check-vat', [
            'vat-id' => 'ATU12345678',
            '--level' => 2,
            '--json' => true,
        ]);

        $output = trim(\Illuminate\Support\Facades\Artisan::output());
        $decoded = json_decode($output, true);

        expect($exitCode)->toBe(0)
            ->and($decoded)->toBeArray()
            ->and($decoded['vat_id'])->toBe('ATU12345678')
            ->and($decoded['valid'])->toBe(true)
            ->and($decoded['name'])->toBe('Test Company GmbH')
            ->and($decoded['address'])->toBe('Teststraße 123, 1010 Wien');
    });

    it('outputs invalid result as JSON', function (): void {
        $result = new VatIdCheckInvalid;
        $result->code = 1;
        $result->valid = false;
        $result->msg = 'VAT ID not found';

        $mock = Mockery::mock(VatIdCheckWs::class);
        $mock->shouldReceive('check')
            ->once()
            ->andReturn($result);

        $this->app->instance(VatIdCheckWs::class, $mock);

        $exitCode = \Illuminate\Support\Facades\Artisan::call('fon:check-vat', [
            'vat-id' => 'ATU99999999',
            '--json' => true,
        ]);

        $output = trim(\Illuminate\Support\Facades\Artisan::output());
        $decoded = json_decode($output, true);

        expect($exitCode)->toBe(0)
            ->and($decoded)->toBeArray()
            ->and($decoded['vat_id'])->toBe('ATU99999999')
            ->and($decoded['valid'])->toBe(false)
            ->and($decoded['error_code'])->toBe(1)
            ->and($decoded['error_message'])->toBe('VAT ID not found');
    });

    it('handles service exceptions gracefully', function (): void {
        $mock = Mockery::mock(VatIdCheckWs::class);
        $mock->shouldReceive('check')
            ->once()
            ->andThrow(new Exception('SOAP connection failed'));

        $this->app->instance(VatIdCheckWs::class, $mock);

        $this->artisan('fon:check-vat', [
            'vat-id' => 'ATU12345678',
        ])
            ->expectsOutputToContain('SOAP connection failed')
            ->assertFailed();
    });

    it('handles service exceptions gracefully with JSON output', function (): void {
        $mock = Mockery::mock(VatIdCheckWs::class);
        $mock->shouldReceive('check')
            ->once()
            ->andThrow(new Exception('SOAP connection failed'));

        $this->app->instance(VatIdCheckWs::class, $mock);

        $exitCode = \Illuminate\Support\Facades\Artisan::call('fon:check-vat', [
            'vat-id' => 'ATU12345678',
            '--json' => true,
        ]);

        $output = trim(\Illuminate\Support\Facades\Artisan::output());
        $decoded = json_decode($output, true);

        expect($exitCode)->toBe(1)
            ->and($decoded)->toBeArray()
            ->and($decoded['error'])->toBe('SOAP connection failed');
    });
});

describe('fon:list-databox command', function (): void {
    it('fetches and displays databox items', function (): void {
        $item1 = new DataboxDownloadListItem;
        $item1->applkey = 'KEY123';
        $item1->filebez = 'Test Invoice';
        $item1->erltyp = 'INVOICE';
        $item1->ts_zust = new DateTime('2024-01-15 10:30:00');

        $item2 = new DataboxDownloadListItem;
        $item2->applkey = 'KEY456';
        $item2->filebez = 'Tax Notice';
        $item2->erltyp = 'NOTICE';
        $item2->ts_zust = new DateTime('2024-01-20 14:45:00');

        $mock = Mockery::mock(DataboxDownloadWs::class);
        $mock->shouldReceive('get')
            ->once()
            ->with('', null, null)
            ->andReturn([$item1, $item2]);

        $this->app->instance(DataboxDownloadWs::class, $mock);

        $exitCode = \Illuminate\Support\Facades\Artisan::call('fon:list-databox');
        $output = \Illuminate\Support\Facades\Artisan::output();

        expect($exitCode)->toBe(0)
            ->and($output)->toContain('Fetching databox items')
            ->and($output)->toContain('Found 2 item(s)')
            ->and($output)->toContain('KEY123')
            ->and($output)->toContain('Test Invoice')
            ->and($output)->toContain('KEY456')
            ->and($output)->toContain('Tax Notice');
    });

    it('displays warning when no items found', function (): void {
        $mock = Mockery::mock(DataboxDownloadWs::class);
        $mock->shouldReceive('get')
            ->once()
            ->andReturn([]);

        $this->app->instance(DataboxDownloadWs::class, $mock);

        $this->artisan('fon:list-databox')
            ->expectsOutputToContain('No items found in databox')
            ->assertSuccessful();
    });

    it('handles service exceptions gracefully', function (): void {
        $mock = Mockery::mock(DataboxDownloadWs::class);
        $mock->shouldReceive('get')
            ->once()
            ->andThrow(new Exception('Connection timeout'));

        $this->app->instance(DataboxDownloadWs::class, $mock);

        $this->artisan('fon:list-databox')
            ->expectsOutputToContain('Connection timeout')
            ->assertFailed();
    });

    it('handles service exceptions gracefully with JSON output', function (): void {
        $mock = Mockery::mock(DataboxDownloadWs::class);
        $mock->shouldReceive('get')
            ->once()
            ->andThrow(new Exception('Connection timeout'));

        $this->app->instance(DataboxDownloadWs::class, $mock);

        $exitCode = \Illuminate\Support\Facades\Artisan::call('fon:list-databox', [
            '--json' => true,
        ]);

        $output = trim(\Illuminate\Support\Facades\Artisan::output());
        $decoded = json_decode($output, true);

        expect($exitCode)->toBe(1)
            ->and($decoded)->toBeArray()
            ->and($decoded['error'])->toBe('Connection timeout');
    });

    it('outputs databox items as JSON', function (): void {
        $item1 = new DataboxDownloadListItem;
        $item1->applkey = 'KEY123';
        $item1->filebez = 'Test Invoice';
        $item1->erltyp = 'INVOICE';
        $item1->ts_zust = new DateTime('2024-01-15 10:30:00');

        $item2 = new DataboxDownloadListItem;
        $item2->applkey = 'KEY456';
        $item2->filebez = 'Tax Notice';
        $item2->erltyp = 'NOTICE';
        $item2->ts_zust = new DateTime('2024-01-20 14:45:00');

        $mock = Mockery::mock(DataboxDownloadWs::class);
        $mock->shouldReceive('get')
            ->once()
            ->andReturn([$item1, $item2]);

        $this->app->instance(DataboxDownloadWs::class, $mock);

        $exitCode = \Illuminate\Support\Facades\Artisan::call('fon:list-databox', [
            '--json' => true,
        ]);

        $output = trim(\Illuminate\Support\Facades\Artisan::output());
        $decoded = json_decode($output, true);

        expect($exitCode)->toBe(0)
            ->and($decoded)->toBeArray()
            ->and($decoded)->toHaveCount(2)
            ->and($decoded[0]['applkey'])->toBe('KEY123')
            ->and($decoded[0]['description'])->toBe('Test Invoice')
            ->and($decoded[0]['type'])->toBe('INVOICE')
            ->and($decoded[0]['date'])->toBe('2024-01-15 10:30:00')
            ->and($decoded[1]['applkey'])->toBe('KEY456')
            ->and($decoded[1]['description'])->toBe('Tax Notice');
    });

    it('outputs empty array as JSON when no items found', function (): void {
        $mock = Mockery::mock(DataboxDownloadWs::class);
        $mock->shouldReceive('get')
            ->once()
            ->andReturn([]);

        $this->app->instance(DataboxDownloadWs::class, $mock);

        $exitCode = \Illuminate\Support\Facades\Artisan::call('fon:list-databox', [
            '--json' => true,
        ]);

        $output = trim(\Illuminate\Support\Facades\Artisan::output());
        $decoded = json_decode($output, true);

        expect($exitCode)->toBe(0)
            ->and($decoded)->toBeArray()
            ->and($decoded)->toBeEmpty();
    });
});
