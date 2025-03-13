<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CCD\QuestionController;
use App\Http\Controllers\API as Api;
use App\Http\Controllers\Exam as Exam;
use App\Http\Controllers\Auth as Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->any('/user', function (Request $request) {
    return $request->user();
});

Route::post('exam/start', [Api\ExamController::class, 'startExam'])->name('exam-start');
Route::get('{exam:exam_no}/end-exam', [Api\ExamController::class, 'endExam'])->name('end-exam');

Route::group(['prefix' => 'institutions/{institution}/', 'middleware' => [], 'as' => 'institutions.'], function() {
    Route::any('show-institution', [Api\InstitutionController::class, 'showInstitution'])->name('show-institution');
    Route::any('events', [Api\EventController::class, 'index'])->name('events.index');
    Route::any('events/{event}/show', [Api\EventController::class, 'show'])->name('events.show');
    Route::any('events/{event}/deep-show', [Api\EventController::class, 'deepShow'])->name('events.deep-show');
    Route::any('events/{event}/exams', [Api\ExamController::class, 'index'])->name('events.exams.index');
    Route::any('exams/upload', [Api\ExamController::class, 'uploadEventResult'])->name('exams.upload');
}); 

Route::group(['middleware' => []], function() {
    Route::any('/exam/pause', [Exam\ExamController::class, 'pauseExam']);
    Route::any('/exam/end', [Exam\ExamController::class, 'endExam']);
    Route::any('/exam/submit', [Exam\ExamController::class, 'submitExam']);
});

Route::group(['middleware' => []], function() {
    Route::any('/ccd/institution/{institution_id}/question/create/{sessionId}', [QuestionController::class, 'apiCreate'])->name('api.ccd.question.create');
});

// Route::any('/login', [Auth\LoginController::class, 'apiLogin']);
// Route::any('/register', [Auth\RegisterController::class, 'apiRegister']);

Route::middleware('auth:sanctum')->get('/rough', function (Request $request) {
// Route::get('/rough', function (Request $request) {
    $user = \Auth::guard('sanctum')->user();
    die(json_encode($user));
//     return $request->user();
});

