<?php

use App\Facades\Reqres;
use Illuminate\Support\Facades\Http;

use function PHPUnit\Framework\{assertTrue, assertCount, assertEquals, assertIsArray};

beforeEach(function () {
    $url = Reqres::getBaseUrl() . "/api/users";

    // Fake response data generator.
    $generateData = fn(int $start = 1, int $end = 12): array => collect(range($start, $end))
        ->map(fn($id) => [
            "id" => $id,
            "email" => "george.bluth@reqres.in",
            "first_name" => "George",
            "last_name" => "Bluth",
            "avatar" => "https://reqres.in/img/faces/{$id}-image.jpg",
        ])->toArray();

    $firstPageResponse = Http::response([
        "page" => 1,
        "per_page" => 6,
        "total" => 12,
        "total_pages" => 2,
        'data' => $generateData(1, 6),
    ], 200);


    Http::fake([
        $url => $firstPageResponse,
        "{$url}?page=1" => $firstPageResponse,
        "{$url}?page=2" => Http::response([
            "page" => 2,
            "per_page" => 6,
            "total" => 12,
            "total_pages" => 2,
            'data' => $generateData(7, 12),
        ], 200),
        "{$url}?page=3" => Http::response([
            "page" => 3,
            "per_page" => 6,
            "total" => 12,
            "total_pages" => 2,
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
