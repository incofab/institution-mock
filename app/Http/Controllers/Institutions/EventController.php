<?php
namespace App\Http\Controllers\Institutions;

use App\Actions\DownloadResult;
use App\Actions\EndExam;
use App\Actions\BuildLicenseInvoice;
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
        $query
          ->withCount('eventCourses')
          ->withCount('exams')
          ->withCount([
            'exams as activated_exams_count' => fn($query) => $query->whereNotNull(
              'exam_activation_id',
            ),
            'exams as unactivated_exams_count' => fn($query) => $query->whereNull(
              'exam_activation_id',
            ),
          ])
          ->latest('id'),
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
    abort_if($event->institution_id !== $institution->id, 404);

    $unactivatedExamCount = $event
      ->exams()
      ->whereNull('exam_activation_id')
      ->count();

    if ($unactivatedExamCount > 0) {
      return back()->with(
        'error',
        'Results cannot be downloaded until all exams in this event have been activated.',
      );
    }

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

  function invoice(Institution $institution, Event $event)
  {
    $invoice = (new BuildLicenseInvoice())->event($institution, $event);

    return Response::make($invoice['content'], 200, [
      'Content-Type' => 'application/pdf',
      'Content-Disposition' => "attachment; filename=\"{$invoice['file_name']}\"",
    ]);
  }

  function evaluateEvent(Institution $institution, Event $event)
  {
    EndExam::make()->endEventExams($event);
    return back()->with('message', 'All Exam results evaluated successfully');
  }
}
