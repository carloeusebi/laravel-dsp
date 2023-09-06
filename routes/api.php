<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PatientsController;
use App\Http\Controllers\Admin\QuestionsController;
use App\Http\Controllers\Admin\SurveysController;
use App\Http\Controllers\Admin\TagsController;
use App\Http\Controllers\Admin\FilesController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\TestsController;
use Illuminate\Http\Request;
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
  Route::resource('/patients', PatientsController::class);
  Route::resource('/questions', QuestionsController::class);
  Route::resource('/surveys', SurveysController::class);
  Route::resource('/tags', TagsController::class);

  Route::get('/surveys/{id}/results', [SurveysController::class, 'calculateResults'])->name('survey.results');

  Route::prefix('/file')->controller(FilesController::class)->group(function () {
    Route::get('/{id}', 'download');
    Route::post('/', 'upload');
    Route::delete('/{id}', 'destroy');
  });

  Route::post('/email/test-link', [MailController::class, 'sendEmailWithTestLink']);
});

Route::prefix('/tests')->controller(TestsController::class)->name('tests.')->group(function () {
  Route::get('/{token}', 'show')->name('show');
  Route::put('/{id}', 'update')->name('update');
  Route::put('/patient/{id}', 'updatePatientInfo')->name('patient');
});

Route::post('email/support', [MailController::class, 'contactSupport']);
