<?php

declare(strict_types=1);

namespace CSoellinger\Laravel\FonWebservices\Facades;

use CSoellinger\FonWebservices\Response\Session\LoginSuccessResponse;
use CSoellinger\FonWebservices\Response\Session\LogoutSuccessResponse;
use CSoellinger\FonWebservices\SessionWs;
use Illuminate\Support\Facades\Facade;

/**
 * @method static LoginSuccessResponse login()
 * @method static LogoutSuccessResponse logout()
 * @method static string getSessionId()
 *
 * @see SessionWs
 */
class FonSession extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return SessionWs::class;
    }
}
