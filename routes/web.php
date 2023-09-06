<?php

use App\Http\Controllers\MailController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [SiteController::class, 'index'])->name('home');
Route::get('/chi-sono', [SiteController::class, 'index'])->name('chi-sono');
Route::get('/cosa-aspettarsi', [SiteController::class, 'index'])->name('cosa-aspettarsi');
Route::get('/di-cosa-mi-occupo', [SiteController::class, 'index'])->name('di-cosa-mi-occupo');
Route::get('/contatti', [SiteController::class, 'index'])->name('contatti');

Route::post('/send-contact-form', [MailController::class, 'sendFromForm'])->name('send.contact-form');
