<?php
namespace App\Http\Controllers\Institutions;

use App\Actions\DownloadResult;
use App\Actions\EndExam;
use App\Models\Course;
use App\Models\Event;
use App\Http\Controllers\Controller;
use App\Models\ExternalContent;
use Illuminate\Http\Request;
use App\Models\Institution;
use Illuminate\Support\Facades\Response;

class EventController extends Controller
{
  function index(Institution $institution)
  {
    $query = $institution ? $institution->events() : Event::query();
    return view('institutions.events.index', [
      'allRecords' => paginateFromRequest(
        $query->withCount('eventCourses')->latest('id'),
      ),
    ]);
  }

  function create(Institution $institution)
  {
    return view('institutions.events.create', [
      'edit' => null,
      'subjects' => Course::query()->with('courseSessions')->get(),
      'externalContents' => ExternalContent::all(),
    ]);
  }

  function store(Institution $institution, Request $request)
  {
    $data = $request->validate(Event::ruleCreate());
    $institution->events()->create($data);
    return redirect(instRoute('events.index'))->with(
      'message',
      'Event recorded successfully',
    );
  }

  function edit(Institution $institution, Event $event, Request $request)
  {
    return view('institutions.events.create', [
      'edit' => $event,
    ]);
  }

  function update(Institution $institution, Event $event, Request $request)
  {
    $data = $request->validate(Event::ruleCreate($event));
    $event->fill($data)->save();
    return redirect(instRoute('events.index'))->with(
      'success',
      'Record updated',
    );
  }

  function suspend(Institution $institution, Event $event)
  {
    $event->update(['status' => 'suspended']);
    return redirect(instRoute('events.index'))->with(
      'message',
      'Event has been suspended',
    );
  }

  function unSuspend(Institution $institution, Event $event)
  {
    $event->update(['status' => 'active']);
    return redirect(instRoute('events.index'))->with(
      'message',
      'Event has been unsuspended',
    );
  }

  function destroy(Institution $institution, Event $event)
  {
    abort_if($event->exams()->exists(), 401, 'This event contains some exams');
    $event->eventCourses()->delete();
    $event->delete();

    return redirect(instRoute('events.index'))->with(
      'message',
      'Event deleted successfully',
    );
  }

  function show(Institution $institution, Event $event)
  {
    $event->load('eventCourses.courseSession.course');
    return view('institutions.events.show', [
      'event' => $event,
      'eventCourses' => $event->getEventCourses(),
    ]);
  }

  function download(Institution $institution, Event $event)
  {
    $event->load('exams.student', 'exams.examCourses');
    $excelWriter = DownloadResult::run($event);
    $fileName = sanitizeFilename("{$event->title}-exams.xlsx");
    $tempFilePath = storage_path("app/public/{$fileName}");
    // Save to a temporary file
    $excelWriter->save($tempFilePath);

    return Response::download($tempFilePath, $fileName)->deleteFileAfterSend(
      true,
    );
  }

  function evaluateEvent(Institution $institution, Event $event)
  {
    EndExam::make()->endEventExams($event);
    return back()->with('message', 'All Exam results evaluated successfully');
  }
}
