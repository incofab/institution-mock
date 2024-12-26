<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $admin = User::factory()->admin()->create();
    actingAs($admin);
});

test('index method returns view with paginated users', function () {
    User::factory()->count(10)->create();

    $response = $this->get(route('admin.users.index'));

    $response
        ->assertOk()
        ->assertViewIs('admin.users.index')
        ->assertViewHas('allRecords');
});

test('search method returns view with filtered users', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $response = $this->get(
        route('admin.users.search', ['search_user' => 'john']),
    );

    $response
        ->assertOk()
        ->assertViewIs('admin.users.index')
        ->assertViewHas('allRecords');
});

test('destroy method deletes a user and redirects', function () {
    $user = User::factory()->create();

    $response = $this->delete(route('admin.users.destroy', $user));

    $response
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('message', 'User record deleted');

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});
