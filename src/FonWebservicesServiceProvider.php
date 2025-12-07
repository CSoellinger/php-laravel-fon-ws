<?php

declare(strict_types=1);

namespace CSoellinger\Laravel\FonWebservices;

use CSoellinger\FonWebservices\Authentication\FonCredential;
use CSoellinger\FonWebservices\BankDataTransmissionWs;
use CSoellinger\FonWebservices\DataboxDownloadWs;
use CSoellinger\FonWebservices\FileUploadWs;
use CSoellinger\FonWebservices\QueryDataTransmissionWs;
use CSoellinger\FonWebservices\SessionWs;
use CSoellinger\FonWebservices\VatIdCheckWs;
use CSoellinger\Laravel\FonWebservices\Console\CheckVatCommand;
use CSoellinger\Laravel\FonWebservices\Console\ListDataboxCommand;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class FonWebservicesServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/fon-webservices.php',
            'fon-webservices'
        );

        // Register FonCredential as a singleton
        $this->app->singleton(FonCredential::class, function ($app) {
            $credentials = config('fon-webservices.credentials');

            return new FonCredential(
                $credentials['te_id'] ?? '',
                $credentials['te_uid'] ?? '',
                $credentials['ben_id'] ?? '',
                $credentials['ben_pin'] ?? ''
            );
        });

        // Register SessionWs as a singleton
        $this->app->singleton(SessionWs::class, function ($app) {
            $soapOptions = config('fon-webservices.soap_options', []);

            return new SessionWs(
                $app->make(FonCredential::class),
                $soapOptions
            );
        });

        // Register VatIdCheckWs if enabled
        if (config('fon-webservices.services.vat_id_check', true)) {
            $this->app->singleton(VatIdCheckWs::class, function ($app) {
                $soapOptions = config('fon-webservices.soap_options', []);

                return new VatIdCheckWs(
                    $app->make(SessionWs::class),
                    $soapOptions
                );
            });
        }

        // Register DataboxDownloadWs if enabled
        if (config('fon-webservices.services.databox_download', true)) {
            $this->app->singleton(DataboxDownloadWs::class, function ($app) {
                $soapOptions = config('fon-webservices.soap_options', []);

                return new DataboxDownloadWs(
                    $app->make(SessionWs::class),
                    $soapOptions
                );
            });
        }

        // Register FileUploadWs if enabled
        if (config('fon-webservices.services.file_upload', true)) {
            $this->app->singleton(FileUploadWs::class, function ($app) {
                $soapOptions = config('fon-webservices.soap_options', []);

                return new FileUploadWs(
                    $app->make(SessionWs::class),
                    $soapOptions
                );
            });
        }

        // Register BankDataTransmissionWs if enabled
        if (config('fon-webservices.services.bank_data_transmission', true)) {
            $this->app->singleton(BankDataTransmissionWs::class, function ($app) {
                $soapOptions = config('fon-webservices.soap_options', []);

                return new BankDataTransmissionWs(
                    $app->make(SessionWs::class),
                    $soapOptions
                );
            });
        }

        // Register QueryDataTransmissionWs if enabled
        if (config('fon-webservices.services.query_data_transmission', true)) {
            $this->app->singleton(QueryDataTransmissionWs::class, function ($app) {
                $soapOptions = config('fon-webservices.soap_options', []);

                return new QueryDataTransmissionWs(
                    $app->make(SessionWs::class),
                    $soapOptions
                );
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            // Publish configuration file
            $this->publishes([
                __DIR__.'/../config/fon-webservices.php' => config_path('fon-webservices.php'),
            ], 'fon-webservices-config');

            // Register commands
            $this->commands([
                CheckVatCommand::class,
                ListDataboxCommand::class,
            ]);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            FonCredential::class,
            SessionWs::class,
            VatIdCheckWs::class,
            DataboxDownloadWs::class,
            FileUploadWs::class,
            BankDataTransmissionWs::class,
            QueryDataTransmissionWs::class,
        ];
    }
}
