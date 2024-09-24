<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 *
 * @see \App\Services\ReqresService
 */
class Reqres extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\ReqresService::class;
    }
}
