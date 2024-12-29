<?php

namespace App\Http\Controllers\Home;

use App\Actions\ExamHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExamController extends Controller
{
  /*
    function index()
    {
        $ret = $this->examHelper->list(Auth::user()->id);

        // Show a list of Exam Content Bodies
        return view('users.exams.index', [
            'all' => $ret['all'],
            'count' => $ret['count'],
        ]);
    }

    public function selectExamBody()
    {
        $allExamBody = $this->examHelper->getAllExamBody();

        $subs = \App\Helpers\SubscriptionHelper::getUserSubscriptionsByContentId(
            Auth::user(),
        );

        return view('home.exams.select_exam_body', [
            'allExamBody' => $allExamBody,
            'active_subs' => $subs,
        ]);
    }

    public function selectExamSubjects($examContentId)
    {
        $courses = $this->examHelper->getCoursesWithSessions($examContentId);

        return view('home.exams.select_exam_subject', [
            'courses' => $courses,
            'examContentId' => $examContentId,
        ]);
    }

    public function registerExam(Request $request)
    {
        $post = $request->all();
        //         dDie($post);
        //         $subjects = $post['exam_subjects'];
        $hrs = (int) $post['hours'];
        $mins = (int) $post['mins'];
        $durationInSecs = (60 * $hrs + $mins) * 60;
        //         $examContentId = $post['exam_content_id'];

        $post['duration'] = $durationInSecs;

        $user = Auth::user();
        $ret = $this->examHelper->registerExam($post, $user);

        if (!$ret[SUCCESSFUL]) {
            return Redirect::back()
                ->with('error', $ret[MESSAGE])
                ->withErrors(Arr::get($ret, 'val'));
        }

        $exam = $ret['data'];

        return redirect(route('home.start-exam', $exam['exam_no']));
    }
*/
  function startExamView(Request $request)
  {
    if ($request->has('exam_no')) {
      return view('exam.exam-page');
    }
    return view('exam.exam-login');
  }

  /**
   * Starts or resumes an exam
   */
  function startExam(Request $request)
  {
    $request->validate([
      'exam_no' => ['required', 'string'],
      'student_code' => ['nullable', 'string'],
    ]);
    // info($request->all());
    // dd('here');
    $res = ExamHelper::make()->getExamStartupData(
      $request->exam_no,
      $request->student_code,
      true,
    );

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
  /*
    function pauseExam(Request $request)
    {
        //         dlog($request->all());
        $ret = $this->examHelper->pauseExam(
            $request->input('exam_no'),
            Auth::user(),
        );

        return json_encode($ret);
    }

    function submitExam(Request $request)
    {
        //         $content = file_get_contents('php://input');
        //         http_response_code("200");
        //         dlog($content);
        //         dlog($request->all());
        //         die('djsnd');
        //         return json_encode([SUCCESSFUL => true, MESSAGE => 'Here']);
        $attempts = $request->input('attempts', []);
        $examNo = $request->input('exam_no');
        $userId = $request->input('user_id');

        $ret = $this->examHandler->attemptQuestion($attempts, $examNo, $userId);

        $ret = $this->examHelper->endExam($examNo, $userId);

        return json_encode($ret);
    }

    function viewResult($examNo)
    {
        $exam = \App\Models\Exam::where('exam_no', '=', $examNo)
            ->with(['examSubjects', 'user'])
            ->first();

        if (!$exam) {
            return redirect(route('user-dashboard'))->with(
                'error',
                'Exam not found',
            );
        }
        if (
            $exam->status !== STATUS_ENDED &&
            $exam->status !== STATUS_EXPIRED
        ) {
            return redirect(route('user-dashboard'))->with(
                'error',
                'Exam has not been concluded',
            );
        }

        $examSubjects = $exam['examSubjects'];

        if (empty($examSubjects) || !$examSubjects->first()) {
            return redirect(route('user-dashboard'))->with(
                'error',
                'No subjects recorded for this exam',
            );
        }

        $user = $exam->user;

        $examBody = $examSubjects
            ->first()
            ->course()
            ->first()
            ->examContent()
            ->first();

        return view('home.exams.result', [
            'exam' => $exam,
            'examSubjects' => $examSubjects,
            'user' => $user,
            'examBody' => $examBody,
        ]);
    }

    function previewExamResult($examNo)
    {
        $exam = Exam::where('exam_no', '=', $examNo)
            ->with(['examSubjects', 'user'])
            ->first();

        if (!$exam) {
            return redirect(route('user-dashboard'))->with(
                'error',
                'No subjects recorded for this exam',
            );
        }

        $user = $exam['user'];
        $allAttempts = [];
        $getContent = $this->examHandler->getContent(
            $examNo,
            $user->id ?? null,
        );

        if ($getContent['success']) {
            $allAttempts = Arr::get($getContent['content'], 'attempts');
        }

        return view('home/exam/preview_exam_result', [
            'exam' => $exam,
            'examSubjects' => $exam['examSubjects'],
            'allAttempts' => $allAttempts,
        ]);
    }
    */
}
