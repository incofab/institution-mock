<?php
namespace App\Http\Controllers\Institutions;

use App\Models\Course;
use App\Models\Event;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Institution;

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
        $data = $request->validate(Event::ruleCreate());
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
        abort_if(
            $event->exams()->exists(),
            401,
            'This event contains some exams',
        );
        $event->eventCourses()->delete();
        $event->delete();

        return redirect(instRoute('events.index'))->with(
            'message',
            'Event deleted successfully',
        );
    }

    // function show(Institution $institution, Event $event)
    // {
    //     $event->load([
    //         'eventSubjects',
    //         'eventSubjects.course',
    //         'eventSubjects.session',
    //     ]);
    //     return view('institutions.events.show', [
    //         'event' => $event,
    //         'eventSubjects' => $event->eventSubjects,
    //     ]);
    // }

    function eventResult($institutionId, $event_id)
    {
        $ret = $this->eventsHelper->getEventResult($event_id, $institutionId);

        if (!$ret[SUCCESSFUL]) {
            return redirect(instRoute('events.index'))->with(
                'message',
                $ret[MESSAGE],
            );
        }

        return view('institutions.events.event_result', [
            'allRecords' => $ret['result_list'],
            'event' => $ret['event'],
        ]);
    }

    /** @deprecated*/
    function smsEventResult($event_id)
    {
        if (!$_POST) {
            /** @var \App\Models\Event $event */
            $event = $this->eventsModel
                ->where(TABLE_ID, '=', $event_id)
                ->where(CENTER_CODE, '=', $this->getCenterData()[CENTER_CODE])
                ->first();

            if (!$event) {
                $this->session->flash('error', 'Event not found');

                redirect_(getAddr('center_view_all_events'));
            }

            return view('centers/events/sms_event_result', [
                'event' => $event,
                'post' => $this->session->getFlash('post', []),
            ]);
        }

        ini_set('max_execution_time', 960);

        $ret = $this->eventsHelper->smsResult(
            $event_id,
            array_get($_POST, USERNAME),
            array_get($_POST, PASSWORD),
            $this->getCenterData()[CENTER_CODE],
            getAddr('center_view_all_events'),
        );

        if (!$ret[SUCCESSFUL]) {
            $this->session->flash('error', $ret[MESSAGE]);

            $this->session->flash('post', $_POST);

            redirect_(null);
        }

        $this->session->flash(SUCCESSFUL, $ret[MESSAGE]);

        redirect_(getAddr('center_view_all_events'));
    }
    /** @deprecated*/
    function smsInvite($event_id)
    {
        /** @var \App\Models\Event $event */
        $event = $this->eventsModel
            ->where(TABLE_ID, '=', $event_id)
            ->where(CENTER_CODE, '=', $this->getCenterData()[CENTER_CODE])
            ->first();

        if (!$event) {
            $this->session->flash('error', 'Event not found');

            redirect_(getAddr('center_view_all_events'));
        }

        if (!$_POST) {
            return view('centers/events/sms_invite', [
                'event' => $event,
                'post' => $this->session->getFlash('post', []),
            ]);
        }

        ini_set('max_execution_time', 960);

        $ret = $this->eventsHelper->smsInvite(
            $event,
            array_get($_POST, USERNAME),
            array_get($_POST, PASSWORD),
            array_get($_POST, 'time'),
            getAddr('center_view_all_events'),
        );

        if (!$ret[SUCCESSFUL]) {
            $this->session->flash('error', $ret[MESSAGE]);

            $this->session->flash('post', $_POST);

            redirect_(null);
        }

        $this->session->flash(SUCCESSFUL, $ret[MESSAGE]);

        redirect_(getAddr('center_view_all_events'));
    }

    function downloadEventResult(Institution $institution, Event $event)
    {
        $ret = $this->eventsHelper->getEventResult($event_id, $institutionId);

        if (!$ret[SUCCESSFUL]) {
            return redirect(instRoute('events.index'))->with(
                'message',
                'Event not found',
            );
        }

        $resultList = $ret['result_list'];

        $event = $ret['event'];

        $headers = [
            'S/No',
            'Name',
            'Student ID',
            'Exam No',
            'Subjects',
            'Correct Answers',
            'Score',
        ];

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(
            $this->resultsDir . 'resultsheet-template.xlsx',
        );

        $sheetData = $spreadsheet->getActiveSheet();

        $i = 1;
        foreach ($headers as $value) {
            $sheetData->setCellValueByColumnAndRow($i, 1, $value);
            $i++;
        }

        $eventSubjects = $event->eventSubjects;
        foreach ($eventSubjects as $eventSubject) {
            $sheetData->setCellValueByColumnAndRow(
                $i,
                1,
                $eventSubject->course->course_code,
            );
            $i++;
        }

        $j = 2;
        $serialNo = 1;
        foreach ($resultList as $result) {
            $sheetData->setCellValueByColumnAndRow(1, $j, $serialNo);
            $sheetData->setCellValueByColumnAndRow(2, $j, $result['name']);
            $sheetData->setCellValueByColumnAndRow(
                3,
                $j,
                $result['student_id'],
            );
            $sheetData->setCellValueByColumnAndRow(4, $j, $result['exam_no']);
            $sheetData->setCellValueByColumnAndRow(5, $j, $result['subjects']);
            $sheetData->setCellValueByColumnAndRow(
                6,
                $j,
                "{$result['total_score']}/{$result['total_num_of_questions']}",
            );
            $sheetData->setCellValueByColumnAndRow(
                7,
                $j,
                "{$result['total_score_percent']}/{$result['total_num_of_questions_percent']}",
            );

            $i = 8;
            $subjectsAndScores = $result['subjects_and_scores'];
            foreach ($eventSubjects as $eventSubject) {
                $courseCode = $eventSubject->course->course_code;

                if (!empty($subjectsAndScores[$courseCode])) {
                    $sheetData->setCellValueByColumnAndRow(
                        $i,
                        $j,
                        $subjectsAndScores[$courseCode]['score'],
                    );
                }

                $i++;
            }

            $j++;
            $serialNo++;
        }

        $title = str_replace(' ', '_', $event['title']);

        $fileName = "$title--{$event['id']}--results.xlsx";

        // Output the file so that user can download
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=$fileName");
        header('Cache-Control:max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter(
            $spreadsheet,
            'Xlsx',
        );
        $writer->save('php://output');

        exit();
    }
}
