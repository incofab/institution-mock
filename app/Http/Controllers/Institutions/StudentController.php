<?php
namespace App\Http\Controllers\Institutions;

use App\Actions\CreateStudent;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadStudentsRequest;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\Institution;

class StudentController extends Controller
{
  function index(Request $request, Institution $institution)
  {
    $grade = $request->grade;
    $query = Student::query()
      ->when($grade, fn($q) => $q->where('grade_id', $grade))
      ->with('grade');
    return view('institutions.students.index', [
      'allRecords' => paginateFromRequest($query),
      'allGrades' => Grade::all(),
    ]);
  }

  function create(Institution $institution)
  {
    return view('institutions.students.create', [
      'allGrades' => Grade::all(),
      'edit' => null,
    ]);
  }

  function store(Institution $institution, Request $request)
  {
    $data = $request->validate(Student::ruleCreate());

    (new CreateStudent($institution))->run($data);

    return redirect(route('institutions.students.index', [$institution]))->with(
      'message',
      'Student registered, Record exam subjects',
    );
  }

  function multiCreate(Institution $institution)
  {
    return view('institutions.students.multi-create', [
      'allGrades' => Grade::all(),
    ]);
  }

  function multiStore(Institution $institution, Request $request)
  {
    $data = $request->validate([
      'students' => ['required', 'array', 'min:1'],
      ...Student::ruleCreate('students.*.'),
    ]);
    foreach ($data['students'] as $student) {
      (new CreateStudent($institution))->run($student);
    }

    return redirect(route('institutions.students.index', [$institution]))->with(
      'message',
      'Students registered',
    );
  }

  function edit(Institution $institution, Student $student)
  {
    return view('institutions.students.create', [
      'edit' => $student,
      'allGrades' => Grade::all(),
    ]);
  }

  function update(Institution $institution, Request $request, Student $student)
  {
    $data = $request->validate(Student::ruleCreate());
    $student->update(collect($data)->except('institution_id')->toArray());

    return redirect(route('institutions.students.index', $institution))->with(
      'message',
      "{$student->firstname}'s record updated",
    );
  }

  function destroy(Institution $institution, Student $student)
  {
    $student->delete();

    return redirect(route('institutions.students.index', $institution))->with(
      'message',
      'Student deleted successfully',
    );
  }

  function uploadCreate(Institution $institution)
  {
    return view('institutions.students.upload', [
      'grades' => Grade::all(),
    ]);
  }

  function uploadStore(Institution $institution, UploadStudentsRequest $request)
  {
    $data = $request->safe()->students;
    $gradeKeys = Grade::query()->pluck('id', 'title');
    foreach ($data as $key => $student) {
      (new CreateStudent($institution))->run([
        ...collect($student)->except('grade_title')->toArray(),
        'grade_id' => $gradeKeys[$student['grade_title'] ?? ''] ?? null,
        'institution_id' => $institution->id,
      ]);
    }
    return back()->with('message', 'Records uploaded successfully');
  }

  function downloadTemplateExcel(Institution $institution)
  {
    $file_name = 'student-recording-template.xlsx';
    $fileToDownload = public_path($file_name);

    header('Content-Type: application/zip');

    header("Content-Disposition: attachment; filename=$file_name");

    header('Content-Length: ' . filesize($fileToDownload));

    readfile($fileToDownload);

    exit();
  }
}
