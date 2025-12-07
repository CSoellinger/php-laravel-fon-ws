<?php

declare(strict_types=1);

namespace CSoellinger\Laravel\FonWebservices\Facades;

use CSoellinger\FonWebservices\DataboxDownloadWs;
use Illuminate\Support\Facades\Facade;

/**
 * @see DataboxDownloadWs
 */
class FonDataboxDownload extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return DataboxDownloadWs::class;
    }
}
