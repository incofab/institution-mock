<?php
namespace App\Http\Controllers\Institutions;

use App\Actions\CreateStudent;
use App\Http\Controllers\Controller;
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

        return redirect(
            route('institutions.students.index', [$institution]),
        )->with('message', 'Student registered, Record exam subjects');
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

        return redirect(
            route('institutions.students.index', [$institution]),
        )->with('message', 'Students registered');
    }

    function edit(Institution $institution, Student $student)
    {
        return view('institutions.students.create', [
            'edit' => $student,
            'allGrades' => Grade::all(),
        ]);
    }

    function update(
        Institution $institution,
        Request $request,
        Student $student,
    ) {
        $data = $request->validate(Student::ruleCreate());
        $student->update(collect($data)->except('institution_id')->toArray());

        return redirect(
            route('institutions.students.index', $institution),
        )->with('message', "{$student->firstname}'s record updated");
    }

    function destroy(Institution $institution, Student $student)
    {
        $student->delete();

        return redirect(
            route('institutions.students.index', $institution),
        )->with('message', 'Student deleted successfully');
    }
    /*
    function multiDelete(Institution $institution, Request $request)
    {
        $data = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids' => ['required', 'exists:students,id'],
        ]);

        Student::query()->whereIn('id', $data)->delete();
        return redirect(
            route('institutions.students.index', $institution),
        )->with('message', 'Students deleted');
    }

    function uploadCreate(Institution $institution)
    {
        return view('institutions.students.upload', [
            'grades' => Grade::all(),
        ]);
    }

    function uploadStore(
        Institution $institution,
        Request $request,
        \App\Helpers\StudentsUploadHelper $studentsUploadHelper,
    ) {
        $ret = $studentsUploadHelper->uploadStudent(
            $_FILES,
            $request->get('institution'),
        );

        if (!$ret[SUCCESSFUL]) {
            return $this->redirect(redirect()->back(), $ret);
        }

        return redirect(
            route('institutions.students.index', $institution),
        )->with('message', $ret[MESSAGE]);
    }

    function downloadTemplateExcel()
    {
        $fileToDownload = public_path() . '/student-recording-template.xlsx';
        $file_name = 'student-recording-template.xlsx';

        header('Content-Type: application/zip');

        header("Content-Disposition: attachment; filename=$file_name");

        header('Content-Length: ' . filesize($fileToDownload));

        readfile($fileToDownload);

        exit();
    }
    */
}
