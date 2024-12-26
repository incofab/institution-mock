<?php

use App\Models\Grade;
use App\Models\Institution;
use App\Models\Student;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->institution = Institution::factory()->user()->create();
    $this->assignedUser = $this->institution->institutionUsers()->first()->user;
    actingAs($this->assignedUser);
});

it('shows the grades index page', function () {
    $response = $this->get(
        route('institutions.grades.index', $this->institution),
    );
    $response->assertStatus(200);
    $response->assertViewIs('institutions.grades.index');
});

it('shows the grade creation page', function () {
    $response = $this->get(
        route('institutions.grades.create', $this->institution),
    );
    $response->assertStatus(200);
    $response->assertViewIs('institutions.grades.create');
});

it('stores a new grade', function () {
    $gradeData = Grade::factory()
        ->make(['institution_id' => $this->institution->id])
        ->toArray();

    $response = $this->post(
        route('institutions.grades.store', $this->institution),
        $gradeData,
    );

    $response->assertRedirect(
        route('institutions.grades.index', $this->institution),
    );
    $this->assertDatabaseHas('grades', $gradeData);
});

it('shows the grade edit page', function () {
    $grade = Grade::factory()->create([
        'institution_id' => $this->institution->id,
    ]);

    $response = $this->get(
        route('institutions.grades.edit', [$this->institution, $grade]),
    );

    $response->assertStatus(200);
    $response->assertViewIs('institutions.grades.create');
    $response->assertViewHas('edit', $grade);
});

it('updates an existing grade', function () {
    $grade = Grade::factory()->create([
        'institution_id' => $this->institution->id,
    ]);
    $updateData = Grade::factory()
        ->make(['institution_id' => $this->institution->id])
        ->toArray();

    $response = $this->put(
        route('institutions.grades.update', [$this->institution, $grade]),
        $updateData,
    );

    $response->assertRedirect(
        route('institutions.grades.index', $this->institution),
    );
    $this->assertDatabaseHas('grades', $updateData);
});

it('deletes a grade', function () {
    $grade = Grade::factory()
        ->for($this->institution)
        ->create();

    $this->delete(
        route('institutions.grades.destroy', [$this->institution, $grade]),
    )->assertRedirect(route('institutions.grades.index', $this->institution));
    $this->assertModelMissing($grade);
});

it('prevents deletion of a grade with students', function () {
    $grade = Grade::factory()
        ->for($this->institution)
        ->create();
    Student::factory(2)
        ->for($grade)
        ->for($this->institution)
        ->create();

    $this->delete(
        route('institutions.grades.destroy', [$this->institution, $grade]),
    )->assertStatus(401);
    $this->assertModelExists($grade);
});
