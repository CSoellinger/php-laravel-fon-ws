<?php

declare(strict_types=1);

namespace CSoellinger\Laravel\FonWebservices\Facades;

use CSoellinger\FonWebservices\BankDataTransmissionWs;
use Illuminate\Support\Facades\Facade;

/**
 * @see BankDataTransmissionWs
 */
class FonBankDataTransmission extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return BankDataTransmissionWs::class;
    }
}
