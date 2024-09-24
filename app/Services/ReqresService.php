<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Contracts\ReqresServiceContract;

use function config;

class ReqresService implements ReqresServiceContract
{

    /**
     * The base URL for the Reqres API.
     *
     * @var string
     */
    protected string $baseURL;

    /**
     * Number of records per page.
     *
     * @var int
     */
    protected $perPageRecords;


    /**
     * HTTP client for making requests to the Reqres API.
     *
     * @var \Illuminate\Http\Client\PendingRequest
     */
    protected \Illuminate\Http\Client\PendingRequest $http;


    /**
     * ReqresService constructor.
     *
     * Initializes the service with the number of records per page and the base URL for the Reqres API.
     */
    public function __construct()
    {
        $this->perPageRecords = config('reqres.per_page_records');

        $this->baseURL = config('reqres.base_url');

        $this->http = Http::baseUrl($this->baseURL);
    }

    /**
     * Get the base URL for the Reqres API.
     *
     * @return string The base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseURL;
    }


    /**
     * Get the number of records per page.
     *
     * @return int The number of records per page.
     */
    public function getPerPageRecords(): int
    {
        return $this->perPageRecords;
    }


    /**
     * Fetches a list of users from the Reqres API.
     *
     * @param int $page The page number to retrieve.
     * @param int|null $perPage The number of records per page. Defaults to the configured value.
     *
     * @return \Illuminate\Http\Client\Response The response from the Reqres API.
     *
     * @throws \Illuminate\Http\Client\ConnectionException If there is a connection error.
     */
    public function getUsers(int $page = 1, int $perPage = null): \Illuminate\Http\Client\Response
    {
        return (clone $this->http)->get('api/users', ['page' => $page, 'per_page' => $perPage ?? $this->perPageRecords]);
    }
}
