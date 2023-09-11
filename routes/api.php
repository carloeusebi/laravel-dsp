<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\SurveyController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\FileController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::delete('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
  Route::apiResource('/patients', PatientController::class);
  Route::apiResource('/questions', QuestionController::class);
  Route::apiResource('/surveys', SurveyController::class);
  Route::apiResource('/tags', TagController::class);

  Route::get('/surveys/{id}/results', [SurveyController::class, 'calculateResults'])->name('survey.results');

  Route::prefix('/file')->controller(FileController::class)->group(function () {
    Route::get('/{id}', 'download');
    Route::post('/', 'upload');
    Route::delete('/{id}', 'destroy');
  });

  Route::post('/email/test-link', [MailController::class, 'sendEmailWithTestLink']);
});

Route::prefix('/tests')->middleware('patient')->controller(TestController::class)->name('tests.')->group(function () {
  Route::get('/{token}', 'show')->name('show');
  Route::put('/{id}', 'update')->name('update');
  Route::put('/patient/{id}', 'updatePatientInfo')->name('patient');
});

Route::post('email/support', [MailController::class, 'contactSupport']);

include __DIR__ . '/artisan-api.php';
