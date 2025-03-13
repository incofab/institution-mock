<?php
namespace App\Http\Controllers\Exam;

use Illuminate\Http\Request;
use App\Models\Exam;

class ExamController extends BaseExamController
{
  function viewResult(Request $request, $examNo = null)
  {
    $examNo = $request->input('exam_no') ?? $examNo;
    if (!$examNo) {
      return view('exams.view-result-form');
    }

    /** @var Exam $exam */
    $exam = Exam::where('exam_no', '=', $examNo)
      ->with(['examCourses', 'student', 'event', 'event.institution'])
      ->first();

    if (!$exam) {
      return redirect(route('exams.view-result'))->with(
        'message',
        'Exam not found',
      );
    }

    if (!$exam->isEnded()) {
      return redirect(route('exams.view-result'))->with(
        'error',
        'Exam has not been concluded',
      );
    }

    return view('exams.view-result', [
      'exam' => $exam,
    ]);
  }
}
