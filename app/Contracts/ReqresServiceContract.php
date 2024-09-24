<?php

namespace App\Contracts;

use Illuminate\Http\Client\Response;

interface ReqresServiceContract
{
    /**
     * Get the base URL for the Reqres API.
     *
     * @return string The base URL.
     */
    public function getBaseUrl(): string;


    /**
     * Get the number of records per page.
     *
     * @return int The number of records per page.
     */
    public function getPerPageRecords(): int;

    /**
     * Fetches a list of users from the Reqres API.
     *
     * @param int $page The page number to retrieve.
     * @param int|null $perPage The number of records per page. Defaults to the configured value.
     *
     * @return Response The response from the Reqres API.
     */
    public function getUsers(int $page = 1, int $perPage = null): Response;
}
