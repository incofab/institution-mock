<?php
namespace App\Http\Controllers\Institutions;

use App\Models\Course;
use App\Models\Event;
use App\Http\Controllers\Controller;
use App\Models\EventCourse;
use App\Models\Institution;
use Illuminate\Http\Request;

class EventCourseController extends Controller
{
    function index(Institution $institution, Event $event)
    {
        $query = $event
            ->eventCourses()
            ->getQuery()
            ->with('courseSession.course');

        return view('institutions.event-courses.index', [
            'allRecords' => $query->latest('id')->get(),
            'event' => $event,
            'courses' => Course::query()
                ->with('courseSessions', fn($q) => $q->latest('session'))
                ->oldest('order')
                ->get(),
        ]);
    }

    function store(Request $request, Institution $institution, Event $event)
    {
        $data = $request->validate([
            'course_session_id' => [
                'required',
                'integer',
                'exists:course_sessions,id',
            ],
            'num_of_questions' => ['nullable', 'integer'],
        ]);
        $event
            ->eventCourses()
            ->updateOrCreate(
                ['course_session_id' => $data['course_session_id']],
                $data,
            );

        return redirect(instRoute('event-courses.index', $event))->with(
            'message',
            'Event course added',
        );
    }

    function multiCreate(Institution $institution, Event $event)
    {
        return view('institutions.event-courses.create-multi-event-courses', [
            'event' => $event,
            'courses' => Course::query()
                ->when(
                    $event->exam_content_id,
                    fn($q, $value) => $q->where('exam_content_id', $value),
                )
                ->with('courseSessions', fn($q) => $q->latest('session'))
                ->oldest('order')
                ->oldest('course_code')
                ->get(),
        ]);
    }

    function multiStore(
        Request $request,
        Institution $institution,
        Event $event,
    ) {
        $validatedData = $request->validate([
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*.num_of_questions' => ['nullable', 'integer'],
            'subjects.*.course_session_id' => [
                'nullable',
                'integer',
                'exists:course_sessions,id',
            ],
        ]);
        $filteredSubjects = array_filter(
            $validatedData['subjects'],
            fn($item) => !empty($item['course_session_id']),
        );
        foreach ($filteredSubjects as $key => $data) {
            $event
                ->eventCourses()
                ->updateOrCreate(
                    ['course_session_id' => $data['course_session_id']],
                    $data,
                );
        }
        return redirect(instRoute('event-courses.index', $event))->with(
            'message',
            'Multiple event courses added',
        );
    }

    function destroy(Institution $institution, EventCourse $eventCourse)
    {
        $eventCourse->delete();
        return redirect(
            instRoute('event-courses.index', [$eventCourse->event_id]),
        )->with('message', 'Event course deleted');
    }
}
