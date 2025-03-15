<?php
namespace App\Http\Controllers\API;

use App\Actions\EndExam;
use App\Actions\StartExam;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Exam;
use App\Models\ExamCourse;
use App\Models\Institution;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExamController extends Controller
{
  function index(Institution $institution, Event $event)
  {
    $exams = $event
      ->exams()
      ->getQuery()
      ->with('examCourses.courseSession.course', 'student')
      ->get();
    // info($event->exams()->get()->first()->examCourses()->get());
    // info('$exams');
    // info($exams);
    return $this->apiSuccessRes($exams);
  }

  function startExam(Request $request)
  {
    $request->validate([
      'exam_no' => ['required', 'string'],
      'student_code' => ['nullable', 'string'],
    ]);
    $exam = Exam::query()
      ->where('exam_no', $request->exam_no)
      ->with('event')
      ->first();
    if (!$exam) {
      return throw ValidationException::withMessages([
        'exam_no' => 'Exam record not found',
      ]);
    }
    $res = StartExam::make($exam)->getExamStartupData();

    if ($res->isNotSuccessful()) {
      return $this->apiFailRes([], $res->getMessage());
    }

    $exam = $res->exam;
    return $this->apiSuccessRes([
      'exam_track' => $res->exam_track,
      'exam' => $exam,
      'timeRemaining' => $exam->getTimeRemaining(),
      'baseUrl' => url('/'),
    ]);
  }

  function uploadEventResult(Institution $institution, Request $request)
  {
    $request->validate([
      'exams' => 'required|array',
      'exams.*.id' => ['required', 'integer', 'exists:exams,id'],
      'exams.*.exam_courses' => ['required', 'array', 'min:1'],
      'exams.*.exam_courses.*.course_session_id' => 'required|integer',
      'exams.*.exam_courses.*.score' => ['nullable', 'numeric'],
    ]);
    $exams = $request->input('exams');

    DB::beginTransaction();

    foreach ($exams as $exam) {
      if (empty($exam['attempts'])) {
        continue;
      }
      $examCourses = $exam['exam_courses'];
      foreach ($examCourses as $examCourse) {
        ExamCourse::query()->updateOrCreate(
          [
            'exam_id' => $exam['id'],
            'course_session_id' => $examCourse['course_session_id'],
          ],
          collect($examCourse)
            ->only(
              'score',
              'course_code',
              'session',
              'status',
              'num_of_questions',
            )
            ->toArray(),
        );
      }

      Exam::find($exam['id'])->update(
        collect($exam)
          ->only(
            'time_remaining',
            'start_time',
            'pause_time',
            'end_time',
            'score',
            'status',
            'num_of_questions',
            'attempts',
          )
          ->toArray(),
      );
    }

    DB::commit();

    return $this->apiSuccessRes([], 'Exam records updated');
  }

  function endExam(Exam $exam)
  {
    // info("Exam exam called {$exam->exam_no}");
    EndExam::make()->endExam($exam);
    return $this->apiSuccessRes([], 'Exam ended successfully');
  }
}
