<?php

namespace Idez\NovaSecurity\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Idez\NovaSecurity\NovaSecurity
 */
class NovaSecurity extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'nova-security';
    }
}
