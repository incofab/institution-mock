<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Home\ExamController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Home\CallbackController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Institution\EventController;
use App\Http\Controllers\Institution\StudentController;
use App\Http\Controllers\CCD\CourseController;
use App\Http\Controllers\CCD\SessionController;
use App\Http\Controllers\CCD\QuestionController;
use App\Http\Controllers\Institution\InstitutionController;
use Illuminate\Http\Request;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Institution\GradeController;
use App\Http\Controllers\Admin as Admin;
use App\Http\Controllers\Home as Home;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('home.contact');
//     return view('home.index');
// });

Auth::routes();
Route::any('/logout', '\App\Http\Controllers\Auth\LoginController@logout')->name('logout');

Route::get('/', [HomeController::class, 'index'])->name('home');
// Route::any('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy-policy');
/*
Route::any('/home/callback/paystack', [CallbackController::class, 'paystackCallback'])->name('paystack-callback');
Route::any('/home/callback/rave', [CallbackController::class, 'raveCallback'])->name('rave-callback');
Route::any('/home/callback/monnify', [CallbackController::class, 'monnifyCallback'])->name('monnify-callback');
Route::any('/home/card-payment/validate-reference', [CallbackController::class, 'checkPaymentStatus'])->name('validate-payment-reference');
Route::get('/home/monnify/check-out', [HomeController::class, 'monnifyCheckout'])->name('monnify-checkout');

Route::get('/init-exam', [ExamController::class, 'selectExamBody'])->name('home.init-exam');
Route::get('/select-exam-subjects/{examBodyId}', [ExamController::class, 'selectExamSubjects'])->name('home.select-subjects');
Route::post('/register-exam', [ExamController::class, 'registerExam'])->name('home.register-exam');
Route::post('/pause-exam', [ExamController::class, 'pauseExam'])->name('home.pause-exam');
Route::get('/view-exam-result/{examNo}', [ExamController::class, 'viewResult'])->name('home.view-result');
Route::get('/preview-exam-result/{examNo}', [ExamController::class, 'previewExamResult'])->name('home.preview-result');

Route::get('/exam/start/{examNo?}', [\App\Http\Controllers\Exam\ExamController::class, 'startExam'])->name('home.exams.start');
Route::get('/exam/completed/{examNo?}', [\App\Http\Controllers\Exam\ExamController::class, 'examCompleted'])->name('home.exams.completed');
Route::get('/exam/view-result-form', [\App\Http\Controllers\Exam\ExamController::class, 'viewResultForm'])->name('home.exams.view-result-form');
Route::get('/exam/view-result', [\App\Http\Controllers\Exam\ExamController::class, 'viewResult'])->name('home.exams.view-result');
*/


Route::get('/dashboard', [UserController::class, 'index'])->name('users.dashboard');

Route::group(['middleware' => ['auth', 'admin.user'], 'prefix' => 'admin/', 'as' => 'admin.'], function() {
    //Admin
    Route::get('dashboard', [AdminController::class, 'index'])->name('dashboard');
    
    Route::get('users/search', [Admin\UserController::class, 'search'])->name('users.search');
    Route::resource('users', Admin\UserController::class)
    ->except(['create']);

    Route::resource('institutions', Admin\InstitutionController::class);
    Route::get('institutions/{institution}/assign-user', [Admin\InstitutionController::class, 'assignUserView'])->name('institutions.assign-user');
    Route::post('institutions/{institution}/assign-user', [Admin\InstitutionController::class, 'assignUserStore'])->name('institutions.assign-user.store');
});

Route::get('/exam/login', [Home\ExamController::class, 'startExamView'])->name('exam-login');

Route::get('/rough/{instId?}', function (Request $request) {
    dd('rough');
});

// Route::group(['prefix' => 'admin'], function () {
//     Voyager::routes();
// });