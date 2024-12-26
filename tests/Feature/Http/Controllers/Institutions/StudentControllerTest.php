<?php

use App\Models\Grade;
use App\Models\Institution;
use App\Models\Student;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->institution = Institution::factory()->user()->create();
    $this->assignedUser = $this->institution->institutionUsers()->first()->user;
    actingAs($this->assignedUser);
});

test(
    'index method filters students by grade and returns the correct view',
    function () {
        $grade = Grade::factory()->create();
        Student::factory(3)->for($grade)->create();

        $response = getJson(
            route('institutions.students.index', $this->institution) .
                '?grade=' .
                $grade->id,
        );

        $response->assertOk();
        $response->assertViewIs('institutions.students.index');
        $response->assertViewHas('allRecords');
        $response->assertViewHas('allGrades');
        $this->assertCount(3, $response->viewData('allRecords')->items());
    },
);

test('create method returns the correct view', function () {
    $response = getJson(
        route('institutions.students.create', $this->institution),
    );

    $response->assertOk();
    $response->assertViewIs('institutions.students.create');
    $response->assertViewHas('allGrades');
});

test('store method creates a new student and redirects', function () {
    $grade = Grade::factory()->create();

    $data = [
        'firstname' => 'John',
        'lastname' => 'Doe',
        'grade_id' => $grade->id,
    ];

    $response = postJson(
        route('institutions.students.store', $this->institution),
        $data,
    );

    $response->assertRedirect(
        route('institutions.students.index', $this->institution),
    );
    assertDatabaseHas('students', $data);
});

test('multiCreate method returns the correct view', function () {
    $response = getJson(
        route('institutions.students.multi-create', $this->institution),
    );

    $response->assertOk();
    $response->assertViewIs('institutions.students.multi-create');
    $response->assertViewHas('allGrades');
});

test('multiStore method creates multiple students and redirects', function () {
    $grade = Grade::factory()->create();

    $studentsData = [
        [
            'firstname' => 'Alice',
            'lastname' => 'Smith',
            'grade_id' => $grade->id,
        ],
        [
            'firstname' => 'Bob',
            'lastname' => 'Brown',
            'grade_id' => $grade->id,
        ],
    ];

    $data = ['students' => $studentsData];

    $response = postJson(
        route('institutions.students.multi-store', $this->institution),
        $data,
    );

    $response->assertRedirect(
        route('institutions.students.index', $this->institution),
    );
    foreach ($studentsData as $student) {
        assertDatabaseHas('students', $student);
    }
});

test('edit method returns the correct view', function () {
    $student = Student::factory()->create();

    $response = getJson(
        route('institutions.students.edit', [$this->institution, $student]),
    );

    $response->assertOk();
    $response->assertViewIs('institutions.students.create');
    $response->assertViewHas('edit', $student);
    $response->assertViewHas('allGrades');
});

test('update method updates a student and redirects', function () {
    $student = Student::factory()->create();
    $grade = Grade::factory()->create();

    $data = [
        'firstname' => 'Updated Name',
        'lastname' => $student->lastname,
        'grade_id' => $grade->id,
    ];

    $response = putJson(
        route('institutions.students.update', [$this->institution, $student]),
        $data,
    );

    $response->assertRedirect(
        route('institutions.students.index', $this->institution),
    );
    assertDatabaseHas('students', $data);
});

test('destroy method deletes a student and redirects', function () {
    $student = Student::factory()->create();

    $response = deleteJson(
        route('institutions.students.destroy', [$this->institution, $student]),
    );

    $response->assertRedirect(
        route('institutions.students.index', $this->institution),
    );
    assertDatabaseMissing('students', ['id' => $student->id]);
});
