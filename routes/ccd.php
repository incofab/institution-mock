<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CCD as CCD;

Route::resource('/exam-contents', CCD\ExamContentController::class)->except('show');

Route::get('/courses/index/{examContent?}', [CCD\CourseController::class, 'index'])->name('courses.index');
Route::resource('/courses', CCD\CourseController::class)->except('show', 'index');

Route::get('courses/{course}/export-content', [CCD\UploadContentController::class, 'exportCourse'])->name('courses.export-content');
Route::get('courses/{course}/upload-content', [CCD\UploadContentController::class, 'uploadContentView'])->name('courses.upload-content.create');
Route::post('courses/{course}/upload-content', [CCD\UploadContentController::class, 'uploadContent'])->name('courses.upload-content.store');    

// Route::get('/course-sessions/{course?}', [CCD\CourseSession\CourseSessionController::class, 'index'])->name('course-sessions.index');
Route::resource('courses/{course}/course-sessions', CCD\CourseSession\CourseSessionController::class)->except(['show']);

// Route::get('course-sessions/index/{course?}', [CCD\CourseSession\CourseSessionController::class, 'index'])->name('course-sessions.index');
// Route::get('course-sessions/{courseSession}/delete', [CCD\CourseSession\CourseSessionController::class, 'destroy'])->name('course-sessions.destroy');
// Route::get('course-sessions/{course}/create', [CCD\CourseSession\CourseSessionController::class, 'create'])->name('course-sessions.create');
// Route::post('course-sessions/{course}/store', [CCD\CourseSession\CourseSessionController::class, 'store'])->name('course-sessions.store');

Route::get('couse-sessions/{courseSession}/passages/index/{passage?}', [CCD\CourseSession\PassageController::class, 'index'])->name('passages.index');
Route::post('course-sessions/{courseSession}/passages/store', [CCD\CourseSession\PassageController::class, 'store'])->name('passages.store');
Route::put('passages/{passage}/update', [CCD\CourseSession\PassageController::class, 'update'])->name('passages.update');
Route::get('passages/{passage}/delete', [CCD\CourseSession\PassageController::class, 'destroy'])->name('passages.destroy');

Route::get('couse-sessions/{courseSession}/instructions/index/{instruction?}', [CCD\CourseSession\InstructionController::class, 'index'])->name('instructions.index');
Route::post('course-sessions/{courseSession}/instructions/store', [CCD\CourseSession\InstructionController::class, 'store'])->name('instructions.store');
Route::put('instructions/{instruction}/update', [CCD\CourseSession\InstructionController::class, 'update'])->name('instructions.update');
Route::get('instructions/{instruction}/delete', [CCD\CourseSession\InstructionController::class, 'destroy'])->name('instructions.destroy');

// Question
Route::get('questions/couse-sessions/{courseSession}/index', [CCD\QuestionController::class, 'index'])->name('questions.index');
Route::get('questions/course-sessions/{courseSession}/create', [CCD\QuestionController::class, 'create'])->name('questions.create');
Route::post('questions/course-sessions/{courseSession}/store', [CCD\QuestionController::class, 'store'])->name('questions.store');
Route::get('questions/{question}/edit', [CCD\QuestionController::class, 'edit'])->name('questions.edit');
Route::put('questions/{question}/update', [CCD\QuestionController::class, 'update'])->name('questions.update');
Route::get('questions/{question}/delete', [CCD\QuestionController::class, 'destroy'])->name('questions.destroy');

Route::get('questions/course-sessions/{courseSession}/upload', [CCD\QuestionController::class, 'uploadQuestionsView'])->name('questions.upload.create');
Route::post('questions/course-sessions/{courseSession}/upload', [CCD\QuestionController::class, 'uploadQuestionsStore'])->name('questions.upload.store');
// Route::get('question-corrections/{questionCorrection}/mark-as-resolved', [CCD\QuestionController::class, 'markQuestionCorrectionAsResolved'])->name('question-corrections.mark-as-resolved');
    



//----- API --------

Route::post(
    'questions/course-sessions/{courseSession}/image-upload', CCD\UploadTinyMceImageController::class
)->name('api.questions.image-upload');

Route::post('api/questions/course-sessions/{courseSession}/store', [CCD\QuestionController::class, 'storeApi'])->name('api.questions.store');
