<?php

use App\Models\Grade;
use App\Models\Institution;
use App\Models\Student;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
  $this->institution = Institution::factory()->user()->create();
  $this->assignedUser = $this->institution->institutionUsers()->first()->user;
  $this->grade = Grade::factory()
    ->for($this->institution)
    ->create();
  actingAs($this->assignedUser);
});

test(
  'index method filters students by grade and returns the correct view',
  function () {
    Student::factory(3)
      ->grade($this->grade)
      ->create();

    $response = getJson(
      route('institutions.students.index', $this->institution) .
        '?grade=' .
        $this->grade->id,
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
  $data = [
    'firstname' => 'John',
    'lastname' => 'Doe',
    'grade_id' => $this->grade->id,
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
  $studentsData = [
    [
      'firstname' => 'Alice',
      'lastname' => 'Smith',
      'grade_id' => $this->grade->id,
    ],
    [
      'firstname' => 'Bob',
      'lastname' => 'Brown',
      'grade_id' => $this->grade->id,
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
  $student = Student::factory()
    ->grade($this->grade)
    ->create();

  $response = getJson(
    route('institutions.students.edit', [$this->institution, $student]),
  );

  $response->assertOk();
  $response->assertViewIs('institutions.students.create');
  $response->assertViewHas('edit', $student);
  $response->assertViewHas('allGrades');
});

test('update method updates a student and redirects', function () {
  $student = Student::factory()
    ->grade($this->grade)
    ->create();
  $grade = Grade::factory()
    ->for($this->institution)
    ->create();

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
  $student = Student::factory()
    ->grade($this->grade)
    ->create();

  $response = deleteJson(
    route('institutions.students.destroy', [$this->institution, $student]),
  );

  $response->assertRedirect(
    route('institutions.students.index', $this->institution),
  );
  assertDatabaseMissing('students', ['id' => $student->id]);
});

test('uploadStore method applies selected grade to all uploaded students', function () {
  $grade = Grade::factory()
    ->for($this->institution)
    ->create();

  $response = post(route('institutions.students.upload.store', $this->institution), [
    'grade_id' => $grade->id,
    'file' => makeStudentUploadFile([
      ['Mary', 'Stone', '', '', 'Class Not In This Institution'],
      ['James', 'Hill', '', '', 'Another Missing Class'],
    ]),
  ]);

  $response->assertSessionHasNoErrors();
  $response->assertSessionHas('message', 'Records uploaded successfully');
  assertDatabaseHas('students', [
    'firstname' => 'Mary',
    'lastname' => 'Stone',
    'grade_id' => $grade->id,
    'institution_id' => $this->institution->id,
  ]);
  assertDatabaseHas('students', [
    'firstname' => 'James',
    'lastname' => 'Hill',
    'grade_id' => $grade->id,
    'institution_id' => $this->institution->id,
  ]);
});

test('uploadStore method reads grade from spreadsheet when none is selected', function () {
  $response = post(route('institutions.students.upload.store', $this->institution), [
    'file' => makeStudentUploadFile([
      ['Ruth', 'Cole', '', '', $this->grade->title],
    ]),
  ]);

  $response->assertSessionHasNoErrors();
  $response->assertSessionHas('message', 'Records uploaded successfully');
  assertDatabaseHas('students', [
    'firstname' => 'Ruth',
    'lastname' => 'Cole',
    'grade_id' => $this->grade->id,
    'institution_id' => $this->institution->id,
  ]);
});

function makeStudentUploadFile(array $rows): UploadedFile
{
  $spreadsheet = new Spreadsheet();
  $sheet = $spreadsheet->getActiveSheet();
  $sheet->fromArray(
    [['Firstname', 'Lastname', 'Phone', 'Email', 'Class'], ...$rows],
  );

  $path = tempnam(sys_get_temp_dir(), 'students-upload');
  (new Xlsx($spreadsheet))->save($path);

  return new UploadedFile(
    $path,
    'students-upload.xlsx',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    null,
    true,
  );
}
