<?php

declare(strict_types=1);

namespace CSoellinger\Laravel\FonWebservices\Facades;

use CSoellinger\FonWebservices\QueryDataTransmissionWs;
use Illuminate\Support\Facades\Facade;

/**
 * @see QueryDataTransmissionWs
 */
class FonQueryDataTransmission extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return QueryDataTransmissionWs::class;
    }
}
