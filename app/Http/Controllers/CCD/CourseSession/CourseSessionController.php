<?php
namespace App\Http\Controllers\CCD\CourseSession;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Institution;

class CourseSessionController extends Controller
{
  function index(Institution $institution, Course $course)
  {
    $query = $course
      ->courseSessions()
      ->with('course')
      ->withCount(['questions']);

    return view('ccd.course-sessions.index', [
      'allRecords' => paginateFromRequest($query),
      'course' => $course,
      'courses' => Course::all(),
    ]);
  }

  function create(Institution $institution, Course $course)
  {
    return view('ccd.course-sessions.create', [
      'edit' => null,
      'course' => $course,
    ]);
  }

  function store(Institution $institution, Course $course)
  {
    $data = request()->validate(CourseSession::createRule());

    $course->courseSessions()->firstOrCreate(
      [
        'course_id' => $course->id,
        'session' => $data['session'],
      ],
      $data,
    );

    return $this->res(
      successRes('Course session record created'),
      instRoute('ccd.course-sessions.index', [$course]),
    );
  }

  function edit(
    Institution $institution,
    Course $course,
    CourseSession $courseSession,
  ) {
    return view('ccd.course-sessions.create', [
      'edit' => $courseSession,
      'course' => $courseSession->course,
    ]);
  }

  function update(
    Institution $institution,
    Course $course,
    CourseSession $courseSession,
  ) {
    $data = request()->validate(CourseSession::createRule($courseSession));

    $courseSession->fill($data)->save();

    return $this->res(
      successRes('Course session record updated'),
      instRoute('ccd.course-sessions.index', [$courseSession->course_id]),
    );
  }

  function destroy(
    Institution $institution,
    Course $course,
    CourseSession $courseSession,
  ) {
    abort_if(
      $courseSession->questions()->exists(),
      403,
      'Cannot delete sessions with question(s)',
    );
    $courseSession->delete();

    return $this->res(
      successRes('Course session record deleted'),
      instRoute('course-sessions.index'),
    );
  }
}
