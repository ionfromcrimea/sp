<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PaymentsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/payments/new', [PaymentsController::class, 'create'])->name('payments.create')->middleware('auth');
Route::post('/payments', [PaymentsController::class, 'store'])->name('payments')->middleware('auth');
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
