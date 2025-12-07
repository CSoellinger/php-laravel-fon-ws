<?php

declare(strict_types=1);

namespace CSoellinger\Laravel\FonWebservices\Facades;

use CSoellinger\FonWebservices\Enum\VatIdCheckLevel;
use CSoellinger\FonWebservices\Model\VatIdCheckInvalid;
use CSoellinger\FonWebservices\Model\VatIdCheckValidLevelOne;
use CSoellinger\FonWebservices\Model\VatIdCheckValidLevelTwo;
use CSoellinger\FonWebservices\VatIdCheckWs;
use Illuminate\Support\Facades\Facade;

/**
 * @method static VatIdCheckInvalid|VatIdCheckValidLevelOne|VatIdCheckValidLevelTwo check(string $uid, VatIdCheckLevel|int $level = VatIdCheckLevel::SimpleCheck)
 *
 * @see VatIdCheckWs
 */
class FonVatIdCheck extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return VatIdCheckWs::class;
    }
}
