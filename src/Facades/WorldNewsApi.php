<?php

namespace Amsaid\WorldNewsApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Amsaid\WorldNewsApi\WorldNewsApi
 */
class WorldNewsApi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Amsaid\WorldNewsApi\WorldNewsApi::class;
    }
}
