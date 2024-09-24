<?php

use App\Facades\Reqres;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\artisan;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertTrue;

function generateRecords(int $start = 1, int $end = 12): array
{
    return collect(range($start, $end))
        ->map(fn ($id) => [
            'id' => $id,
            'email' => 'george.bluth@reqres.in',
            'first_name' => 'George',
            'last_name' => 'Bluth',
            'avatar' => "https://reqres.in/img/faces/{$id}-image.jpg",
        ])->toArray();
}

beforeEach(function () {
    $this->url = Reqres::getBaseUrl().'/api/users';

    $firstPageResponse = Http::response([
        'page' => 1,
        'per_page' => 6,
        'total' => 12,
        'total_pages' => 2,
        'data' => generateRecords(1, 6),
    ], 200);

    Http::fake([
        $this->url => $firstPageResponse,
        "{$this->url}?page=1" => $firstPageResponse,
        "{$this->url}?page=2" => Http::response([
            'page' => 2,
            'per_page' => 6,
            'total' => 12,
            'total_pages' => 2,
            'data' => generateRecords(7),
        ], 200),
        "{$this->url}?page=3" => Http::response([
            'page' => 3,
            'per_page' => 6,
            'total' => 12,
            'total_pages' => 2,
            'data' => [],
        ], 200),

    ]);
});

test('it fetches users successfully', function () {
    $response = Reqres::getUsers();

    // Assert request ok
    assertTrue($response->successful());
    assertEquals(200, $response->status());

    assertIsArray($response->json('data'));

    // As per the reqres configuration the default records limit is 10
    assertCount(10, $response->collect('data'));
});

test('it fetches users for specific pages', function ($page) {
    $response = Reqres::getUsers(page: $page, perPage: 6);

    // Assert request ok
    assertTrue($response->successful());
    assertEquals(200, $response->status());

    assertIsArray($response->json('data'));
    // Assert response page
    assertEquals($page, $response->json('page'));
    // Assert response data counts.
    assertCount(6, $response->collect('data'));
})->with([1, 2]);

test('it returns empty data when we exceed the total number of pages.', function () {
    $response = Reqres::getUsers(page: 3, perPage: 6);

    // Assert request ok
    assertTrue($response->successful());
    assertEquals(200, $response->status());

    assertIsArray($response->json('data'));

    assertCount(0, $response->collect('data'));
});

test('it syncs users from Reqres API via artisan command.', function () {
    artisan('reqres:sync-users')
        ->assertSuccessful()
        ->expectsOutputToContain('Fetching users from ReqRes "reqres.in"')
        ->expectsOutputToContain('Total records fetched: 10')
        ->assertExitCode(1);
});

test('it syncs users from Reqres API via artisan command with modified per page records.', function ($records) {
    artisan('reqres:sync-users --per-page-records='.$records)
        ->assertSuccessful()
        ->expectsOutputToContain('Fetching users from ReqRes "reqres.in"')
        ->expectsOutputToContain('Total records fetched: '.$records)
        ->assertExitCode(1);
})->with([2, 4, 6, 7, 5, 9, 10, 12]);

test('it syncs users from all available pages of Reqres API via artisan command.', function () {
    artisan('reqres:sync-users --fetch-all')
        ->assertSuccessful()
        ->expectsOutputToContain('Fetching users from ReqRes "reqres.in"')
        ->expectsOutputToContain('Total records fetched: 12')
        ->assertExitCode(1);
});
