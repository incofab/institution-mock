<?php
namespace App\Http\Controllers\Institutions;

use App\Actions\EndExam;
use App\Actions\ExtendExamTime;
use App\Actions\RegisterExam;
use App\Enums\ExamStatus;
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
    $query = Exam::query()->where('event_id', $event->id);
    $allExamsCount = (clone $query)->count();
    $startedExamsCount = (clone $query)
      ->where('status', '!=', ExamStatus::Pending)
      ->count();
    $pendingExamsCount = (clone $query)
      ->where('status', ExamStatus::Pending)
      ->count();
    return view('institutions.exams.index', [
      'allRecords' => $event
        ->exams()
        ->with('examCourses.courseSession.course')
        ->get(),
      'allEvents' => Event::query()
        ->with('eventCourses.courseSession.course')
        ->get(),
      'event' => $event,
      'allExamsCount' => $allExamsCount,
      'startedExamsCount' => $startedExamsCount,
      'pendingExamsCount' => $pendingExamsCount,
    ]);
  }

  function create(Institution $institution, Student|null $student = null)
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
        ->filter(fn($item) => $item['course_session_id'] == intval($value))
        ->first();
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

    return redirect(instRoute('exams.index', $event))->with(
      'message',
      'Exam registered',
    );
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
      'course_session_ids.*' => [
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

  function destroy(Institution $institution, Exam $exam)
  {
    $exam->delete();

    return redirect(instRoute('exams.index', $exam->event))->with(
      'message',
      'Exam deleted',
    );
  }

  function evaluateExam(Institution $institution, Exam $exam)
  {
    EndExam::make()->endExam($exam);
    return back()->with('message', 'Exam result evaluated successfully');
  }

  function extentTimeView(Institution $institution, Exam $exam)
  {
    return view('institutions.exams.extend-time', ['exam' => $exam]);
  }

  function extentTimeStore(
    Institution $institution,
    Exam $exam,
    Request $request,
  ) {
    $request->validate(['duration' => ['required', 'integer', 'min:1']]);
    ExtendExamTime::make($exam)->run($request->duration);
    return redirect(instRoute('exams.index', $exam->event_id))->with(
      'message',
      "Exam time extended by {$request->duration} mins",
    );
  }
}
