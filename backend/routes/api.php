<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResetPassController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([

    // 'middleware' => 'auth:api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->name('me');

    Route::post('sendPassword', [ResetPassController::class, 'sendMail'])->name('sendMail');
    Route::post('resetpostPass', [ResetPassController::class, 'resetPass'])->name('resetPass');
});



Route::group(['prefix' => 'admin'], function () {
    Route::get('/', [UserController::class, 'getAllUser'])->name('getAllUser');

    Route::post('/users', [UserController::class, 'store'])->name('store');

    Route::get('/users/{id}', [UserController::class, 'show'])->name('show'); 
    Route::put('/users/{id}', [UserController::class, 'update'])->name('update');

    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('destroy');
});
