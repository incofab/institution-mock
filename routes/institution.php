<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Institutions as Inst;

Route::get('/dashboard', [Inst\InstitutionController::class, 'index'])->name('dashboard');
Route::get('/activation-history', [Inst\InstitutionController::class, 'activationHistory'])
->name('activation-history');
Route::get('/funding-history', [Inst\InstitutionController::class, 'fundingHistory'])
->name('funding-history');
Route::get('/fund-licenses', [Inst\InstitutionController::class, 'fundLicensesView'])
->name('fund-licenses.create');
Route::post('/fund-licenses', [Inst\InstitutionController::class, 'fundLicensesStore'])
->name('fund-licenses.store');
Route::get('/fund-licenses/{gateway}/callback', [Inst\InstitutionController::class, 'fundLicensesCallback'])
->name('fund-licenses.callback');

Route::get('/users', [Inst\InstitutionUserController::class, 'index'])
->name('users.index');
Route::post('/users', [Inst\InstitutionUserController::class, 'store'])
->name('users.store');

Route::get('/events/{event}/suspend', [Inst\EventController::class, 'suspend'])
->name('events.suspend');
Route::get('/events/{event}/unsuspend', [Inst\EventController::class, 'unSuspend'])
->name('events.unsuspend');
Route::get('/events/{event}/download', [Inst\EventController::class, 'download'])
->name('events.download');
Route::get('/events/{event}/evaluate', [Inst\EventController::class, 'evaluateEvent'])
->name('events.evaluate');
Route::get('/events/{event}/delete', [Inst\EventController::class, 'destroy'])
->name('events.destroy');
Route::resource('/events', Inst\EventController::class)->except('show', 'destroy');

Route::get('/event-courses/events/{event}/index', [Inst\EventCourseController::class, 'index'])
->name('event-courses.index');
Route::post('/event-courses/events/{event}/store', [Inst\EventCourseController::class, 'store'])
->name('event-courses.store');
Route::get('/event-courses/events/{event}/multi-create', [Inst\EventCourseController::class, 'multiCreate'])
->name('event-courses.multi-create');
Route::post('/event-courses/events/{event}/multi-store', [Inst\EventCourseController::class, 'multiStore'])
->name('event-courses.multi-store');
Route::get('/event-courses/{eventCourse}/delete', [Inst\EventCourseController::class, 'destroy'])
->name('event-courses.destroy');

Route::get('/exams/events/{event}/index', [Inst\ExamController::class, 'index'])
->name('exams.index');
Route::get('/exams/create/{student?}', [Inst\ExamController::class, 'create'])
->name('exams.create');
Route::post('/exams/store', [Inst\ExamController::class, 'store'])
->name('exams.store');
Route::get('/exams/events/{event}/create-grade-exam', [Inst\ExamController::class, 'createGradeExam'])
->name('exams.events.grades.create');
Route::post('/exams/events/{event}/store-grade-exam', [Inst\ExamController::class, 'storeGradeExam'])
->name('exams.events.grades.store');
Route::post('/exams/events/{event}/multi-store-exam', [Inst\ExamController::class, 'multiStoreExam'])
->name('exams.multi-store-exam');
Route::post('/exams/events/{event}/activate', [Inst\ExamController::class, 'activateEvent'])
->name('exams.events.activate');
Route::post('/exams/{exam}/activate', [Inst\ExamController::class, 'activateExam'])
->name('exams.activate');
Route::delete('/exams/{exam}/delete', [Inst\ExamController::class, 'destroy'])
->name('exams.destroy');
Route::get('exams/{exam}/evaluate', [Inst\ExamController::class, 'evaluateExam'])->name('exams.evaluate');
Route::get('exams/{exam}/extend-time', [Inst\ExamController::class, 'extentTimeView'])->name('exams.extend-time');
Route::post('exams/{exam}/extend-time', [Inst\ExamController::class, 'extentTimeStore'])->name('exams.extend-time.store');

Route::resource('/grades', Inst\GradeController::class);

Route::get('/students/multi-create', [Inst\StudentController::class, 'multiCreate'])
->name('students.multi-create');
Route::post('/students/multi-store', [Inst\StudentController::class, 'multiStore'])
->name('students.multi-store');
Route::post('/students/multi-delete', [Inst\StudentController::class, 'multiDelete'])
->name('students.multi-delete');
Route::get('/students/upload-create', [Inst\StudentController::class, 'uploadCreate'])
->name('students.upload.create');
Route::post('/students/upload-store', [Inst\StudentController::class, 'uploadStore'])
->name('students.upload.store');
Route::get('/students/download-template', [Inst\StudentController::class, 'downloadTemplateExcel'])
->name('students.download-template');
Route::resource('/students', Inst\StudentController::class);



/*
    // CCD Course
    Route::resource('/ccd/institutions/{institution}/course', CourseController::class, ['as' => 'ccd'])
    ->except(['show', 'destroy']);
    Route::get('/ccd/institutions/{institution}/course/{courseId}/delete', [CourseController::class, 'delete'])->name('ccd.course.delete');

    // CCD Session
    Route::resource('/ccd/institutions/{institution}/session', SessionController::class, ['as' => 'ccd'])
    ->except(['index', 'create', 'store']);
    Route::any('/ccd/institutions/{institution}/session/preview/{id}', [SessionController::class, 'preview'])->name('ccd.session.preview');
    Route::get('/ccd/institutions/{institution}/sessions/{courseId}', [SessionController::class, 'index'])->name('ccd.session.index');
    Route::get('/ccd/institutions/{institution}/session/create/{courseId}', [SessionController::class, 'create'])->name('ccd.session.create');
    Route::post('/ccd/institutions/{institution}/session/store/{courseId}', [SessionController::class, 'store'])->name('ccd.session.store');
    Route::get('/ccd/institutions/{institution}/session/store/{courseId}/upload-excel-questions/{courseSessionId}', [SessionController::class, 'uploadExcelQuestionCreate'])->name('ccd.session.upload-excel-question');
    Route::post('/ccd/institutions/{institution}/session/store/{courseId}/upload-excel-questions/{courseSessionId}', [SessionController::class, 'uploadExcelQuestionStore']);
    
    // CCD Question
    Route::resource('/ccd/institutions/{institution}/question', QuestionController::class, ['as' => 'ccd'])
    ->except(['index', 'create', 'store']);
    Route::get('/ccd/institutions/{institution}/questions/{sessionId}', [QuestionController::class, 'index'])->name('ccd.question.index');
    Route::get('/ccd/institutions/{institution}/question/create/{sessionId}', [QuestionController::class, 'create'])->name('ccd.question.create');
    Route::post('/ccd/institutions/{institution}/question/create/{sessionId}', [QuestionController::class, 'store'])->name('ccd.question.store');
    Route::any('/ccd/image-upload/institutions/{institution}/question/{courseId}/{sessionId}', [\App\Http\Controllers\CCD\HomeController::class, 'uploadImage'])->name('ccd.question.upload-image');

    //Content Upload
    Route::get('/ccd/institutions/{institution}/course/upload/{courseId}', [\App\Http\Controllers\CCD\CourseUploadController::class, 'uploadCourseView'])->name('ccd.course.upload');
    Route::post('/ccd/institutions/{institution}/course/upload/{courseId}', [\App\Http\Controllers\CCD\CourseUploadController::class, 'uploadCourse'])->name('ccd.course.upload.store');
    Route::get('/ccd/institutions/{institution}/course/uninstall/{courseId}', [\App\Http\Controllers\CCD\CourseUploadController::class, 'unInstallCourse'])->name('ccd.course.uninstall');
    Route::get('/ccd/institutions/{institution}/course/export/{courseId}', [\App\Http\Controllers\CCD\CourseUploadController::class, 'exportCourse'])->name('ccd.course.export');
*/
