<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\{get, actingAs};

describe('guest', function () {
    test('dashboard page redirects to login page.', function () {
        get(route('dashboard'))->assertRedirect(route('login'));
    });
});

describe('auth', function () {
    beforeEach(function () {
        User::factory(10)->create();

        $this->user = User::factory()->create();
    });

    test('dashboard page displayed', function () {
        actingAs($this->user)->get(route('dashboard'))
            ->assertOk();
    });

    test('dashboard page displays the users.', function () {
        actingAs($this->user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee($this->user->name)
            ->assertSee($this->user->email);
    });

    test('show users from page 2 on dashboard', function () {
        actingAs($this->user)->get(route('dashboard', ['page' => 2]))
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $page) => $page
                ->where('users.current_page', 2)
                ->where('users.total', 11),
            );
    });

    test('search users by first_name on dashboard', function () {

        User::factory()->create(['first_name' => 'Yash', 'last_name' => 'Pal']);

        actingAs($this->user)->get(route('dashboard', ['search' => 'Yash', 'column' => 'first_name']))
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $inertia) => $inertia
                ->where('users.total', 1)
                ->where('users.data.0.first_name', 'Yash')
            );
    });

    test('search users by email on dashboard', function () {

        User::factory()->create(['email' => 'iyashpal.dev@gmail.com']);

        actingAs($this->user)->get(route('dashboard', ['search' => 'iyashpal.dev', 'column' => 'email']))
            ->assertOk()
            ->assertInertia(
                fn (AssertableInertia $inertia) => $inertia
                ->where('users.total', 1)
                ->where('users.data.0.email', 'iyashpal.dev@gmail.com')
            );
    });
});
