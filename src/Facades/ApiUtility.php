<?php

namespace Decodeblock\ApiUtility\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Decodeblock\ApiUtility\ApiUtility
 */
class ApiUtility extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Decodeblock\ApiUtility\ApiUtility::class;
    }
}
