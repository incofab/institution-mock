<?php
namespace App\Http\Controllers\CCD;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadSessionQuestionsRequest;
use App\Models\CourseSession;
use App\Models\Institution;
use App\Models\Question;

class QuestionController extends Controller
{
  function index(Institution $institution, CourseSession $courseSession)
  {
    $query = $courseSession->questions();
    $courseSession->load('course');
    return view('ccd.questions.index', [
      'allRecords' => paginateFromRequest($query),
      'courseSession' => $courseSession,
    ]);
  }

  function create(Institution $institution, CourseSession $courseSession)
  {
    $lastQuestion = $courseSession->questions()->latest('question_no')->first();
    $questionNo = intval($lastQuestion?->question_no) + 1;

    return view('ccd.questions.create-question', [
      'edit' => null,
      'questionNo' => $questionNo,
      'courseSession' => $courseSession,
    ]);
  }

  function storeApi(Institution $institution, CourseSession $courseSession)
  {
    $data = request()->validate(Question::createRule());
    $this->storeQuestion($institution, $courseSession, $data);

    return response()->json(['success' => true]);
  }

  function store(Institution $institution, CourseSession $courseSession)
  {
    $data = request()->validate(Question::createRule());
    $this->storeQuestion($institution, $courseSession, $data);

    return $this->res(
      successRes('Question created'),
      instRoute('ccd.questions.index', [$courseSession]),
    );
  }

  private function storeQuestion(
    Institution $institution,
    CourseSession $courseSession,
    array $validatedData = [],
  ) {
    $question = $courseSession->questions()->updateOrCreate(
      [
        'question_no' => $validatedData['question_no'],
      ],
      $validatedData,
    );

    return $question;
  }

  function edit(Institution $institution, Question $question)
  {
    return view('ccd.questions.create-question', [
      'edit' => $question,
      'courseSession' => $question->courseSession,
      'questionNo' => $question->question_no,
    ]);
  }

  function update(Institution $institution, Question $question)
  {
    $data = request()->validate(Question::createRule($question));

    $question->fill($data)->save();

    return $this->res(
      successRes('Question record updated'),
      instRoute('ccd.questions.index', [$question->course_session_id]),
    );
  }

  function destroy(Institution $institution, Question $question)
  {
    $question->delete();

    return $this->res(
      successRes('Question record deleted'),
      instRoute('ccd.questions.index', $question->course_session_id),
    );
  }

  function uploadQuestionsView(
    Institution $institution,
    CourseSession $courseSession,
  ) {
    return view('ccd.questions.upload-session-questions', [
      'courseSession' => $courseSession,
    ]);
  }

  function uploadQuestionsStore(
    Institution $institution,
    CourseSession $courseSession,
    UploadSessionQuestionsRequest $uploadSessionQuestionsRequest,
  ) {
    $data = $uploadSessionQuestionsRequest->validated();
    foreach ($data['questions'] as $key => $item) {
      $this->storeQuestion($institution, $courseSession, $item);
    }

    return redirect(instRoute('ccd.questions.index', $courseSession))->with(
      'message',
      'Questions uploaded successfully',
    );
  }
}
