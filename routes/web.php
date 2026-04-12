<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Home\ExamController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin as Admin;
use App\Http\Controllers\Exam as Exam;

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

Route::get('/exam/start/{examNo?}', [\App\Http\Controllers\Exam\ExamController::class, 'startExam'])->name('home.exams.start');
Route::get('/exam/completed/{examNo?}', [\App\Http\Controllers\Exam\ExamController::class, 'examCompleted'])->name('home.exams.completed');
Route::get('/exam/view-result', [\App\Http\Controllers\Exam\ExamController::class, 'viewResult'])->name('home.exams.view-result');
*/

Route::get('exams/view-result/{examNo?}', [Exam\ExamController::class, 'viewResult'])->name('exams.view-result');

Route::get('/dashboard', [UserController::class, 'index'])
    ->middleware('auth')
    ->name('users.dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/institutions/select', [UserController::class, 'selectInstitution'])->name('users.institutions.select');
    Route::post('/institutions/select', [UserController::class, 'switchInstitution'])->name('users.institutions.switch');
    Route::get('/institutions/create', [UserController::class, 'createInstitution'])->name('users.institutions.create');
    Route::post('/institutions', [UserController::class, 'storeInstitution'])->name('users.institutions.store');
});

Route::group(['middleware' => ['auth', 'admin.user'], 'prefix' => 'admin/', 'as' => 'admin.'], function() {
    //Admin
    Route::get('dashboard', [AdminController::class, 'index'])->name('dashboard');
    
    Route::get('users/search', [Admin\UserController::class, 'search'])->name('users.search');
    Route::resource('users', Admin\UserController::class)
    ->except(['create']);

    Route::get('fundings', [Admin\InstitutionController::class, 'fundingHistory'])->name('fundings.index');
    Route::resource('institutions', Admin\InstitutionController::class);
    Route::get('institutions/{institution}/assign-user', [Admin\InstitutionController::class, 'assignUserView'])->name('institutions.assign-user');
    Route::post('institutions/{institution}/assign-user', [Admin\InstitutionController::class, 'assignUserStore'])->name('institutions.assign-user.store');
    Route::get('institutions/{institution}/invoice', [Admin\InstitutionController::class, 'invoiceView'])->name('institutions.invoice');
    Route::post('institutions/{institution}/invoice', [Admin\InstitutionController::class, 'invoice'])->name('institutions.invoice.store');
    Route::get('institutions/{institution}/fund', [Admin\InstitutionController::class, 'fundView'])->name('institutions.fund');
    Route::post('institutions/{institution}/fund', [Admin\InstitutionController::class, 'fundStore'])->name('institutions.fund.store');
});

Route::get('/rough/{instId?}', function (Request $request) {
    $diff = now()->diffInSeconds(now()->addMinutes(30), false);
    dd(['diff' => $diff]);
});

// Route::group(['prefix' => 'admin'], function () {
//     Voyager::routes();
// });
