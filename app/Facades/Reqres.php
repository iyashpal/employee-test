<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string getBaseUrl()
 * @method static int getPerPageRecords()
 * @method static \Illuminate\Http\Client\Response getUsers(int $page = 1, int $perPage = null)
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
