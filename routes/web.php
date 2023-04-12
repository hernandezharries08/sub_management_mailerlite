<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailerLiteController;

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

Route::get('/', [MailerLiteController::class, 'showApiKeyForm'])->name('api_key_form');
Route::get('/subscribers/create', [MailerLiteController::class, 'create'])->name('subscribers.create');
Route::post('/validate-api-key', [MailerLiteController::class, 'validateAndSaveApiKey'])->name('validate_api_key');
Route::get('/subscribers', [MailerLiteController::class, 'showSubscribers'])->name('subscribers.index');
Route::post('/subscribers', [MailerLiteController::class, 'createSubscriber'])->name('subscribers.store');
Route::delete('/subscribers/{id}', [MailerLiteController::class, 'deleteSubscriber'])->name('subscribers.destroy');
Route::get('/subscribers/{id}/edit', [MailerLiteController::class, 'editSubscriberForm'])->name('subscribers.edit');
Route::put('/subscribers/{id}', [MailerLiteController::class, 'editSubscriber'])->name('subscribers.update');
Route::get('/subscribers/data', [MailerLiteController::class, 'getSubscribersData'])->name('subscribers.data');