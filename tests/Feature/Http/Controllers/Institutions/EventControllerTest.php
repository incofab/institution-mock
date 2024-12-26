<?php

use App\Models\Course;
use App\Models\Event;
use App\Models\Exam;
use App\Models\Institution;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    $this->institution = Institution::factory()->user()->create();
    $this->assignedUser = $this->institution->institutionUsers()->first()->user;
    actingAs($this->assignedUser);
});

test('index method displays events', function () {
    $response = $this->get(
        route('institutions.events.index', $this->institution),
    );
    $response
        ->assertStatus(200)
        ->assertViewIs('institutions.events.index')
        ->assertViewHas('allRecords');
});

test('create method shows the create form', function () {
    Course::factory()
        ->for($this->institution)
        ->count(3)
        ->create();

    $response = $this->get(
        route('institutions.events.create', $this->institution),
    );

    $response
        ->assertStatus(200)
        ->assertViewIs('institutions.events.create')
        ->assertViewHas('subjects');
});

test('store method creates a new event', function () {
    $data = [
        'title' => 'New Event',
        'duration' => 120,
    ];

    $response = $this->post(
        route('institutions.events.store', $this->institution),
        $data,
    );

    $response
        ->assertRedirect(route('institutions.events.index', $this->institution))
        ->assertSessionHas('message', 'Event recorded successfully');

    $this->assertDatabaseHas(
        'events',
        array_merge($data, ['institution_id' => $this->institution->id]),
    );
});

test('edit method shows the edit form', function () {
    $event = Event::factory()
        ->for($this->institution)
        ->create();

    $response = $this->get(
        route('institutions.events.edit', [$this->institution, $event]),
    );

    $response
        ->assertStatus(200)
        ->assertViewIs('institutions.events.create')
        ->assertViewHas('edit', $event);
});

test('update method updates an event', function () {
    $event = Event::factory()
        ->for($this->institution)
        ->create();
    $data = [
        'title' => 'Updated Event',
        'duration' => 150,
    ];

    $response = $this->put(
        route('institutions.events.update', [$this->institution, $event]),
        $data,
    );

    $response
        ->assertRedirect(route('institutions.events.index', $this->institution))
        ->assertSessionHas('success', 'Record updated');

    $this->assertDatabaseHas(
        'events',
        array_merge($data, ['id' => $event->id]),
    );
});

test('suspend method suspends an event', function () {
    $event = Event::factory()
        ->for($this->institution)
        ->create();

    $response = $this->get(
        route('institutions.events.suspend', [$this->institution, $event]),
    );

    $response
        ->assertRedirect(route('institutions.events.index', $this->institution))
        ->assertSessionHas('message', 'Event has been suspended');

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'status' => 'suspended',
    ]);
});

test('unSuspend method activates a suspended event', function () {
    $event = Event::factory()
        ->for($this->institution)
        ->create(['status' => 'suspended']);

    $response = $this->get(
        route('institutions.events.unsuspend', [$this->institution, $event]),
    );

    $response
        ->assertRedirect(route('institutions.events.index', $this->institution))
        ->assertSessionHas('message', 'Event has been unsuspended');

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'status' => 'active',
    ]);
});

test('destroy method deletes an event without exams', function () {
    $event = Event::factory()
        ->for($this->institution)
        ->create();

    $this->delete(
        route('institutions.events.destroy', [$this->institution, $event]),
    )
        ->assertRedirect(route('institutions.events.index', $this->institution))
        ->assertSessionHas('message', 'Event deleted successfully');

    assertDatabaseMissing('events', $event->only('id'));
});

test('destroy method aborts if event contains exams', function () {
    $event = Event::factory()
        ->for($this->institution)
        ->create();
    Exam::factory()->for($event)->create();
    $this->delete(
        route('institutions.events.destroy', [$this->institution, $event]),
    )->assertStatus(401);
    $this->assertDatabaseHas('events', ['id' => $event->id]);
});
