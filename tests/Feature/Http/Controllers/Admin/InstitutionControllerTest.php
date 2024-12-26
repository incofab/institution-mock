<?php

use App\Models\Course;
use App\Models\Institution;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $admin = User::factory()->admin()->create();
    actingAs($admin);
});
test('index method returns view with paginated institutions', function () {
    Institution::factory()->count(10)->create();

    $response = $this->get(route('admin.institutions.index'));

    $response
        ->assertOk()
        ->assertViewIs('admin.institutions.index')
        ->assertViewHas('allRecords');
});

test('create method returns view for creating an institution', function () {
    $response = $this->get(route('admin.institutions.create'));

    $response->assertOk()->assertViewIs('admin.institutions.create');
});

test('store method validates and creates an institution', function () {
    $data = Institution::factory()->make()->toArray();

    $this->post(route('admin.institutions.store'), $data)
        ->assertRedirect(route('admin.institutions.index'))
        ->assertSessionHas('message', 'Record created successfully');

    $this->assertDatabaseCount('institutions', 1);
});

test('edit method updates an institution', function () {
    $institution = Institution::factory()->create();
    $response = $this->get(route('admin.institutions.edit', $institution));
    $response->assertOk()->assertViewIs('admin.institutions.create');
});

test('update method updates an institution and redirects', function () {
    $institution = Institution::factory()->create();
    $updatedData = [
        'name' => 'Updated Institution Name',
        'email' => 'updated@example.com',
    ];

    $response = $this->put(
        route('admin.institutions.update', $institution),
        $updatedData,
    );

    $response
        ->assertRedirect(route('admin.institutions.index'))
        ->assertSessionHas('message', 'Record updated');

    $this->assertDatabaseHas('institutions', $updatedData);
});

test(
    'destroy method deletes an institution without courses or events',
    function () {
        $institution = Institution::factory()->create();

        $response = $this->delete(
            route('admin.institutions.destroy', $institution),
        );

        $response
            ->assertRedirect(route('admin.institutions.index'))
            ->assertSessionHas('message', 'Delete institution');

        $this->assertDatabaseMissing('institutions', [
            'id' => $institution->id,
        ]);
    },
);

test('destroy method aborts if institution has courses or events', function () {
    $institution = Institution::factory()->create();
    Course::factory()->for($institution)->create();

    $response = $this->delete(
        route('admin.institutions.destroy', $institution),
    );

    $response->assertStatus(401);
    $this->assertDatabaseHas('institutions', ['id' => $institution->id]);
});

test('assignUserView method returns view with institution', function () {
    $institution = Institution::factory()->create();

    $response = $this->get(
        route('admin.institutions.assign-user', $institution),
    );

    $response
        ->assertOk()
        ->assertViewIs('admin.institutions.assign-user')
        ->assertViewHas('institution', $institution);
});

test('assignUserStore method assigns user to institution', function () {
    $institution = Institution::factory()->create();
    $user = User::factory()->create();

    $data = ['email' => $user->email];

    $response = $this->post(
        route('admin.institutions.assign-user.store', $institution),
        $data,
    );

    $response
        ->assertRedirect(route('admin.institutions.index'))
        ->assertSessionHas('message', 'User assigned');

    $this->assertDatabaseHas('institution_users', [
        'institution_id' => $institution->id,
        'user_id' => $user->id,
    ]);
});
