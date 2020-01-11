<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('details', 'UserController@details');
    Route::post('logout/{logout_type}', 'UserController@logout');

    Route::prefix('password')->group(function () {
        Route::post('reset', 'Auth\ResetPasswordController@resetPassword');
    });

    Route::prefix('email')->group(function () {
        Route::post('reset', 'UserController@resetEmail');
    });
});

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('login')->group(function () {
    Route::post('/', 'UserController@login');
    Route::get('{login_type}/redirect', 'UserController@redirect');
    Route::get('{login_type}/callback', 'UserController@callback');
});

Route::post('register', 'UserController@register');

Route::prefix('email')->group(function () {
    Route::post('verify', 'Auth\VerificationController@verifyUser');
});

Route::prefix('password')->group(function () {
    Route::post('forget/email/send', 'Auth\ForgotPasswordController@forgotPasswordFromEmail');
    Route::post('forget/verify', 'Auth\ResetPasswordController@verifyToken');
    Route::post('forget/reset', 'Auth\ResetPasswordController@forgotPasswordFromEmail');
});

Route::prefix('test')->group(function () {
    // Route::get('test', 'TestController@testSignature');
});


//Forget Password/Verify Email/Send Verification Email
//Password verify
//Send email(register, reset password, reset email)
