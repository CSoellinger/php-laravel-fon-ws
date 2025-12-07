<?php

declare(strict_types=1);

namespace CSoellinger\Laravel\FonWebservices\Tests;

use CSoellinger\Laravel\FonWebservices\FonWebservicesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            FonWebservicesServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        // Load credentials from environment variables or use dummy credentials for unit/feature tests
        config()->set('fon-webservices.credentials', [
            'te_id' => env('FON_T_ID', 'TEST12345'),
            'te_uid' => env('FON_T_UID', 'ATU12345678'),
            'ben_id' => env('FON_BEN_ID', 'BENID'),
            'ben_pin' => env('FON_BEN_PIN', 'PIN12'),
        ]);
    }
}
