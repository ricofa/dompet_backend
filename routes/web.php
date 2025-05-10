<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\RedirectPaymentController;
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

Route::get('payment_finish', [RedirectPaymentController::class, 'finish']);

Route::group(['prefix' => 'admin'], function () {
    Route::view('login', 'login')->name('admin.login');
    Route::post('login', [AuthController::class, 'login'])->name('admin.login.login');
    Route::view('/', 'dashboard')->name('admin.dashboard');

    Route::get('transaction', [TransactionController::class, 'index'])->name('admin.transaction.index');
});
