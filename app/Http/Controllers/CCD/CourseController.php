<?php
namespace App\Http\Controllers\CCD;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ExamContent;
use App\Models\Institution;
use Illuminate\Http\Request;

class CourseController extends Controller
{
  public function index(
    Institution $institution,
    ExamContent|null $examContent = null,
  ) {
    $query = $examContent?->courses() ?? Course::query();
    return view('ccd.courses.index', [
      'allRecords' => paginateFromRequest($query->withCount('courseSessions')),
    ]);
  }

  public function create(Institution $institution)
  {
    return view('ccd.courses.create', ['edit' => null]);
  }

  public function store(Request $request, Institution $institution)
  {
    $validatedData = $request->validate(Course::createRule());
    $course = $institution->courses()->firstOrCreate(
      [
        'course_code' => $validatedData['course_code'],
      ],
      $validatedData,
    );

    return redirect(
      instRoute('ccd.courses.index', [$course->exam_content_id]),
    )->with('message', 'Data recorded successfully');
  }

  public function edit(Institution $institution, Course $course)
  {
    return view('ccd.courses.create', ['edit' => $course]);
  }

  public function update(
    Request $request,
    Institution $institution,
    Course $course,
  ) {
    $validatedData = $request->validate(Course::createRule());

    $course->update($validatedData);

    return redirect(
      instRoute('ccd.courses.index', [$course->exam_content_id]),
    )->with('message', 'Record updated successfully');
  }

  public function destroy(Institution $institution, Course $course)
  {
    abort_if(
      $course->courseSessions()->exists(),
      403,
      'Cannot delete content with subject(s)',
    );
    $course->delete();

    return redirect(
      instRoute('ccd.courses.index', [$course->exam_content_id]),
    )->with('message', 'Data deleted successfully');
  }
}
