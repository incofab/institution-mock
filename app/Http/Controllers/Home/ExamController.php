<?php

namespace App\Http\Controllers\Home;

use App\Actions\StartExam;
use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExamController extends Controller
{
  /** @deprecated No longer in use. The examiner react app is used instead */
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
}
