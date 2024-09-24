<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Service Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the ReqRes API, retrieved from the environment
    | configuration. Defaults to "https://reqres.in" if not set.
    |
    */
    'base_url' => env('REQRES_BASE_URL', "https://reqres.in"),


    /*
    |--------------------------------------------------------------------------
    | Records Per Page
    |--------------------------------------------------------------------------
    |
    | The number of records to display per page, retrieved from the environment
    | configuration. Defaults to 10 if not set.
    |
    */
    'per_page_records' => env('REQRES_PER_PAGE_RECORDS', 10)
];
