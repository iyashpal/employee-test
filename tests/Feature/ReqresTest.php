<?php

use App\Facades\Reqres;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\artisan;
use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertIsArray;
use function Pest\Laravel\assertDatabaseCount;

function generateRecords(int $start = 1, int $end = 12): array
{
    return collect(range($start, $end))
        ->map(fn ($id) => [
            'id' => $id,
            'email' => "email_{$id}@reqres.in",
            'first_name' => 'User',
            'last_name' => 'Name',
            'avatar' => "https://reqres.in/img/faces/{$id}-image.jpg",
        ])->toArray();
}

beforeEach(function () {
    $this->url = 'reqres.in/api/users';

    $responses = collect([50, 40, 30, 20])
        ->mapWithKeys(function ($per_page) {
            return [
                "{$this->url}?page=1&per_page={$per_page}" => Http::response([
                    'page' => 1,
                    'per_page' => $per_page,
                    'total' => $per_page,
                    'total_pages' => 1,
                    'data' => generateRecords(end: $per_page),
                ], 200),
            ];
        });

    $generalResponses = [
        "{$this->url}" => Http::response([
            'page' => 1,
            'per_page' => 6,
            'total' => 12,
            'total_pages' => 2,
            'data' => generateRecords(1, 6),
        ], 200),
        "{$this->url}?page=1*" => Http::response([
            'page' => 1,
            'per_page' => 6,
            'total' => 12,
            'total_pages' => 2,
            'data' => generateRecords(1, 6),
        ], 200),
        "{$this->url}?page=2*" => Http::response([
            'page' => 2,
            'per_page' => 6,
            'total' => 12,
            'total_pages' => 2,
            'data' => generateRecords(7),
        ], 200),
        "{$this->url}?page=3*" => Http::response([
            'page' => 3,
            'per_page' => 6,
            'total' => 12,
            'total_pages' => 2,
            'data' => [],
        ], 200),

    ];

    Http::fake($responses->merge($generalResponses)->toArray());
});

test('it fetches users successfully', function () {
    $response = Reqres::getUsers();

    // Assert request ok
    assertTrue($response->successful());
    assertEquals(200, $response->status());

    assertIsArray($response->json('data'));

    assertCount(6, $response->collect('data'));
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
        ->expectsOutputToContain('Total records fetched: 6')
        ->assertExitCode(0);

    assertDatabaseCount('users', 6);
});

test('it syncs users from Reqres API via artisan command with modified per page records.', function ($records) {
    artisan('reqres:sync-users --per-page-records='.$records)
        ->assertSuccessful()
        ->expectsOutputToContain('Fetching users from ReqRes "reqres.in"')
        ->expectsOutputToContain('Total records fetched: '.$records)
        ->assertExitCode(0);

    assertDatabaseCount('users', $records);
})->with([50, 40, 30, 20]);

test('it syncs users from all available pages of Reqres API via artisan command.', function () {
    artisan('reqres:sync-users --fetch-all')
        ->assertSuccessful()
        ->expectsOutputToContain('Fetching users from ReqRes "reqres.in"')
        ->expectsOutputToContain('Total records fetched: 12')
        ->assertExitCode(0);
    assertDatabaseCount('users', 12);
});
