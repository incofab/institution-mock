<?php
namespace App\Http\Controllers\Institutions;

use App\Actions\RegisterExam;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Institution;
use App\Models\Student;
use Illuminate\Http\Request;

class ExamController extends Controller
{
  function index(Institution $institution, Event $event)
  {
    return view('institutions.exams.index', [
      'allRecords' => $event
        ->exams()
        ->with('examCourses.courseSession.course')
        ->get(),
      'allEvents' => Event::query()
        ->with('eventCourses.courseSession.course')
        ->get(),
      'event' => $event,
    ]);
  }

  function create(Institution $institution, Student $student = null)
  {
    $events = Event::query()->active()->get();
    $students = Student::all();
    $event = Event::where('id', request('event_id'))->first();
    $event?->load('eventCourses.courseSession.course');
    return view('institutions.exams.create', [
      'events' => $events,
      'students' => $students,
      'student' => $student,
      'event' => $event,
      'edit' => null,
      'eventCourses' => $event?->getEventCourses(),
      // $event?->eventCourses()->with('courseSession.course')->get() ?? [],
    ]);
  }

  private function validateEventCourseSessions(Event $event)
  {
    return function ($attr, $value, $fail) use ($event) {
      $exists = $event
        ->getEventCourses()
        ->where('course_session_id', $value)
        ->exists();
      if (!$exists) {
        $fail("$attr is not part of this event {$event->title}");
      }
    };
  }

  function store(Request $request, Institution $institution)
  {
    $event = Event::query()->findOrFail($request->event_id);
    $data = $request->validate([
      'event_id' => ['required', 'exists:events,id'],
      'student_id' => ['required', 'exists:students,id'],
      'course_session_ids' => ['required', 'array', 'min:1'],
      'course_session_ids.*' => [
        'required',
        $this->validateEventCourseSessions($event),
      ],
    ]);

    $event->load('eventCourses.courseSession.course');
    (new RegisterExam($event))->run(
      $data['student_id'],
      $data['course_session_ids'],
    );

    return redirect()->back()->with('message', 'Exam registered');
  }

  function createGradeExam(Institution $institution, Event $event)
  {
    $event->load('eventCourses.courseSession.course');
    return view('institutions.exams.create-grade-exam', [
      'event' => $event,
      'eventCourses' => $event->getEventCourses(),
      'grades' => Grade::all(),
    ]);
  }

  function storeGradeExam(
    Request $request,
    Institution $institution,
    Event $event,
  ) {
    $data = $request->validate([
      'grade_id' => 'exists:grades,id',
      'course_session_ids' => ['required', 'array', 'min:1'],
      'course_session_ids' => [
        'required',
        $this->validateEventCourseSessions($event),
      ],
    ]);

    $students = Student::where('grade_id', $data['grade_id'])->get();
    $event->load('eventCourses.courseSession.course');
    $obj = new RegisterExam($event);
    foreach ($students as $student) {
      $obj->run($student->id, $data['course_session_ids'], true);
    }

    return redirect(instRoute('exams.index', $event))->with(
      'message',
      'Exams recorded',
    );
  }

  function multiStoreExam(
    Request $request,
    Institution $institution,
    Event $event,
  ) {
    $data = $request->validate([
      'items' => ['required', 'array', 'min:1'],
      'items.*.student_id' => ['required', 'exists:students,id'],
      'items.*.course_session_ids' => ['required', 'array', 'min:1'],
      'items.*.course_session_ids.*.course_session_id' => [
        'required',
        $this->validateEventCourseSessions($event),
      ],
    ]);

    $event->load('eventCourses.courseSession.course');
    $obj = new RegisterExam($event);
    foreach ($data['items'] as $key => $item) {
      $obj->run($item['student_id'], $item['course_session_ids'], true);
    }
    return redirect(instRoute('exams.index', $event))->with(
      'message',
      'Exams recorded',
    );
  }

  // function forceEndExam(Institution $institution, Exam $exam)
  // {
  //     // $ret = $this->examRepository->endExam($examNo, $student);

  //     return redirect(route('institutions.exams.index'))->with(
  //         'message',
  //         'Exam ended',
  //     );
  // }

  function destroy(Institution $institution, Exam $exam)
  {
    $exam->delete();

    return redirect(instRoute('exams.index', $exam->event))->with(
      'message',
      'Exam deleted',
    );
  }
  /*
    function extendExamTimeView($institutionId, $examNo)
    {
        $exam = Exam::whereExam_no($examNo)
            ->with('student', 'event')
            ->firstOrFail();

        $event = $exam->event;
        $startTime = \Carbon\Carbon::parse($exam['start_time']);
        $pausedTime = \Carbon\Carbon::parse($exam['pause_time']);
        $endTime = \Carbon\Carbon::parse($exam['end_time']);

        if ($exam['status'] === STATUS_PAUSED) {
            $timeElapsed = $startTime->diffInSeconds($pausedTime);
            $timeRemaining = $event['duration'] - $timeElapsed;
        } else {
            $timeRemaining = \Carbon\Carbon::now()->diffInSeconds(
                $endTime,
                false,
            );
        }

        if ($timeRemaining < 1) {
            $timeRemaining = 0;
        }

        return view('institutions.exams.extend_time', [
            'exam' => $exam,
            'student' => $exam->student,
            'event' => $event,
            'timeRemaining' => $timeRemaining,
        ]);
    }

    function extendExamTime($institutionId, $examNo, Request $request)
    {
        $exam = Exam::whereExam_no($examNo)
            ->with(['student', 'event'])
            ->firstOrFail();

        if ($exam['status'] !== STATUS_PAUSED && empty($exam['end_time'])) {
            return redirect(
                route('institutions.exams.index', $institutionId),
            )->with('error', 'Exam has not started');
        }

        $event = $exam['event'];

        $time = (int) $request->extend_time;

        $ret = $this->examRepository->extendExam($exam, $time);

        if (!$ret[SUCCESSFUL]) {
            return $this->redirect(redirect()->back(), $ret);
        }

        return redirect(route('institutions.exams.index', $institutionId))->with(
            'message',
            $ret[MESSAGE],
        );
    }
*/

  function viewExamResult($institutionId, $examNo, $studentID)
  {
    return $this->displayExamResult($examNo, $studentID);
  }
}
