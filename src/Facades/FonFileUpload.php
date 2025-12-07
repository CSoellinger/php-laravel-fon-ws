<?php

declare(strict_types=1);

namespace CSoellinger\Laravel\FonWebservices\Facades;

use CSoellinger\FonWebservices\FileUploadWs;
use Illuminate\Support\Facades\Facade;

/**
 * @see FileUploadWs
 */
class FonFileUpload extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return FileUploadWs::class;
    }
}
